<?php

namespace App\Support\Categories;

use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

final class CategoryTree
{
    public static function tablePath(Model $record): ?string
    {
        if ($record->parent_id === null) {
            return null;
        }

        $key = 'category-table-paths:'.$record::class;
        if (! request()->attributes->has($key)) {
            request()->attributes->set($key, self::options($record::class));
        }

        return request()->attributes->get($key)[$record->id] ?? null;
    }

    public static function forDisplay(Collection $categories, string $countAttribute): Collection
    {
        $groups = $categories->groupBy('parent_id');
        $ordered = collect();
        $visited = [];
        $visit = function ($parent, string $prefix = '') use (&$visit, &$visited, $groups, $ordered, $countAttribute): int {
            $total = 0;
            foreach ($groups->get($parent, collect()) as $category) {
                if (isset($visited[$category->id])) {
                    continue;
                }
                $visited[$category->id] = true;
                $category->setAttribute('tree_label', $prefix.$category->name);
                $ordered->push($category);
                $count = (int) $category->{$countAttribute} + $visit($category->id, $prefix.$category->name.' > ');
                $category->setAttribute($countAttribute, $count);
                $total += $count;
            }

            return $total;
        };
        $visit('');

        return $ordered;
    }

    public static function options(string $model): array
    {
        $rows = $model::query()->orderBy('sort_order')->orderBy('id')->get(['id', 'parent_id', 'name'])->groupBy('parent_id');
        $options = [];
        $visit = function ($parent, string $prefix = '') use (&$visit, &$options, $rows): void {
            foreach ($rows->get($parent, collect()) as $row) {
                if (isset($options[$row->id])) {
                    continue;
                }
                $label = $prefix.$row->name;
                $options[$row->id] = $label;
                $visit($row->id, $label.' > ');
            }
        };
        $visit('');

        return $options;
    }

    public static function subtreeIds(string $model, int $id, bool $activeOnly = false): array
    {
        $rows = $model::query()->when($activeOnly, fn ($query) => $query->where('is_active', true))->get(['id', 'parent_id'])->groupBy('parent_id');
        $ids = [];
        $visit = function (int $key) use (&$visit, &$ids, $rows): void {
            if (in_array($key, $ids, true)) {
                return;
            }
            $ids[] = $key;
            foreach ($rows->get($key, collect()) as $child) {
                $visit((int) $child->id);
            }
        };
        $visit($id);

        return $ids;
    }

    public static function parentOptions(string $model, ?Model $record = null): array
    {
        $excluded = $record?->exists ? $record->subtreeIds() : [];
        if ($model === ProductCategory::class) {
            $excluded = array_merge($excluded, $model::query()->has('products')->pluck('id')->all());
        }

        return array_diff_key(self::options($model), array_flip($excluded));
    }

    public static function leafOptions(string $model): array
    {
        $ids = $model::query()->doesntHave('children')->pluck('id')->all();

        return array_intersect_key(self::options($model), array_flip($ids));
    }
}
