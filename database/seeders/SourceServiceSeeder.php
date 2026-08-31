<?php

namespace Database\Seeders;

use App\Models\PricingPackage;
use App\Models\ServicePricing;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * Laravel-owned snapshot of the published service catalogue.
 *
 * The source snapshot is intentionally data-only. Public requests never read
 * an external CMS; they use Service, MySQL and Curator media only.
 */
class SourceServiceSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('content/source-services.json');

        if (! File::exists($path)) {
            return;
        }

        $records = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);
        $projectLinks = File::exists($projectPath = database_path('content/source-service-projects.json'))
            ? json_decode(File::get($projectPath), true, 512, JSON_THROW_ON_ERROR)
            : [];

        DB::transaction(function () use ($records, $projectLinks): void {
            foreach ($records as $record) {
                $this->upsertService($record, $projectLinks);
            }
        });
    }

    /** @param array<string, mixed> $record @param array<string, mixed> $projectLinks */
    private function upsertService(array $record, array $projectLinks): void
    {
        $slug = trim((string) ($record['slug'] ?? ''));
        $data = (array) ($record['service'] ?? []);

        if ($slug === '' || $data === []) {
            return;
        }

        $service = Service::query()
            ->whereHas('slugs', fn ($query) => $query->where('slug', $slug))
            ->first();

        $landing = \App\Models\LandingPage::query()
            ->whereHas('slugs', fn ($query) => $query->where('slug', $slug))
            ->first();

        if (! $service) {
            $service = new Service;
        }

        $categoryId = DB::table('slugs')
            ->where('sluggable_type', 'service-category')
            ->where('slug', (string) ($record['category_slug'] ?? ''))
            ->value('sluggable_id');

        if ($categoryId) {
            $data['service_category_id'] = (int) $categoryId;
        }

        $pricingMediaId = $data['pricing_media_id'] ?? null;
        unset($data['pricing_media_id']);

        $data['status'] = $data['status'] ?? 'published';
        $data['published_at'] = $data['published_at'] ?? now();
        $service->fill($data);
        $service->saveQuietly();

        if ($landing) {
            $this->moveLandingRelations($landing->id, $service);
        }

        DB::table('slugs')
            ->where('sluggable_type', 'service')
            ->where('sluggable_id', $service->id)
            ->delete();

        if ($landing) {
            DB::table('slugs')
                ->where('sluggable_type', 'landing-page')
                ->where('sluggable_id', $landing->id)
                ->update([
                    'sluggable_type' => 'service',
                    'sluggable_id' => $service->id,
                ]);
        }

        if (! DB::table('slugs')->where('sluggable_type', 'service')->where('sluggable_id', $service->id)->where('slug', $slug)->exists()) {
            DB::table('slugs')->insert([
                'sluggable_type' => 'service',
                'sluggable_id' => $service->id,
                'locale' => 'vi',
                'slug' => $slug,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if ($landing) {
            DB::table('pricing_plans')->where('landing_page_id', $landing->id)->delete();
            DB::table('landing_pages')->where('id', $landing->id)->delete();
        }

        $this->syncPricingCatalog($service, $pricingMediaId, (array) ($record['plans'] ?? []));

        if (array_key_exists($slug, $projectLinks)) {
            $service->backstageProjects()->syncWithoutDetaching(array_map('intval', (array) $projectLinks[$slug]));
        }
    }

    /** @param list<array<string, mixed>> $plans */
    private function syncPricingCatalog(Service $service, mixed $pricingMediaId, array $plans): void
    {
        if (blank($pricingMediaId) && $plans === [] && ! $service->pricingCatalog()->exists()) {
            return;
        }

        $pricing = ServicePricing::query()->firstOrCreate(
            ['service_id' => $service->id],
            ['title' => 'Bảng giá dịch vụ'],
        );

        if (filled($pricingMediaId) && blank($pricing->source_media_id)) {
            $pricing->source_media_id = (int) $pricingMediaId;
            $pricing->save();
        }

        foreach ($plans as $index => $plan) {
            $name = trim((string) ($plan['name'] ?? ''));
            if ($name === '') {
                continue;
            }

            $package = PricingPackage::query()->firstOrCreate(
                [
                    'service_pricing_id' => $pricing->id,
                    'name' => $name,
                ],
                [
                    'description' => $plan['description'] ?? null,
                    'list_price' => $plan['price'] ?? null,
                    'price_label' => $plan['price_label'] ?? null,
                    'price_unit' => $plan['price_unit'] ?? null,
                    'badge' => $plan['badge'] ?? null,
                    'is_featured' => (bool) ($plan['is_featured'] ?? false),
                    'is_active' => (bool) ($plan['is_active'] ?? true),
                    'sort_order' => (int) ($plan['sort_order'] ?? $index + 1),
                ],
            );

            foreach ((array) ($plan['features'] ?? []) as $itemIndex => $feature) {
                $itemName = is_array($feature)
                    ? trim((string) ($feature['name'] ?? $feature['title'] ?? $feature['label'] ?? ''))
                    : trim((string) $feature);
                if ($itemName === '') {
                    continue;
                }

                $package->items()->firstOrCreate(
                    ['name' => $itemName],
                    [
                        'description' => is_array($feature) ? ($feature['description'] ?? null) : null,
                        'sort_order' => $itemIndex + 1,
                    ],
                );
            }
        }
    }

    private function moveLandingRelations(int $landingId, Service $service): void
    {
        DB::table('landing_page_project')
            ->where('landing_page_id', $landingId)
            ->get()
            ->each(function (object $pivot) use ($service): void {
                DB::table('project_service')->insertOrIgnore([
                    'project_id' => $pivot->project_id,
                    'service_id' => $service->id,
                    'created_at' => $pivot->created_at,
                    'updated_at' => $pivot->updated_at,
                ]);
            });

        DB::table('landing_events')->where('landing_page_id', $landingId)->update([
            'service_id' => $service->id,
            'landing_page_id' => null,
        ]);
        DB::table('contact_requests')->where('landing_page_id', $landingId)->update([
            'service_id' => $service->id,
            'landing_page_id' => null,
        ]);
        DB::table('comments')
            ->where('commentable_type', 'landing-page')
            ->where('commentable_id', $landingId)
            ->update([
                'commentable_type' => 'service',
                'commentable_id' => $service->id,
            ]);
        DB::table('menu_items')
            ->where('linked_source_id', $landingId)
            ->whereIn('linked_source_type', ['App\\Models\\LandingPage', 'native_landing_page'])
            ->update([
                'linked_source_id' => $service->id,
                'linked_source_type' => 'native_service',
            ]);
    }
}
