<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\LandingPage;
use App\Support\Frontend\MediaUrl;
use App\Support\Landing\LandingPageBlocks;
use App\Support\Localization\LocalizedUrl;
use App\Support\Seo\FrontendSeoBuilder;
use Illuminate\View\View;

/** Public renderer for database-backed landing pages and their templates. */
class LandingController extends Controller
{
    public function __construct(
        private readonly FrontendSeoBuilder $seo,
        private readonly LandingPageBlocks $landingPageBlocks,
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

        $templateView = $this->landingPageBlocks->templateView($landingPage);
        $templateDefinition = $this->landingPageBlocks->templateDefinition($landingPage);
        $usesBuilderLayout = collect($landingPage->sections)->isNotEmpty();

        return view($templateView, [
            'landingPage' => $landingPage,
            'landingBlocks' => $usesBuilderLayout ? $this->landingPageBlocks->prepare($landingPage) : [],
            'landingTheme' => $this->landingPageBlocks->theme($landingPage),
            'landingTemplateView' => $templateView,
            'landingTemplateDefinition' => $templateDefinition,
            'landingTemplateSettings' => $this->landingPageBlocks->templateSettings($landingPage),
            'landingTemplateMedia' => $this->landingPageBlocks->templateMedia($landingPage),
            'landingCampaignState' => $this->landingPageBlocks->campaignState($landingPage),
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
