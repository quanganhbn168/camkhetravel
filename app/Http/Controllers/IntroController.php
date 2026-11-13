<?php

namespace App\Http\Controllers;

use App\Support\Content\ArticleOutline;
use App\Models\Intro;
use App\Support\Seo\FrontendSeoBuilder;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\View\View;

class IntroController extends Controller
{
    public function __invoke(string $slug): View
    {
        $intro = Intro::query()
            ->with(['curatorMedia', 'slugs'])
            ->published()
            ->whereHas('slugs', fn ($slugs) => $slugs->where('slug', $slug))
            ->firstOrFail();

        $content = RichContentRenderer::make($intro->content ?? '')->toHtml();

        return view('intros.show', [
            'intro' => $intro,
            'seo' => app(FrontendSeoBuilder::class)->listing($intro->seo_title ?: $intro->title, $intro->seo_description ?: $intro->summary ?: '', $intro->url, image: $intro->seoImageUrl($intro->image_url)),
            'content' => ArticleOutline::prepare((string) $content)['html'],
            'seoTitle' => $intro->seo_title ?: $intro->title,
            'seoDescription' => $intro->seo_description ?: $intro->summary,
            'seoImage' => $intro->seoImageUrl($intro->image_url),
            'seoUrl' => $intro->url,
        ]);
    }
}
