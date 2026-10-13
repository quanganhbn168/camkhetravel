<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Support\Seo\FrontendSeoBuilder;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\View\View;

class PageController extends Controller
{
    /** Trang tĩnh tại /{slug}; tin nháp hoặc chưa tới ngày xuất bản thì 404. */
    public function show(Page $page): View
    {
        $page = Page::query()->published()->with(['curatorMedia', 'bannerMedia', 'slugs'])->findOrFail($page->getKey());

        $content = blank($page->content)
            ? ''
            : (string) str(RichContentRenderer::make($page->content)->toHtml())->sanitizeHtml();

        return view('frontend.pages.show', [
            'page' => $page,
            'content' => $content,
            'pageBannerUrl' => $page->banner_url,
            'seo' => app(FrontendSeoBuilder::class)->listing(
                $page->seo_title ?: $page->title,
                $page->seo_description ?: (string) $page->excerpt,
                $page->url,
                image: $page->seoImageUrl($page->image_url),
            ),
        ]);
    }
}
