<?php

namespace App\Support\Pricing;

use App\Models\ServicePricing;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use JsonException;

class PricingJsonImporter
{
    /** @return array{packages: int, items: int} */
    public function import(ServicePricing $pricing, string $json): array
    {
        try {
            $payload = json_decode(trim($json), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new InvalidArgumentException('JSON không hợp lệ: '.$exception->getMessage(), previous: $exception);
        }

        if (! is_array($payload)) {
            throw new InvalidArgumentException('JSON bảng giá phải là một object hoặc một danh sách gói giá.');
        }

        $packages = array_is_list($payload)
            ? $payload
            : ($payload['packages'] ?? $payload['plans'] ?? []);

        if (! is_array($packages)) {
            throw new InvalidArgumentException('JSON phải có thuộc tính packages hoặc plans là một danh sách.');
        }

        $validPackageCount = collect($packages)
            ->filter(fn (mixed $package): bool => is_array($package) && filled($package['name'] ?? $package['title'] ?? null))
            ->count();
        if ($validPackageCount === 0) {
            throw new InvalidArgumentException('JSON chưa có gói giá hợp lệ để nhập.');
        }

        $title = is_array($payload) && ! array_is_list($payload)
            ? trim((string) ($payload['title'] ?? ''))
            : '';
        $description = is_array($payload) && ! array_is_list($payload)
            ? trim((string) ($payload['description'] ?? ''))
            : '';
        $counts = ['packages' => 0, 'items' => 0];

        DB::transaction(function () use ($pricing, $json, $packages, $title, $description, &$counts): void {
            if ($title !== '') {
                $pricing->title = $title;
            }
            if ($description !== '') {
                $pricing->description = $description;
            }
            $pricing->source_json = trim($json);
            $pricing->save();

            $pricing->packages()->delete();

            foreach (array_values($packages) as $packageIndex => $packageData) {
                if (! is_array($packageData)) {
                    continue;
                }

                $name = trim((string) ($packageData['name'] ?? $packageData['title'] ?? ''));
                if ($name === '') {
                    continue;
                }

                $package = $pricing->packages()->create([
                    'name' => $name,
                    'badge' => $this->nullableString($packageData['badge'] ?? null),
                    'description' => $this->nullableString($packageData['description'] ?? null),
                    'list_price' => $this->money($packageData['list_price'] ?? $packageData['price'] ?? null),
                    'price_label' => $this->nullableString($packageData['price_label'] ?? null),
                    'price_unit' => $this->nullableString($packageData['price_unit'] ?? null),
                    'promotion_type' => $this->promotionType($packageData),
                    'promotion_value' => $this->promotionValue($packageData),
                    'is_featured' => (bool) ($packageData['is_featured'] ?? false),
                    'is_active' => (bool) ($packageData['is_active'] ?? true),
                    'sort_order' => (int) ($packageData['sort_order'] ?? $packageIndex + 1),
                ]);
                $counts['packages']++;

                $items = $packageData['items'] ?? $packageData['features'] ?? [];
                if (! is_array($items)) {
                    continue;
                }

                foreach (array_values($items) as $itemIndex => $itemData) {
                    $item = is_array($itemData)
                        ? [
                            'name' => trim((string) ($itemData['name'] ?? $itemData['title'] ?? '')),
                            'description' => $this->nullableString($itemData['description'] ?? $itemData['text'] ?? null),
                        ]
                        : ['name' => trim((string) $itemData), 'description' => null];

                    if ($item['name'] === '') {
                        continue;
                    }

                    $package->items()->create([
                        'name' => $item['name'],
                        'description' => $item['description'],
                        'is_active' => true,
                        'sort_order' => $itemIndex + 1,
                    ]);
                    $counts['items']++;
                }
            }
        });

        return $counts;
    }

    /** @param array<string, mixed> $package */
    private function promotionType(array $package): ?string
    {
        $type = strtolower(trim((string) ($package['promotion_type'] ?? data_get($package, 'promotion.type', ''))));

        if (in_array($type, ['fixed', 'fixed_price', 'fixed-price', 'sale_price'], true) || array_key_exists('sale_price', $package)) {
            return 'fixed_price';
        }

        if (in_array($type, ['percent', 'percentage', 'discount_percent'], true) || array_key_exists('discount_percent', $package)) {
            return 'percent';
        }

        return null;
    }

    /** @param array<string, mixed> $package */
    private function promotionValue(array $package): ?float
    {
        $type = $this->promotionType($package);
        $value = match ($type) {
            'fixed_price' => $package['promotion_value'] ?? data_get($package, 'promotion.value', $package['sale_price'] ?? null),
            'percent' => $package['promotion_value'] ?? data_get($package, 'promotion.value', $package['discount_percent'] ?? null),
            default => null,
        };

        return $value === null || $value === '' ? null : (float) $value;
    }

    private function money(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value)) {
            $value = preg_replace('/[^0-9-]/', '', $value) ?: null;
        }

        return $value === null ? null : max(0, (int) $value);
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) ($value ?? ''));

        return $value === '' ? null : $value;
    }
}
