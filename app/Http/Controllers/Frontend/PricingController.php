<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\PricingPlan;
use App\Models\Landing;
use App\Support\Seo\FrontendSeoBuilder;
use Illuminate\View\View;

class PricingController extends Controller
{
    public function __construct(private readonly FrontendSeoBuilder $seo) {}

    public function index(): View
    {
        $selectedLandingId = request()->integer('landing') ?: request()->integer('service');
        $selectedLanding = $selectedLandingId
            ? Landing::query()->published()->find($selectedLandingId)
            : null;

        $plans = PricingPlan::query()
            ->active()
            ->when($selectedLanding, fn ($query) => $query->where('landing_id', $selectedLanding->id))
            ->with('landing')
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->get();

        return view('frontend.pricing.index', [
            'plans' => $plans,
            'selectedService' => $selectedLanding,
            'seo' => $this->seo->pricing($plans),
        ]);
    }
}
