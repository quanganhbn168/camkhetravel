<?php

namespace App\Actions;

use App\Models\Faq;
use App\Models\HeroSlide;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Slug;
use App\Models\Testimonial;
use App\Settings\AboutSettings;
use App\Settings\CompanySettings;
use App\Settings\HomepageSettings;
use App\Settings\SystemPageSettings;
use App\Settings\WebsiteSettings;
use Awcodes\Curator\Models\Media;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Spatie\LaravelSettings\Support\SettingsCacheFactory;
use Throwable;

/** Explicitly restore the approved public content snapshot; never restore operational data. */
final class RestoreCurrentSite
{
    private array $ids = [];

    private const SETTINGS = [WebsiteSettings::class, HomepageSettings::class, AboutSettings::class, CompanySettings::class, SystemPageSettings::class];

    public function handle(): void
    {
        $snapshot = json_decode(file_get_contents(database_path('seeders/data/current-site.json')), true, flags: JSON_THROW_ON_ERROR);
        if (($snapshot['version'] ?? null) !== 1) {
            throw new RuntimeException('Phiên bản bản chốt website không được hỗ trợ.');
        }
        $this->ids = [];
        $files = $this->mediaFiles($snapshot['content']['curator']);
        $previousFiles = [];

        try {
            DB::transaction(function () use ($snapshot, $files, &$previousFiles): void {
                // IDs in the snapshot are references, never destination primary keys.
                foreach ([
                    'curator' => [Media::class, ['disk', 'path'], null],
                    'service_categories' => [ServiceCategory::class, ['name'], ['is_active' => false]],
                    'services' => [Service::class, ['title'], ['status' => 'draft', 'is_home' => false]],
                    'post_categories' => [PostCategory::class, ['name'], ['is_active' => false]],
                    'posts' => [Post::class, ['title'], ['status' => 'draft']],
                    'hero_slides' => [HeroSlide::class, ['sort_order'], ['is_active' => false]],
                    'faqs' => [Faq::class, ['group', 'question', 'faqable_type', 'faqable_id'], ['is_active' => false]],
                    'testimonials' => [Testimonial::class, ['client_name', 'client_role'], ['is_active' => false]],
                    'menus' => [Menu::class, ['location'], ['is_active' => false]],
                ] as $table => [$class, $keys, $hidden]) {
                    foreach ($snapshot['content'][$table] as $row) {
                        $attributes = $this->resolve(Arr::except($row, ['source_id', 'slug', 'parent_id']));
                        $slug = $row['slug'] ?? null;
                        $record = $slug ? $class::whereHas('slugs', fn ($query) => $query->where('slug', $slug))->first() : null;
                        $record ??= $class::firstOrNew(Arr::only($attributes, $keys));
                        if ($slug && Slug::where('slug', $slug)->where(function ($query) use ($record): void {
                            $query->where('sluggable_type', '!=', $record->getMorphClass())
                                ->orWhere('sluggable_id', '!=', $record->getKey() ?? 0);
                        })->exists()) {
                            throw new RuntimeException("URL {$slug} đang thuộc nội dung khác. Chưa áp dụng bản chốt.");
                        }
                        $record->fill($attributes);
                        if ($slug) {
                            $record->slug = $slug;
                        }
                        $record->save();
                        $this->ids[$table][$row['source_id']] = $record->getKey();
                    }
                    foreach ($snapshot['content'][$table] as $row) {
                        if (array_key_exists('parent_id', $row)) {
                            $class::whereKey($this->id($table, $row['source_id']))->update([
                                'parent_id' => $row['parent_id'] ? $this->id($table, $row['parent_id']) : null,
                            ]);
                        }
                    }
                    if ($hidden) {
                        // Keep records and references from real enquiries; only remove them from publication.
                        $class::whereNotIn('id', array_values($this->ids[$table] ?? []))->update($hidden);
                    }
                }

                // These menus are wholly owned by the approved snapshot.
                MenuItem::whereIn('menu_id', array_values($this->ids['menus']))->delete();
                foreach ($snapshot['content']['menu_items'] as $row) {
                    if ($row['linked_source_id'] !== null) {
                        throw new RuntimeException('Bản chốt này chỉ hỗ trợ menu bằng đường dẫn.');
                    }
                    $item = MenuItem::create([
                        ...Arr::except($row, ['source_id', 'menu_id', 'parent_id']),
                        'menu_id' => $this->id('menus', $row['menu_id']),
                        'parent_id' => null,
                    ]);
                    $this->ids['menu_items'][$row['source_id']] = $item->id;
                }
                foreach ($snapshot['content']['menu_items'] as $row) {
                    if ($row['parent_id']) {
                        MenuItem::whereKey($this->id('menu_items', $row['source_id']))->update(['parent_id' => $this->id('menu_items', $row['parent_id'])]);
                    }
                }

                foreach (self::SETTINGS as $class) {
                    $settings = new $class($this->resolve($snapshot['settings'][$class::group()]));
                    $settings->settingsConfig()->resetDefaultValueLoadedProperties();
                    $settings->save();
                }
                $this->copyMedia($files, $previousFiles);
            });
        } catch (Throwable $exception) {
            $disk = Storage::disk('public');
            foreach ($previousFiles as $path => $contents) {
                $restored = $contents === null ? $disk->delete($path) : $disk->put($path, $contents);
                if (! $restored) {
                    throw new RuntimeException('Database đã rollback nhưng không thể khôi phục ảnh: '.$path, previous: $exception);
                }
            }
            throw $exception;
        } finally {
            foreach (app(SettingsCacheFactory::class)->all() as $cache) {
                if ($cache->isEnabled()) {
                    $cache->clear();
                }
            }
            foreach (self::SETTINGS as $class) {
                app($class)->refresh();
            }
        }
    }

