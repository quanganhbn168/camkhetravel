<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\LandingPage;
use App\Support\Landing\LandingPageBlocks;
use App\Support\Media\MediaUrl;
use App\Support\Seo\FrontendSeoBuilder;
use Illuminate\View\View;

/** Renders a published landing page from its database content and builder blocks. */
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
            'curatorMedia',
            'faqs' => fn ($query) => $query->active()->ordered(),
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

        return view('frontend.landing.builder-shell', [
            'landingPage' => $landingPage,
            'landingBlocks' => $this->landingPageBlocks->prepare($landingPage),
            'landingTheme' => $this->landingPageBlocks->theme(),
            'landingAssets' => [
                'vite' => ['resources/css/app.css', 'resources/js/app.js'],
            ],
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
