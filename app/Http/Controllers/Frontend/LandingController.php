<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\LandingPage;
use App\Support\Frontend\MediaUrl;
use App\Support\Landing\LandingPageBlocks;
use App\Support\Landing\LandingPresenter;
use App\Support\Landing\LandingRegistry;
use App\Support\Seo\FrontendSeoBuilder;
use Illuminate\View\View;

/** Public renderer for database-backed landing pages and their templates. */
class LandingController extends Controller
{
    public function __construct(
        private readonly FrontendSeoBuilder $seo,
        private readonly LandingPageBlocks $landingPageBlocks,
        private readonly LandingPresenter $landingPresenter,
    ) {}

    public function show(LandingPage $landingPage): View
    {
        abort_unless($landingPage->status === 'published' && (! $landingPage->published_at || $landingPage->published_at->isPast()), 404);

        $landingPage->load([
            'landingTemplate',
            'curatorMedia',
            'pricingPlans' => fn ($query) => $query->active()->orderBy('sort_order'),
            'projects' => fn ($query) => $query
                ->published()
                ->with(['category', 'curatorMedia'])
                ->orderByDesc('published_at'),
            'serviceCategories' => fn ($query) => $query
                ->where('is_active', true)
                ->orderBy('sort_order'),
            'services' => fn ($query) => $query
                ->published()
                ->with(['category', 'curatorMedia'])
                ->orderByDesc('is_featured')
                ->orderBy('sort_order'),
            'posts' => fn ($query) => $query
                ->published()
                ->with(['categories', 'curatorMedia'])
                ->orderByDesc('published_at'),
        ]);

        $landingPage->setAttribute('image_url', MediaUrl::resolve($landingPage->curatorMedia));

        $landingTemplateKey = LandingRegistry::templateForSlug($landingPage->slug) ?: $landingPage->template_key;
        $usesLanding = LandingRegistry::find($landingTemplateKey) !== null;
        $templateView = $usesLanding
            ? (LandingRegistry::find($landingTemplateKey)['shell'] ?? 'frontend.landing.shell')
            : $this->landingPageBlocks->templateView($landingPage);
        $templateDefinition = $this->landingPageBlocks->templateDefinition($landingPage);
        $usesBuilderLayout = ! $usesLanding && collect($landingPage->sections)->isNotEmpty();
        $landingTemplateSettings = $this->landingPageBlocks->templateSettings($landingPage);
        $landingTheme = $this->landingPageBlocks->theme($landingPage);

        return view($usesLanding ? $templateView : 'frontend.landing.builder-shell', [
            'landingPage' => $landingPage,
            'landingBlocks' => $usesBuilderLayout ? $this->landingPageBlocks->prepare($landingPage) : [],
            'landingTheme' => $landingTheme,
            'landingViewModel' => $usesLanding
                ? $this->landingPresenter->present($landingTemplateKey, $landingPage)
                : [],
            'landingTemplateView' => $templateView,
            'landingTemplateDefinition' => $templateDefinition,
            'landingLayout' => 'layouts.landing',
            'landingTemplateSettings' => $landingTemplateSettings,
            'landingTemplateMedia' => $this->landingPageBlocks->templateMedia($landingPage),
            'landingCampaignState' => $this->landingPageBlocks->campaignState($landingPage),
            'landingTracking' => [
                'head' => data_get($landingTemplateSettings, 'tracking_head')
                    ?: data_get($landingTemplateSettings, 'communications_source.tracking_head'),
                'body' => data_get($landingTemplateSettings, 'tracking_body')
                    ?: data_get($landingTemplateSettings, 'communications_source.tracking_body'),
                'footer' => data_get($landingTemplateSettings, 'tracking_footer')
                    ?: data_get($landingTemplateSettings, 'communications_source.tracking_footer'),
            ],
            'landingAssets' => [
                'vite' => $usesLanding
                    ? LandingRegistry::viteAssets($landingTemplateKey)
                    : ['resources/css/app.css', 'resources/js/app.js'],
            ],
            'landingTrackingUrl' => route('landing-pages.track', ['landingPage' => $landingPage->id]),
            'hideHeader' => ! $landingPage->show_header,
            'hideFooter' => ! $landingPage->show_footer,
            'seo' => $this->seo->landingPage($landingPage),
        ]);
    }

    public function showBySlug(string $slug): View
    {
        return $this->show($this->landingPageForSlug($slug));
    }

    private function landingPageForSlug(string $slug): LandingPage
    {
        return LandingPage::query()
            ->whereHas('slugs', fn ($query) => $query->where('slug', $slug))
            ->firstOrFail();
    }
}
