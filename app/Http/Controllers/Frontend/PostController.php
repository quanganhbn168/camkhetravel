<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostCategory;
use App\Services\WordPress\WordPressMediaUrlMapper;
use App\Support\Frontend\MediaUrl;
use App\Support\Localization\LocalizedUrl;
use App\Support\Seo\FrontendSeoBuilder;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Str;
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
        private readonly WordPressMediaUrlMapper $mediaUrlMapper,
        private readonly FrontendSeoBuilder $seo,
    ) {}

    public function index(): View
    {
        $sort = $this->selectedSort();
        $posts = $this->withImages($this->sortPosts(Post::query()
            ->published()
            ->with(['categories', 'curatorMedia', 'legacyMedia']), $sort)
            ->paginate(12)
            ->withQueryString());
        $canonicalUrl = LocalizedUrl::route('posts.index');

        if ($posts->currentPage() > 1) {
            $canonicalUrl .= '?page='.$posts->currentPage();
        }

        return view('frontend.posts.index', [
            'categories' => $this->categories(),
            'posts' => $posts,
            'activeCategory' => null,
            'listingUrl' => LocalizedUrl::route('posts.index'),
            'sort' => $sort,
            'sortOptions' => self::SORT_OPTIONS,
            'heroImageUrl' => $posts->first()?->image_url,
            'seo' => $this->listingSeo(
                'Tin tức | '.$this->seo->siteName(),
                'Tin tức, kiến thức và góc nhìn truyền thông.',
                $canonicalUrl,
            ),
        ]);
    }

    public function category(PostCategory $category): View
    {
        abort_unless($category->is_active, 404);

        $title = $category->name.' | Tin tức';
        $description = $category->description ?: 'Các bài viết thuộc chuyên mục '.$category->name.'.';
        $sort = $this->selectedSort();
        $posts = $this->withImages($this->sortPosts($category->posts()
            ->published()
            ->with(['categories', 'curatorMedia', 'legacyMedia']), $sort)
            ->paginate(12)
            ->withQueryString());
        $canonicalUrl = LocalizedUrl::postCategory($category);

        if ($posts->currentPage() > 1) {
            $canonicalUrl .= '?page='.$posts->currentPage();
        }

        return view('frontend.posts.index', [
            'categories' => $this->categories(),
            'posts' => $posts,
            'activeCategory' => $category,
            'listingUrl' => LocalizedUrl::postCategory($category),
            'sort' => $sort,
            'sortOptions' => self::SORT_OPTIONS,
            'heroImageUrl' => $posts->first()?->image_url,
            'seo' => $this->listingSeo($title, $description, $canonicalUrl),
        ]);
    }

    public function show(Post $post): View
    {
        abort_unless($post->status === 'published' && (! $post->published_at || $post->published_at->isPast()), 404);

        $post->load([
            'categories',
            'curatorMedia',
            'legacyMedia',
            'approvedComments' => fn ($query) => $query->latest('approved_at')->latest('id'),
        ]);
        $post->setAttribute('image_url', MediaUrl::resolve($post->curatorMedia, $post->legacyMedia));
        $article = $this->prepareArticleBody(
            $this->mediaUrlMapper->absoluteLocalMediaUrls((string) $post->body),
        );
        $post->setAttribute('body_html', $article['html']);
        $featuredPosts = $this->withImages(Post::query()
            ->published()
            ->whereKeyNot($post->id)
            ->with(['categories', 'curatorMedia', 'legacyMedia'])
            ->orderByDesc('is_featured')
            ->latest('published_at')
            ->limit(4)
            ->get());
        [$previousPost, $nextPost] = $this->adjacentPosts($post);
        $shareUrl = LocalizedUrl::post($post);

        return view('frontend.posts.show', compact('post') + [
            'categories' => $this->categories(),
            'activeCategory' => $post->categories->first(),
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

    public function showBySlug(string $slug): View
    {
        return $this->show($this->postForSlug($slug));
    }

    public function categoryBySlug(string $slug): View
    {
        return $this->category($this->categoryForSlug($slug));
    }

    /**
     * Adds stable anchors to article headings and exposes them for the public table of contents.
     *
     * @return array{html: string, tableOfContents: list<array{id: string, title: string, level: int}>}
     */
    private function prepareArticleBody(string $html): array
    {
        if (blank($html)) {
            return ['html' => $html, 'tableOfContents' => []];
        }

        $previousErrors = libxml_use_internal_errors(true);

        try {
            $document = new DOMDocument('1.0', 'UTF-8');
            $document->loadHTML(
                '<?xml encoding="UTF-8"><!DOCTYPE html><html><body><div id="article-body-root">'.$html.'</div></body></html>',
                LIBXML_HTML_NODEFDTD | LIBXML_NONET,
            );

            $root = (new DOMXPath($document))->query('//*[@id="article-body-root"]')->item(0);

            if (! $root instanceof DOMElement) {
                return ['html' => $html, 'tableOfContents' => []];
            }

            $usedIds = [];
            $tableOfContents = [];
            $headings = (new DOMXPath($document))->query('.//*[self::h2 or self::h3]', $root);

            foreach ($headings ?: [] as $heading) {
                if (! $heading instanceof DOMElement) {
                    continue;
                }

                $title = trim((string) preg_replace('/\s+/u', ' ', $heading->textContent));

                if ($title === '') {
                    continue;
                }

                $id = $heading->getAttribute('id');

                if (! preg_match('/^[A-Za-z][A-Za-z0-9_:.\\-]*$/', $id)) {
                    $id = Str::slug($title) ?: 'muc-noi-dung';
                }

                $baseId = $id;
                $suffix = 2;

                while (isset($usedIds[$id])) {
                    $id = $baseId.'-'.$suffix++;
                }

                $usedIds[$id] = true;
                $heading->setAttribute('id', $id);
                $tableOfContents[] = [
                    'id' => $id,
                    'title' => $title,
                    'level' => (int) substr($heading->tagName, 1),
                ];
            }

            foreach ($this->articleTables($document, $root) as $table) {
                $wrapper = $document->createElement('div');
                $wrapper->setAttribute('class', 'article-table-wrap');
                $table->parentNode?->replaceChild($wrapper, $table);
                $wrapper->appendChild($table);
            }

            $bodyHtml = '';

            foreach ($root->childNodes as $child) {
                $bodyHtml .= $document->saveHTML($child);
            }

            return ['html' => $bodyHtml, 'tableOfContents' => $tableOfContents];
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previousErrors);
        }
    }

    /** @return list<DOMElement> */
    private function articleTables(DOMDocument $document, DOMElement $root): array
    {
        $tables = (new DOMXPath($document))->query('.//table', $root);

        if ($tables === false) {
            return [];
        }

        return array_values(array_filter(
            iterator_to_array($tables),
            fn ($table): bool => $table instanceof DOMElement
                && ! ($table->parentNode instanceof DOMElement
                    && str_contains(' '.$table->parentNode->getAttribute('class').' ', ' article-table-wrap ')),
        ));
    }

    private function withImages(iterable $posts): iterable
    {
        foreach ($posts as $post) {
            $post->setAttribute('image_url', MediaUrl::resolve($post->curatorMedia, $post->legacyMedia));
        }

        return $posts;
    }

    private function postForSlug(string $slug): Post
    {
        return Post::query()
            ->where(function ($query) use ($slug): void {
                $query->whereHas('legacyContent', fn ($legacy) => $legacy->where('slug', $slug))
                    ->orWhereHas('slugs', fn ($slugs) => $slugs->where('slug', $slug));
            })
            ->firstOrFail();
    }

    private function categoryForSlug(string $slug): PostCategory
    {
        return PostCategory::query()
            ->where(function ($query) use ($slug): void {
                $query->whereHas('legacyTerm', fn ($term) => $term->where('slug', $slug))
                    ->orWhereHas('slugs', fn ($slugs) => $slugs->where('slug', $slug));
            })
            ->firstOrFail();
    }

    private function categories()
    {
        return PostCategory::query()
            ->where('is_active', true)
            ->withCount(['posts' => fn ($query) => $query->published()])
            ->orderBy('sort_order')
            ->get()
            ->each(fn (PostCategory $category) => $category->setAttribute('public_url', LocalizedUrl::postCategory($category)));
    }

    private function adjacentPosts(Post $post): array
    {
        if (! $post->published_at) {
            return [null, null];
        }

        $withMedia = ['curatorMedia', 'legacyMedia'];
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
                $adjacentPost->setAttribute('image_url', MediaUrl::resolve($adjacentPost->curatorMedia, $adjacentPost->legacyMedia));
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
        $seo = $this->seo->listing($title, $description, $canonicalUrl);

        if (request()->has('sort')) {
            $seo['robots'] = 'noindex, follow';
        }

        return $seo;
    }
}
