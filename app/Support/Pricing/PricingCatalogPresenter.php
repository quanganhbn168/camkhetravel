<?php

namespace App\Support\Pricing;

use App\Models\PricingPackage;
use App\Models\ServicePricing;
use Illuminate\Support\Collection;

class PricingCatalogPresenter
{
    /** @return array{title: string, description: ?string, packages: list<array<string, mixed>>, items: list<string>} */
    public function present(?ServicePricing $pricing): array
    {
        if (! $pricing) {
            return [
                'title' => 'Bảng giá dịch vụ',
                'description' => null,
                'packages' => [],
                'items' => [],
            ];
        }

        $packages = collect($pricing->packages)
            ->filter(fn (PricingPackage $package): bool => $package->is_active)
            ->map(function (PricingPackage $package): array {
                $items = collect($package->items)
                    ->filter(fn ($item): bool => $item->is_active && filled($item->name))
                    ->map(fn ($item): array => [
                        'name' => trim((string) $item->name),
                        'description' => trim((string) ($item->description ?? '')) ?: null,
                    ])
                    ->values();

                return [
                    'id' => $package->id,
                    'name' => $package->name,
                    'badge' => $package->badge,
                    'description' => $package->description,
                    'list_price' => $package->list_price !== null ? (int) $package->list_price : null,
                    'price_label' => $package->price_label,
                    'price_unit' => $package->price_unit,
                    'promotion_type' => $package->promotion_type,
                    'promotion_value' => $package->promotion_value !== null ? (float) $package->promotion_value : null,
                    'promotion_price' => $package->promotionPrice(),
                    'has_promotion' => $package->hasPromotion(),
                    'is_featured' => $package->is_featured,
                    'items' => $items->all(),
                ];
            })
            ->values();

        return [
            'title' => $pricing->title ?: 'Bảng giá dịch vụ',
            'description' => $pricing->description,
            'packages' => $packages->all(),
            'items' => $this->uniqueItems($packages),
        ];
    }

    /** @param Collection<int, array<string, mixed>> $packages @return list<string> */
    private function uniqueItems(Collection $packages): array
    {
        return $packages
            ->flatMap(fn (array $package): array => collect($package['items'])->pluck('name')->all())
            ->map(fn (mixed $name): string => trim((string) $name))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
