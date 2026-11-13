<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostCategory;
use App\Support\Categories\CategoryTree;
use App\Support\Content\ArticleOutline;
use App\Support\Media\MediaUrl;
use App\Support\Seo\FrontendSeoBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PostController extends Controller
{
    private const SORT_OPTIONS = [
        'latest' => 'Mới nhất',
        'oldest' => 'Cũ nhất',
        'title_asc' => 'Tên A–Z',
        'title_desc' => 'Tên Z–A',
    ];

    public function __construct(
        private readonly FrontendSeoBuilder $seo,
    ) {}

    public function index(): View
    {
        $sort = $this->selectedSort();
        $posts = $this->withImages($this->sortPosts(Post::query()
            ->published()
            ->with(['category', 'curatorMedia', 'slugs']), $sort)
            ->paginate(12)
            ->withQueryString());
        $canonicalUrl = route('posts.index');

        if ($posts->currentPage() > 1) {
            $canonicalUrl .= '?page='.$posts->currentPage();
        }

        return view('frontend.posts.index', [
            'categories' => $this->categories(),
            'posts' => $posts,
            'activeCategory' => null,
            'listingUrl' => route('posts.index'),
            'sort' => $sort,
            'sortOptions' => self::SORT_OPTIONS,
            'heroImageUrl' => $posts->first()?->image_url,
            'seo' => $this->listingSeo(
                'Tin tức | '.$this->seo->siteName(),
                'Tin tức và thông tin hữu ích cho những hành trình cùng CamKheTravel.',
                $canonicalUrl,
            ),
        ]);
    }

    public function category(PostCategory $category): View
    {
        abort_unless($category->is_active, 404);
        $category->loadMissing('slugs');

        $title = $category->seo_title ?: $category->name.' | Tin tức';
        $description = $category->seo_description ?: $category->description ?: 'Các bài viết thuộc chuyên mục '.$category->name.'.';
        $sort = $this->selectedSort();
        $posts = $this->withImages($this->sortPosts(Post::query()->whereIn('post_category_id', $category->subtreeIds(activeOnly: true))
            ->published()
            ->with(['category', 'curatorMedia', 'slugs']), $sort)
            ->paginate(12)
            ->withQueryString());
        $canonicalUrl = route('posts.category', ['slug' => $category->slug]);

        if ($posts->currentPage() > 1) {
            $canonicalUrl .= '?page='.$posts->currentPage();
        }

        return view('frontend.posts.index', [
            'categories' => $this->categories(),
            'posts' => $posts,
            'activeCategory' => $category,
            'pageBannerUrl' => $category->banner_url,
            'categoryImageUrl' => $category->image_url,
            'categoryBodyHtml' => (string) str((string) $category->body)->sanitizeHtml(),
            'listingUrl' => route('posts.category', ['slug' => $category->slug]),
            'sort' => $sort,
            'sortOptions' => self::SORT_OPTIONS,
            'heroImageUrl' => $posts->first()?->image_url,
            'seo' => $this->seo->listing($title, $description, $canonicalUrl, image: $category->seoImageUrl(), trail: [['name' => 'Tin tức', 'url' => route('posts.index')], ['name' => $category->name, 'url' => route('posts.category', ['slug' => $category->slug])]]),
        ]);
    }

    public function show(Post $post): View
    {
        abort_unless($post->status === 'published' && (! $post->published_at || $post->published_at->isPast()), 404);

        $post->load([
            'category',
            'curatorMedia',
            'slugs',
            'approvedComments' => fn ($query) => $query->latest('approved_at')->latest('id'),
        ]);
        $post->setAttribute('image_url', MediaUrl::resolve($post->curatorMedia));
        $article = ArticleOutline::prepare(
            (string) $post->body,
        );
        $post->setAttribute('body_html', $article['html']);
        $featuredPosts = $this->withImages(Post::query()
            ->published()
            ->whereKeyNot($post->id)
            ->with(['category', 'curatorMedia', 'slugs'])
            ->orderByDesc('is_featured')
            ->latest('published_at')
            ->limit(4)
            ->get());
        [$previousPost, $nextPost] = $this->adjacentPosts($post);
        $shareUrl = route('slug.show', ['slug' => $post->slug]);

        return view('frontend.posts.show', compact('post') + [
            'categories' => $this->categories(),
            'activeCategory' => $post->category,
            'heroImageUrl' => $post->image_url,
            'featuredPosts' => $featuredPosts,
            'previousPost' => $previousPost,
            'nextPost' => $nextPost,
            'tableOfContents' => $article['tableOfContents'],
            'share' => [
                'url' => $shareUrl,
                'facebook' => 'https://www.facebook.com/sharer/sharer.php?u='.rawurlencode($shareUrl),
                'linkedin' => 'https://www.linkedin.com/sharing/share-offsite/?url='.rawurlencode($shareUrl),
                'x' => 'https://twitter.com/intent/tweet?url='.rawurlencode($shareUrl).'&text='.rawurlencode($post->title),
            ],
            'seo' => $this->seo->post($post),
        ]);
    }

    public function showBySlug(string $slug): RedirectResponse
    {
        return redirect()->route('slug.show', ['slug' => $this->postForSlug($slug)->slug], 301);
    }

    public function categoryBySlug(string $slug): View
    {
        return $this->category($this->categoryForSlug($slug));
    }

    private function withImages(iterable $posts): iterable
    {
        foreach ($posts as $post) {
            $post->setAttribute('image_url', MediaUrl::resolve($post->curatorMedia));
        }

        return $posts;
    }

    private function postForSlug(string $slug): Post
    {
        return Post::query()
            ->whereHas('slugs', fn ($slugs) => $slugs->where('slug', $slug))
            ->with('slugs')
            ->firstOrFail();
    }

    private function categoryForSlug(string $slug): PostCategory
    {
        return PostCategory::query()
            ->whereHas('slugs', fn ($slugs) => $slugs->where('slug', $slug))
            ->with('slugs')
            ->firstOrFail();
    }

    private function categories()
    {
        return CategoryTree::forDisplay(PostCategory::query()
            ->where('is_active', true)
            ->withCount(['posts' => fn ($query) => $query->published()])
            ->with('slugs')
            ->orderBy('sort_order')
            ->get()
            ->each(fn (PostCategory $category) => $category->setAttribute('public_url', route('posts.category', ['slug' => $category->slug]))), 'posts_count');
    }

    private function adjacentPosts(Post $post): array
    {
        if (! $post->published_at) {
            return [null, null];
        }

        $withMedia = ['curatorMedia', 'slugs'];
        $previousPost = Post::query()
            ->published()
            ->where(function ($query) use ($post): void {
                $query->where('published_at', '<', $post->published_at)
                    ->orWhere(function ($query) use ($post): void {
                        $query->where('published_at', $post->published_at)
                            ->where('id', '<', $post->id);
                    });
            })
            ->with($withMedia)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->first();
        $nextPost = Post::query()
            ->published()
            ->where(function ($query) use ($post): void {
                $query->where('published_at', '>', $post->published_at)
                    ->orWhere(function ($query) use ($post): void {
                        $query->where('published_at', $post->published_at)
                            ->where('id', '>', $post->id);
                    });
            })
            ->with($withMedia)
            ->orderBy('published_at')
            ->orderBy('id')
            ->first();

        foreach ([$previousPost, $nextPost] as $adjacentPost) {
            if ($adjacentPost) {
                $adjacentPost->setAttribute('image_url', MediaUrl::resolve($adjacentPost->curatorMedia));
            }
        }

        return [$previousPost, $nextPost];
    }

    private function selectedSort(): string
    {
        $sort = request()->query('sort');

        return is_string($sort) && array_key_exists($sort, self::SORT_OPTIONS)
            ? $sort
            : 'latest';
    }

    private function sortPosts($query, string $sort)
    {
        return match ($sort) {
            'oldest' => $query->orderByRaw('published_at is null')->orderBy('published_at')->orderBy('id'),
            'title_asc' => $query->orderBy('title')->orderByDesc('published_at'),
            'title_desc' => $query->orderByDesc('title')->orderByDesc('published_at'),
            default => $query->orderByDesc('published_at')->orderByDesc('id'),
        };
    }

    private function listingSeo(string $title, string $description, string $canonicalUrl): array
    {
        return $this->seo->listing($title, $description, $canonicalUrl, trail: [['name' => 'Tin tức', 'url' => route('posts.index')]]);
    }
}
