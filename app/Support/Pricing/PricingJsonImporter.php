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

        if (array_key_exists('is_cumulative', $payload)) {
            throw new InvalidArgumentException('Không còn dùng is_cumulative. Hãy khai báo Có/Không trong comparison.');
        }
        $replacePackages = array_is_list($payload) || isset($payload['packages']) || isset($payload['plans']);
        $packages = array_is_list($payload) ? $payload : ($payload['packages'] ?? $payload['plans'] ?? []);
        if (! is_array($packages) || ($replacePackages && $packages === [])) {
            throw new InvalidArgumentException('packages phải là danh sách gói giá không rỗng.');
        }
        if (! $replacePackages && ! array_key_exists('comparison', $payload)) {
            throw new InvalidArgumentException('JSON cần có packages hoặc comparison.');
        }
        $comparison = $payload['comparison'] ?? [];
        if (! is_array($comparison) || ! array_is_list($comparison)) {
            throw new InvalidArgumentException('comparison phải là danh sách tiêu chí.');
        }
        $counts = ['packages' => 0, 'items' => 0];

        DB::transaction(function () use ($pricing, $json, $payload, $packages, $comparison, $replacePackages, &$counts): void {
            $packageMap = [];
            if ($replacePackages) {
                $pricing->packages()->delete();
                foreach (array_values($packages) as $index => $data) {
                    if (! is_array($data) || blank($data['name'] ?? $data['title'] ?? null)) {
                        throw new InvalidArgumentException('Mỗi gói phải có tên.');
                    }
                    $key = (string) ($data['key'] ?? $index + 1);
                    if ($key === '' || isset($packageMap[$key])) {
                        throw new InvalidArgumentException('key của mỗi gói phải khác nhau và không được rỗng.');
                    }
                    $package = $pricing->packages()->create([
                        'name' => trim((string) ($data['name'] ?? $data['title'])),
                        'badge' => $this->nullableString($data['badge'] ?? null),
                        'description' => $this->nullableString($data['description'] ?? null),
                        'list_price' => $this->money($data['list_price'] ?? $data['price'] ?? null),
                        'price_label' => $this->nullableString($data['price_label'] ?? null),
                        'price_unit' => $this->nullableString($data['price_unit'] ?? null),
                        'promotion_type' => $this->promotionType($data),
                        'promotion_value' => $this->promotionValue($data),
                        'is_featured' => (bool) ($data['is_featured'] ?? false),
                        'is_active' => (bool) ($data['is_active'] ?? true),
                        'sort_order' => (int) ($data['sort_order'] ?? $index + 1),
                    ]);
                    $packageMap[$key] = $package->id;
                    $counts['packages']++;
                    $items = $data['items'] ?? $data['features'] ?? [];
                    if (! is_array($items)) {
                        throw new InvalidArgumentException('items phải là danh sách quyền lợi.');
                    }
                    foreach (array_values($items) as $itemIndex => $itemData) {
                        $item = is_array($itemData) ? $itemData : ['name' => $itemData];
                        $name = trim((string) ($item['name'] ?? $item['title'] ?? ''));
                        if ($name === '') {
                            continue;
                        }
                        $package->items()->create([
                            'name' => $name,
                            'description' => $this->nullableString($item['description'] ?? $item['text'] ?? null),
                            'is_active' => (bool) ($item['is_active'] ?? true),
                            'sort_order' => $itemIndex + 1,
                        ]);
                        $counts['items']++;
                    }
                }
            } else {
                $packageMap = $pricing->packages()->pluck('id', 'id')->all();
            }

            $rows = [];
            foreach ($comparison as $row) {
                if (! is_array($row) || blank($row['name'] ?? null) || ! is_array($row['cells'] ?? null)) {
                    throw new InvalidArgumentException('Mỗi tiêu chí cần name và danh sách cells.');
                }
                $cells = [];
                $seen = [];
                foreach ($row['cells'] as $cell) {
                    if (! is_array($cell) || ! is_scalar($cell['package'] ?? null) || ! is_bool($cell['included'] ?? null)) {
                        throw new InvalidArgumentException('Mỗi ô cần package và included là true hoặc false.');
                    }
                    $key = (string) $cell['package'];
                    if (! isset($packageMap[$key]) || isset($seen[$key])) {
                        throw new InvalidArgumentException('Ô so sánh trỏ đến gói không tồn tại hoặc lặp gói trong cùng tiêu chí: '.$key);
                    }
                    if (isset($cell['value']) && ! is_scalar($cell['value'])) {
                        throw new InvalidArgumentException('value phải là chuỗi hoặc số.');
                    }
                    $seen[$key] = true;
                    $cells[] = ['package_id' => $packageMap[$key], 'included' => $cell['included'], 'value' => $this->nullableString($cell['value'] ?? null)];
                }
                $rows[] = ['name' => trim((string) $row['name']), 'cells' => $cells];
            }
            if ($replacePackages) {
                if (filled($payload['title'] ?? null)) {
                    $pricing->title = trim((string) $payload['title']);
                }
                if (array_key_exists('description', $payload)) {
                    $pricing->description = $this->nullableString($payload['description']);
                }
            }
            $pricing->comparison_rows = $rows;
            $pricing->source_json = trim($json);
            $pricing->save();
            $pricing->unsetRelation('packages');
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
