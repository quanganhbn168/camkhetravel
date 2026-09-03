<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BniArticle;
use App\Models\BniArticleCategory;
use App\Models\BniEvent;
use App\Support\Localization\LocalizedUrl;
use App\Support\Seo\FrontendSeoBuilder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BniArticleController extends Controller
{
    public function __construct(private readonly FrontendSeoBuilder $seo) {}

    public function index(Request $request): View
    {
        $event = BniEvent::query()
            ->published()
            ->where('type', 'handover')
            ->orderByDesc('is_featured')
            ->orderByDesc('starts_at')
            ->first();
        $selectedCategory = trim((string) $request->query('danh-muc'));
        $articleScope = fn ($query) => $query
            ->published()
            ->whereIn('type', ['event', 'chapter'])
            ->when($event, fn ($query) => $query->where(fn ($query) => $query
                ->where('bni_event_id', $event->id)
                ->orWhereNull('bni_event_id')));

        $categories = BniArticleCategory::query()
            ->where('is_active', true)
            ->whereHas('articles', $articleScope)
            ->withCount(['articles as published_articles_count' => $articleScope])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($selectedCategory !== '' && ! $categories->contains('slug', $selectedCategory)) {
            abort(404);
        }

        $articles = BniArticle::query()
            ->published()
            ->with(['chapter', 'categories', 'media'])
            ->whereIn('type', ['event', 'chapter'])
            ->when($event, fn ($query) => $query->where(fn ($query) => $query
                ->where('bni_event_id', $event->id)
                ->orWhereNull('bni_event_id')))
            ->when($selectedCategory !== '', fn ($query) => $query->whereHas('categories', fn ($query) => $query->where('slug', $selectedCategory)))
            ->orderByDesc('is_featured')
            ->latest('published_at')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('frontend.bni-articles-index', [
            'event' => $event,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'articles' => $articles,
            'seo' => $this->seo->listing(
                'Tin tức Lễ chuyển giao BNI',
                'Tin tức, hoạt động và cập nhật mới nhất từ Lễ chuyển giao BNI.',
                LocalizedUrl::route('bni.articles.index'),
            ),
        ]);
    }

    public function show(BniArticle $article): View
    {
        abort_unless($article->status === 'published' && (! $article->published_at || $article->published_at->isPast()), 404);

        $article->load([
            'chapter',
            'categories',
            'media',
            'approvedComments' => fn ($query) => $query->with('user')->latest('approved_at'),
            'reactions',
        ]);

        return view('frontend.bni-article', [
            'article' => $article,
            'imageUrl' => $article->bniMediaUrl('cover'),
            'reactionCounts' => $article->reactions->countBy('reaction'),
            'seo' => $this->seo->listing(
                $article->title.' | BNI',
                $article->excerpt ?: $article->title,
                LocalizedUrl::route('bni.articles.show', ['article' => $article]),
            ),
        ]);
    }
}
