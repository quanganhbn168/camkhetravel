<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Support\Categories\CategoryTree;
use App\Support\Media\MediaUrl;
use App\Support\Pages\SystemPageProfileResolver;
use App\Support\Seo\FrontendSeoBuilder;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function __construct(
        private readonly FrontendSeoBuilder $seo,
        private readonly SystemPageProfileResolver $systemPages,
    ) {}

    public function index(Request $request): View
    {
        $page = $this->systemPages->require('projects');
        $data = $this->listingData(request: $request);
        $data['page'] = $page;
        $data['pageTitle'] = $page['title'];
        $data['pageBannerUrl'] = $page['banner_url'];
        $data['seo'] = $this->seo->systemPage($page, 'projects.index');

        return view('frontend.projects.index', $data);
    }

    public function category(ProjectCategory $category, ?Request $request = null): View
    {
        abort_unless($category->is_active, 404);
        $category->loadMissing('slugs');

        $title = $category->seo_title ?: $category->name.' | Dự án';
        $description = $category->seo_description ?: $category->description ?: 'Các dự án thuộc nhóm '.$category->name.'.';

        $request ??= request();

        return view('frontend.projects.index', $this->listingData($category, request: $request) + [
            'seo' => $this->seo->listing($title, $description, route('projects.category', ['slug' => $category->slug]), image: $category->seoImageUrl()),
        ]);
    }

    public function show(Project $project): View
    {
        abort_unless($project->status === 'published' && (! $project->published_at || $project->published_at->isPast()), 404);

        $project->load([
            'category.slugs',
            'curatorMedia',
            'slugs',
            'faqs' => fn ($query) => $query->active()->ordered(),
            'approvedComments' => fn ($query) => $query->latest('approved_at')->latest('id'),
        ]);
        $project->setAttribute('image_url', MediaUrl::resolve($project->curatorMedia));
        $project->setAttribute('body_html', (string) $project->body);
        $relatedProjects = $this->withImages(Project::query()
            ->published()
            ->whereKeyNot($project->id)
            ->when($project->project_category_id, fn ($query) => $query->where('project_category_id', $project->project_category_id))
            ->with(['category', 'curatorMedia', 'slugs'])
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->limit(3)
            ->get());
        $faqItems = $project->faqs
            ->map(fn ($faq): array => [
                'question' => $faq->question,
                'answer' => $faq->answer,
            ])
            ->values();
        $ratedComments = $project->approvedComments
            ->filter(fn ($comment): bool => $comment->rating !== null)
            ->values();

        return view('frontend.projects.show', compact('project') + [
            'relatedProjects' => $relatedProjects,
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
        ?Request $request = null,
    ): array {
        $request ??= request();
        $sort = $request->string('sort')->value();
        $sort = in_array($sort, ['latest', 'featured', 'title'], true) ? $sort : 'latest';

        $projectsQuery = Project::query()
            ->published()
            ->with(['category', 'curatorMedia', 'slugs']);

        if ($activeCategory) {
            $projectsQuery->whereIn('project_category_id', $activeCategory->subtreeIds(activeOnly: true));
        }

        $this->applyOrdering($projectsQuery, $sort);

        $projects = $this->withImages($projectsQuery
            ->paginate(12)
            ->withQueryString());

        $heroProject = Project::query()
            ->published()
            ->when($activeCategory, fn (Builder $query) => $query->where('project_category_id', $activeCategory->id))
            ->with(['curatorMedia'])
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->first();

        return [
            'activeCategory' => $activeCategory,
            'pageBannerUrl' => $activeCategory?->banner_url,
            'categoryImageUrl' => $activeCategory?->image_url,
            'categoryBodyHtml' => (string) str((string) $activeCategory?->body)->sanitizeHtml(),
            'categories' => $this->categories(),
            'projects' => $projects,
            'heroImageUrl' => $heroProject ? MediaUrl::resolve($heroProject->curatorMedia) : null,
            'archiveStats' => [
                ['value' => (string) Project::query()->published()->count(), 'label' => 'công trình đã triển khai'],
                ['value' => (string) ProjectCategory::query()->where('is_active', true)->count(), 'label' => 'nhóm công trình'],
                ['value' => '100%', 'label' => 'quy trình minh bạch'],
                ['value' => '24/7', 'label' => 'đồng hành kỹ thuật'],
            ],
            'marqueePartners' => Partner::query()
                ->active()
                ->with('curatorMedia')
                ->orderBy('sort_order')
                ->get(),
            'pageTitle' => $activeCategory?->name ?? 'Dự án',
            'pageDescription' => $activeCategory?->description ?: 'Những công trình đã được triển khai.',
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

    private function projectForSlug(string $slug): Project
    {
        return Project::query()
            ->whereHas('slugs', fn (Builder $slugs) => $slugs->where('slug', $slug))
            ->with('slugs')
            ->firstOrFail();
    }

    private function categoryForSlug(string $slug): ProjectCategory
    {
        return ProjectCategory::query()
            ->whereHas('slugs', fn (Builder $slugs) => $slugs->where('slug', $slug))
            ->with('slugs')
            ->firstOrFail();
    }

    private function categories()
    {
        return CategoryTree::forDisplay(ProjectCategory::query()
            ->where('is_active', true)
            ->withCount(['projects' => fn (Builder $query) => $query->published()])
            ->with('slugs')
            ->orderBy('sort_order')
            ->get()
            ->each(fn (ProjectCategory $category) => $category->setAttribute('public_url', route('projects.category', ['slug' => $category->slug]))), 'projects_count');
    }
}
