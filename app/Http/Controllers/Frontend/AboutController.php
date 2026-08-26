<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ContentItem;
use App\Models\MediaAsset;
use App\Services\WordPress\WordPressMediaUrlMapper;
use App\Settings\AboutSettings;
use App\Settings\WebsiteSettings;
use App\Support\Frontend\MediaUrl;
use App\Support\Localization\LanguageCatalog;
use App\Support\Localization\LocalizedUrl;
use App\Support\Seo\FrontendSeoBuilder;
use Awcodes\Curator\Models\Media;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function __construct(
        private readonly WordPressMediaUrlMapper $mediaUrlMapper,
        private readonly FrontendSeoBuilder $seo,
        private readonly AboutSettings $settings,
        private readonly WebsiteSettings $website,
        private readonly LanguageCatalog $languages,
    ) {}

    public function __invoke(): View
    {
        $legacyPage = ContentItem::query()
            ->where('source', 'wordpress')
            ->where('type', 'page')
            ->where('status', 'published')
            ->where('slug', 've-chung-toi')
            ->first();

        $legacyMedia = $legacyPage
            ? MediaAsset::query()
                ->where('source', 'wordpress')
                ->where('source_id', $legacyPage->featured_media_source_id)
                ->first()
            : null;
        $managed = [
            'title' => $this->translated($this->settings->page_title),
            'intro' => $this->translated($this->settings->page_intro),
            'story' => $this->translated($this->settings->story),
            'history' => $this->translated($this->settings->history),
            'mission' => $this->translated($this->settings->mission),
            'vision' => $this->translated($this->settings->vision),
            'core_values' => $this->translated($this->settings->core_values),
        ];
        $hasManagedContent = collect($managed)->filter(fn (string $value): bool => filled($value))->isNotEmpty();

        abort_if(! $legacyPage && ! $hasManagedContent, 404);

        $legacyBody = $legacyPage
            ? $this->mediaUrlMapper->absoluteLocalMediaUrls((string) $legacyPage->body)
            : '';
        $managedImageUrl = $this->website->about_image_media_id
            ? Media::query()->find($this->website->about_image_media_id)?->url
            : null;
        $about = [
            'title' => $managed['title'] ?: $legacyPage?->title ?: $this->website->company_name,
            'intro' => $managed['intro'] ?: $legacyPage?->excerpt ?: '',
            'image_url' => $managedImageUrl ?: MediaUrl::resolve(null, $legacyMedia),
            'story' => $managed['story'] ?: $legacyBody,
            'history' => $managed['history'],
            'mission' => $managed['mission'],
            'vision' => $managed['vision'],
            'core_values' => $managed['core_values'],
        ];
        $description = $about['intro'] ?: trim(strip_tags($about['story'])) ?: $about['title'];

        return view('frontend.about', compact('about') + [
            'seo' => $this->seo->listing(
                $legacyPage?->seo_title ?: $about['title'].' | '.$this->seo->siteName(),
                $legacyPage?->seo_description ?: $description,
                LocalizedUrl::route('about'),
            ),
        ]);
    }

    private function translated(array $content): string
    {
        $locale = app()->getLocale();
        $defaultLocale = $this->languages->defaultCode();
        $fallback = reset($content);

        return (string) ($content[$locale] ?? $content[$defaultLocale] ?? $fallback ?? '');
    }
}
