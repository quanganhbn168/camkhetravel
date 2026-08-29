<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Landing;
use App\Support\Frontend\MediaUrl;
use App\Support\Localization\LocalizedUrl;
use App\Support\Seo\FrontendSeoBuilder;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function __construct(
        private readonly FrontendSeoBuilder $seo,
    ) {}

    public function index(Request $request): View
    {
        $backstageLanding = $this->backstageLanding($request);

        return view('frontend.projects.index', $this->listingData(backstageLanding: $backstageLanding, request: $request) + [
            'seo' => $this->seo->listing(
                'Dự án | '.$this->seo->siteName(),
                'Các case study và dự án truyền thông nổi bật.',
                LocalizedUrl::route('projects.index'),
            ),
        ]);
    }

    public function category(ProjectCategory $category, ?Request $request = null): View
    {
        abort_unless($category->is_active, 404);

        $title = $category->name.' | Dự án';
        $description = $category->description ?: 'Các dự án thuộc nhóm '.$category->name.'.';

        $request ??= request();

        return view('frontend.projects.index', $this->listingData($category, $this->backstageLanding($request), $request) + [
            'seo' => $this->seo->listing($title, $description, LocalizedUrl::projectCategory($category)),
        ]);
    }

    public function show(Project $project): View
    {
        abort_unless($project->status === 'published' && (! $project->published_at || $project->published_at->isPast()), 404);

        $project->load([
            'category',
            'curatorMedia',
            'approvedComments' => fn ($query) => $query->latest('approved_at')->latest('id'),
            'backstageLandings' => fn ($query) => $query
                ->published()
                ->with(['category', 'curatorMedia'])
                ->orderByDesc('published_at'),
            'relatedPosts' => fn ($query) => $query
                ->published()
                ->with(['categories', 'curatorMedia'])
                ->orderByDesc('published_at'),
        ]);
        $project->setAttribute('image_url', MediaUrl::resolve($project->curatorMedia));
        $project->setAttribute('body_html', (string) $project->body);
        $relatedProjects = $this->withImages(Project::query()
            ->published()
            ->whereKeyNot($project->id)
            ->when($project->project_category_id, fn ($query) => $query->where('project_category_id', $project->project_category_id))
            ->with(['category', 'curatorMedia'])
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->limit(3)
            ->get());
        $faqItems = collect($project->faq_items ?? [])
            ->map(fn (mixed $item): array => [
                'question' => trim((string) (is_array($item) ? ($item['question'] ?? '') : '')),
                'answer' => trim((string) (is_array($item) ? ($item['answer'] ?? '') : '')),
            ])
            ->filter(fn (array $item): bool => $item['question'] !== '' && $item['answer'] !== '')
            ->values();
        $ratedComments = $project->approvedComments
            ->filter(fn ($comment): bool => $comment->rating !== null)
            ->values();

        return view('frontend.projects.show', compact('project') + [
            'relatedProjects' => $relatedProjects,
            'relatedServices' => $this->withImages($project->backstageLandings),
            'relatedPosts' => $this->withImages($project->relatedPosts),
            'galleryImages' => $this->galleryImages($project->gallery),
            'projectVideoUrl' => filter_var($project->video_url, FILTER_VALIDATE_URL) ? $project->video_url : null,
            'faqItems' => $faqItems,
            'ratingSummary' => [
                'count' => $ratedComments->count(),
                'average' => $ratedComments->isNotEmpty() ? round((float) $ratedComments->avg('rating'), 1) : null,
            ],
            'seo' => $this->seo->project($project),
        ]);
    }

    public function showBySlug(string $slug): View
    {
        return $this->show($this->projectForSlug($slug));
    }

    public function categoryBySlug(string $slug): View
    {
        return $this->category($this->categoryForSlug($slug));
    }

    /** @return array<string, mixed> */
    private function listingData(
        ?ProjectCategory $activeCategory = null,
        ?Landing $backstageLanding = null,
        ?Request $request = null,
    ): array
    {
        $request ??= request();
        $sort = $request->string('sort')->value();
        $sort = in_array($sort, ['latest', 'featured', 'title'], true) ? $sort : 'latest';

        $projectsQuery = Project::query()
            ->published()
            ->with(['category', 'curatorMedia']);

        if ($activeCategory) {
            $projectsQuery->where('project_category_id', $activeCategory->id);
        }

        if ($backstageLanding) {
            $projectsQuery->whereHas('backstageLandings', fn ($query) => $query->whereKey($backstageLanding->id));
        }

        $this->applyOrdering($projectsQuery, $sort);

        $projects = $this->withImages($projectsQuery
            ->paginate(12)
            ->withQueryString());

        $heroProject = Project::query()
            ->published()
            ->when($activeCategory, fn (Builder $query) => $query->where('project_category_id', $activeCategory->id))
            ->when($backstageLanding, fn (Builder $query) => $query->whereHas('backstageLandings', fn ($landingQuery) => $landingQuery->whereKey($backstageLanding->id)))
            ->with(['curatorMedia'])
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->first();

        return [
            'activeCategory' => $activeCategory,
            'backstageLanding' => $backstageLanding,
            'categories' => $this->categories(),
            'projects' => $projects,
            'heroImageUrl' => $heroProject ? MediaUrl::resolve($heroProject->curatorMedia) : null,
            'pageTitle' => $activeCategory?->name ?? ($backstageLanding ? 'Hậu trường: '.$backstageLanding->title : 'Dự án'),
            'pageDescription' => $activeCategory?->description ?: ($backstageLanding
                ? 'Các dự án và tư liệu hậu trường được gắn với landing '.$backstageLanding->title.'.'
                : 'Những dự án THT Media đã đồng hành từ định hướng ban đầu đến sản phẩm truyền thông hoàn chỉnh.'),
            'sort' => $sort,
            'sortOptions' => [
                'latest' => 'Mới nhất',
                'featured' => 'Nổi bật',
                'title' => 'Tên A–Z',
            ],
        ];
    }

    private function applyOrdering(Builder $query, string $sort): void
    {
        match ($sort) {
            'featured' => $query->orderByDesc('is_featured')->orderByDesc('published_at'),
            'title' => $query->orderBy('title'),
            default => $query->orderByDesc('published_at')->orderByDesc('id'),
        };
    }

    /** @return list<string> */
    private function galleryImages(?array $gallery): array
    {
        $ids = collect($gallery ?? [])
            ->map(fn (mixed $item): mixed => is_array($item) ? ($item['id'] ?? $item['media_id'] ?? null) : $item)
            ->filter(fn (mixed $id): bool => is_numeric($id))
            ->map(fn (mixed $id): int => (int) $id)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return [];
        }

        $media = Media::query()->whereKey($ids->all())->get()->keyBy('id');

        return $ids
            ->map(fn (int $id): ?string => $media->get($id)?->url)
            ->filter()
            ->values()
            ->all();
    }

    private function withImages(iterable $projects): iterable
    {
        foreach ($projects as $project) {
            $project->setAttribute('image_url', MediaUrl::resolve($project->curatorMedia));
        }

        return $projects;
    }

    private function backstageLanding(Request $request): ?Landing
    {
        $landingId = $request->integer('landing') ?: $request->integer('service');

        return $landingId
            ? Landing::query()->published()->find($landingId)
            : null;
    }

    private function projectForSlug(string $slug): Project
    {
        return Project::query()
            ->whereHas('slugs', fn (Builder $slugs) => $slugs->where('slug', $slug))
            ->firstOrFail();
    }

    private function categoryForSlug(string $slug): ProjectCategory
    {
        return ProjectCategory::query()
            ->whereHas('slugs', fn (Builder $slugs) => $slugs->where('slug', $slug))
            ->firstOrFail();
    }

    private function categories()
    {
        return ProjectCategory::query()
            ->where('is_active', true)
            ->withCount(['projects' => fn (Builder $query) => $query->published()])
            ->orderBy('sort_order')
            ->get()
            ->each(fn (ProjectCategory $category) => $category->setAttribute('public_url', LocalizedUrl::projectCategory($category)));
    }
}