    private function resolve(array $values): array
    {
        foreach ($values as $key => $value) {
            if ($value === null) {
                continue;
            }
            if ($key === 'media_id' || str_ends_with((string) $key, '_media_id')) {
                $values[$key] = $this->id('curator', $value);
            } elseif (in_array($key, ['gallery', 'backstage_gallery', 'office_gallery'], true)) {
                $values[$key] = array_map(fn ($id) => $this->id('curator', $id), $value);
            } elseif ($key === 'service_ids') {
                $values[$key] = array_map(fn ($id) => $this->id('services', $id), $value);
            } elseif (in_array($key, ['service_id', 'service_category_id', 'post_category_id', 'header_menu_id', 'footer_menu_id'], true)) {
                $table = match ($key) {
                    'service_id' => 'services', 'service_category_id' => 'service_categories',
                    'post_category_id' => 'post_categories', default => 'menus',
                };
                $values[$key] = $this->id($table, $value);
            } elseif (is_array($value)) {
                $values[$key] = $this->resolve($value);
            }
        }

        return $values;
    }

    private function id(string $table, int $sourceId): int
    {
        return $this->ids[$table][$sourceId] ?? throw new RuntimeException("Thiếu tham chiếu {$table}:{$sourceId} trong bản chốt.");
    }

    private function mediaFiles(array $media): array
    {
        $files = ['media/site/no-image.svg' => public_path('images/no-image.svg')];
        $manifest = json_decode(file_get_contents(public_path('images/homepage-redesign/v1/manifest.json')), true, flags: JSON_THROW_ON_ERROR);
        foreach ($manifest as $asset) {
            foreach (['.webp', '-768.webp'] as $suffix) {
                $filename = $asset['name'].$suffix;
                $files['media/homepage/v1/'.$filename] = public_path('images/homepage-redesign/v1/'.$filename);
            }
        }
        foreach ($media as $image) {
            if ($image['disk'] !== 'public' || ! isset($files[$image['path']])) {
                throw new RuntimeException('Ảnh của bản chốt chưa được đóng gói: '.$image['path']);
            }
        }
        foreach ($files as $source) {
            if (! is_file($source)) {
                throw new RuntimeException('Thiếu ảnh nguồn: '.$source);
            }
        }

        return $files;
    }

    private function copyMedia(array $files, array &$previousFiles): void
    {
        $disk = Storage::disk('public');
        foreach ($files as $path => $source) {
            $contents = file_get_contents($source);
            $existing = $disk->exists($path) ? $disk->get($path) : null;
            if ($existing === $contents) {
                continue;
            }
            $previousFiles[$path] = $existing;
            if (! $disk->put($path, $contents)) {
                throw new RuntimeException('Không thể ghi ảnh: '.$path);
            }
        }
    }
}
