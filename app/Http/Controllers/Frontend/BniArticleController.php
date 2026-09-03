<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BniArticle;
use App\Support\Localization\LocalizedUrl;
use App\Support\Media\MediaUrl;
use App\Support\Seo\FrontendSeoBuilder;
use Illuminate\View\View;

class BniArticleController extends Controller
{
    public function __construct(private readonly FrontendSeoBuilder $seo) {}

    public function show(BniArticle $article): View
    {
        abort_unless($article->status === 'published' && (! $article->published_at || $article->published_at->isPast()), 404);

        $article->load([
            'chapter',
            'categories',
            'coverMedia',
            'approvedComments' => fn ($query) => $query->with('user')->latest('approved_at'),
            'reactions',
        ]);

        return view('frontend.bni-article', [
            'article' => $article,
            'imageUrl' => MediaUrl::versioned($article->coverMedia),
            'reactionCounts' => $article->reactions->countBy('reaction'),
            'seo' => $this->seo->listing(
                $article->title.' | BNI',
                $article->excerpt ?: $article->title,
                LocalizedUrl::route('bni.articles.show', ['article' => $article]),
            ),
        ]);
    }
}
