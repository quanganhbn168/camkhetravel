<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Support\Media\MediaUrl;
use App\Support\Pricing\PricingCatalogPresenter;
use App\Support\Seo\FrontendSeoBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PricingController extends Controller
{
    public function __construct(
        private readonly FrontendSeoBuilder $seo,
        private readonly PricingCatalogPresenter $pricingCatalogPresenter,
    ) {}

    public function index(Request $request): View
    {
        $pricingServices = Service::query()
            ->published()
            ->whereHas('pricingCatalog', fn (Builder $query): Builder => $query
                ->where(fn (Builder $pricingQuery): Builder => $pricingQuery
                    ->whereHas('packages', fn (Builder $packageQuery): Builder => $packageQuery->where('is_active', true))
                    ->orWhereNotNull('source_media_id')
                    ->orWhereNotNull('source_url')))
            ->with([
                'category',
                'slugs',
                'pricingCatalog' => fn ($query) => $query->withCount([
                    'packages as active_packages_count' => fn ($packageQuery) => $packageQuery->where('is_active', true),
                ]),
            ])
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        $requestedServiceSlug = trim((string) $request->query('dich-vu'));
        $selectedService = $requestedServiceSlug !== ''
            ? $pricingServices->first(fn (Service $service): bool => $service->slug === $requestedServiceSlug)
            : $pricingServices->first();

        abort_if($requestedServiceSlug !== '' && ! $selectedService, 404);

        $servicePricing = $selectedService?->pricingCatalog()
            ->with([
                'sourceMedia',
                'packages' => fn ($query) => $query
                    ->active()
                    ->with(['items' => fn ($itemQuery) => $itemQuery->active()->orderBy('sort_order')])
                    ->orderBy('sort_order'),
            ])
            ->first();
        $servicePricingMatrix = $this->pricingCatalogPresenter->present($servicePricing);

        return view('frontend.pricing.index', [
            'pricingServices' => $pricingServices,
            'selectedService' => $selectedService,
            'servicePricing' => $servicePricing,
            'servicePricingMatrix' => $servicePricingMatrix,
            'pricingMediaUrl' => MediaUrl::versioned($servicePricing?->sourceMedia),
            'pricingMediaIsImage' => str_starts_with((string) $servicePricing?->sourceMedia?->type, 'image/'),
            'pricingSourceUrl' => $servicePricing?->source_url,
            'seo' => $this->seo->pricing($selectedService, $servicePricingMatrix['packages']),
        ]);
    }
}
