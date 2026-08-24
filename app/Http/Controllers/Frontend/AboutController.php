<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ContentItem;
use App\Models\MediaAsset;
use App\Services\WordPress\WordPressMediaUrlMapper;
use App\Support\Frontend\MediaUrl;
use App\Support\Localization\LocalizedUrl;
use App\Support\Seo\FrontendSeoBuilder;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function __construct(
        private readonly WordPressMediaUrlMapper $mediaUrlMapper,
        private readonly FrontendSeoBuilder $seo,
    ) {}

    public function __invoke(): View
    {
        $page = ContentItem::query()
            ->where('source', 'wordpress')
            ->where('type', 'page')
            ->where('status', 'published')
            ->where('slug', 've-chung-toi')
            ->firstOrFail();

        $legacyMedia = MediaAsset::query()
            ->where('source', 'wordpress')
            ->where('source_id', $page->featured_media_source_id)
            ->first();

        $page->setAttribute('image_url', MediaUrl::resolve(null, $legacyMedia));
        $page->setAttribute('body_html', $this->mediaUrlMapper->absoluteLocalMediaUrls((string) $page->body));

        return view('frontend.about', compact('page') + [
            'seo' => $this->seo->listing(
                $page->seo_title ?: $page->title.' | '.$this->seo->siteName(),
                $page->seo_description ?: $page->excerpt ?: $page->title,
                LocalizedUrl::route('about'),
            ),
        ]);
    }
}
