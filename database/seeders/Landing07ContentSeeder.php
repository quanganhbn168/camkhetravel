<?php

namespace Database\Seeders;

use App\Models\LandingPage;
use App\Models\LandingTemplate;
use App\Models\PricingPlan;
use App\Models\Project;
use App\Support\Landing\CommunicationsLandingContent;
use App\Support\Landing\Landing07Catalog;
use App\Support\Landing\LandingTemplateRegistry;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Seeder;

class Landing07ContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(LandingTemplateSeeder::class);
        // The former landing entries are native services now.
        // Only true campaign/template pages belong in this seeder.
        $this->seedCatalogPages();
        $this->normalizeLandingTemplateContent();
    }


    private function seedCatalogPages(): void
    {
        foreach (Landing07Catalog::pages() as $slug => $page) {
            if (in_array($slug, ['phong-su-cuoi', 'to-chuc-su-kien-tron-goi'], true)) {
                continue;
            }

            $template = LandingTemplate::query()
                ->where('key', $page['template_key'])
                ->firstOrFail();
            $landing = $this->landing($slug, $page['title']);
            $isCommunications = $page['template_key'] === Landing07Catalog::COMMUNICATIONS;
            $shouldInitialize = ! $landing->exists
                || $landing->template_key !== $template->key
                || blank($landing->sections);
            $defaultImageId = $this->firstImageId();
            $catalogHeroMediaId = $this->catalogHeroMediaId($page['template_key']);
            $heroMediaId = $landing->curator_media_id ?: ($catalogHeroMediaId ?: $defaultImageId);
            $replaceDefaultHero = $landing->exists
                && $landing->curator_media_id === $defaultImageId
                && $catalogHeroMediaId
                && $catalogHeroMediaId !== $defaultImageId;
            if ($replaceDefaultHero) {
                $heroMediaId = $catalogHeroMediaId;
            }
            $galleryMediaIds = $this->mediaIds((array) $landing->backstage_gallery, (array) $landing->gallery);
            $sections = $this->hydrateSections($page['sections'], $heroMediaId, $galleryMediaIds);

            if ($shouldInitialize) {
                $landing->fill([
                    'landing_template_id' => $template->id,
                    'layout_mode' => 'custom_template',
                    'template_key' => $template->key,
                    'template_settings' => $template->default_settings,
                    'theme_settings' => $template->palette,
                    'curator_media_id' => $heroMediaId,
                    'sections' => $sections,
                    'title' => $page['title'],
                    'excerpt' => $page['excerpt'],
                    'body' => null,
                    'faq_title' => 'Trao đổi trước khi bắt đầu',
                    'faq_description' => 'Thông tin cần thống nhất trước khi triển khai.',
                    'faq_items' => $page['faq_items'],
                    'seo_title' => $page['seo_title'],
                    'seo_description' => $page['seo_description'],
                    'show_header' => false,
                    'show_footer' => false,
                    'tracking_enabled' => true,
                ]);
                $this->publishNewLanding($landing);
                $landing->save();
            } elseif ($replaceDefaultHero) {
                $landing->forceFill([
                    'curator_media_id' => $heroMediaId,
                    'sections' => array_map(function (array $section) use ($heroMediaId): array {
                        if (($section['type'] ?? null) === 'hero') {
                            $section['data']['media_id'] = $heroMediaId;
                        }

                        return $section;
                    }, (array) $landing->sections),
                ])->saveQuietly();
            }

            $this->seedPlans($landing, $page['plans']);
            $landing->projects()->syncWithoutDetaching($this->catalogProjectIds($page['project_terms']));

            if ($isCommunications) {
                $landing->forceFill([
                    'landing_template_id' => $template->id,
                    'layout_mode' => 'custom_template',
                    'template_key' => $template->key,
                    'template_settings' => CommunicationsLandingContent::templateSettings(),
                    'theme_settings' => $template->palette,
                    'sections' => $this->hydrateSections(
                        CommunicationsLandingContent::sections(),
                        $heroMediaId,
                        $galleryMediaIds,
                    ),
                    'title' => $page['title'],
                    'excerpt' => $page['excerpt'],
                    'faq_items' => $page['faq_items'],
                    'seo_title' => $page['seo_title'],
                    'seo_description' => $page['seo_description'],
                    'show_header' => false,
                    'show_footer' => false,
                    'tracking_enabled' => true,
                ])->saveQuietly();
            }
        }
    }

    /** @return list<int> */
    private function catalogProjectIds(array $terms): array
    {
        $ids = Project::query()
            ->published()
            ->where(function ($query) use ($terms): void {
                foreach ($terms as $term) {
                    $query
                        ->orWhere('title', 'like', '%'.$term.'%')
                        ->orWhere('client_name', 'like', '%'.$term.'%')
                        ->orWhere('industry', 'like', '%'.$term.'%')
                        ->orWhereHas('category', fn ($category) => $category->where('name', 'like', '%'.$term.'%'));
                }
            })
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->limit(6)
            ->pluck('id')
            ->map(fn (mixed $id): int => (int) $id)
            ->all();

        if (count($ids) >= 4) {
            return $ids;
        }

        return array_values(array_unique([
            ...$ids,
            ...$this->filmProjectIds(),
            ...$this->eventProjectIds(),
        ]));
    }

    private function normalizeLandingTemplateContent(): void
    {
        $templateKeys = [
            LandingTemplateRegistry::CORPORATE_FILM,
            LandingTemplateRegistry::EVENT_MEDIA,
            ...array_keys(Landing07Catalog::templates()),
        ];

        LandingPage::query()
            ->whereIn('template_key', $templateKeys)
            ->get()
            ->each(function (LandingPage $landing): void {
                $settings = is_array($landing->template_settings) ? $landing->template_settings : [];

                unset($settings['film_showreel_url']);

                $landing->forceFill([
                    'body' => null,
                    'template_settings' => $settings,
                ])->saveQuietly();
            });
    }

    private function landing(string $slug, string $title): LandingPage
    {
        return LandingPage::query()
            ->whereHas('slugs', fn ($query) => $query->where('slug', $slug))
            ->first() ?? new LandingPage(['title' => $title, 'slug' => $slug]);
    }

    private function publishNewLanding(LandingPage $landing): void
    {
        if (! $landing->exists) {
            $landing->status = 'published';
            $landing->published_at = now();
            $landing->sort_order = ((int) LandingPage::query()->max('sort_order')) + 1;
        }

        if (! $landing->curator_media_id) {
            $landing->curator_media_id = $this->firstImageId();
        }
    }

    /** @param list<array<string, mixed>> $sections */
    private function hydrateSections(array $sections, ?int $heroMediaId, array $galleryMediaIds): array
    {
        return array_map(function (array $section) use ($heroMediaId, $galleryMediaIds): array {
            $data = is_array($section['data'] ?? null) ? $section['data'] : [];

            if (($section['type'] ?? null) === 'hero' && $heroMediaId) {
                $data['media_id'] = $heroMediaId;
            }

            if (($section['type'] ?? null) === 'gallery') {
                $data['media_ids'] = array_slice($galleryMediaIds, 0, 12);
            }

            $section['data'] = $data;

            return $section;
        }, $sections);
    }

    /** @param array<int, mixed> ...$groups */
    private function mediaIds(array ...$groups): array
    {
        $ids = [];

        foreach ($groups as $group) {
            foreach ($group as $id) {
                if (is_numeric($id)) {
                    $ids[] = (int) $id;
                }
            }
        }

        return array_values(array_unique($ids));
    }

    private function firstImageId(): ?int
    {
        return Media::query()->where('type', 'like', 'image/%')->orderBy('id')->value('id');
    }

    private function catalogHeroMediaId(string $templateKey): ?int
    {
        $terms = match ($templateKey) {
            Landing07Catalog::WEDDING => ['psc', 'wedding', 'cưới'],
            Landing07Catalog::PROFILE => ['profile', 'hồ sơ', 'company'],
            Landing07Catalog::OUTSOURCED_MARKETING => ['HNP00214', 'marketing', 'team'],
            Landing07Catalog::EVENT_ORGANIZATION => ['sự kiện', 'event', 'hội nghị', 'khai trương'],
            Landing07Catalog::ADS => ['quảng cáo', 'facebook', 'ads', 'tvc'],
            Landing07Catalog::COMMUNICATIONS => ['truyền thông', 'tvc', 'doanh nghiệp'],
            Landing07Catalog::ACADEMY, Landing07Catalog::ACADEMY_V2 => ['nhiếp ảnh', 'studio', 'kỷ yếu'],
            default => [],
        };

        if ($terms === []) {
            return null;
        }

        return Media::query()
            ->where('type', 'like', 'image/%')
            ->where(function ($query) use ($terms): void {
                foreach ($terms as $term) {
                    $query
                        ->orWhere('path', 'like', '%'.$term.'%')
                        ->orWhere('name', 'like', '%'.$term.'%')
                        ->orWhere('title', 'like', '%'.$term.'%');
                }
            })
            ->orderBy('id')
            ->value('id');
    }

    /** @param list<array<string, mixed>> $plans */
    private function seedPlans(LandingPage $landing, array $plans, ?string $priceUnit = null): void
    {
        foreach ($plans as $index => $plan) {
            $pricingPlan = PricingPlan::query()->firstOrNew([
                'landing_page_id' => $landing->id,
                'name' => $plan['name'],
            ]);

            if (! $pricingPlan->exists) {
                $pricingPlan->fill([
                    'description' => $plan['description'] ?? null,
                    'price' => $plan['price'] ?? null,
                    'price_label' => $plan['price_label'] ?? null,
                    'price_unit' => $priceUnit,
                    'badge' => $plan['badge'] ?? null,
                    'features' => $plan['features'],
                    'is_featured' => filled($plan['badge'] ?? null),
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ])->save();
            }
        }
    }

    /** @return list<int> */
    private function filmProjectIds(): array
    {
        return Project::query()
            ->published()
            ->whereHas('category', fn ($query) => $query->where('name', 'like', '%TVC%'))
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->limit(6)
            ->pluck('id')
            ->map(fn (mixed $id): int => (int) $id)
            ->all();
    }

    /** @return list<int> */
    private function eventProjectIds(): array
    {
        return Project::query()
            ->published()
            ->where(function ($query): void {
                $query->whereHas('category', fn ($category) => $category
                    ->where('name', 'like', '%sự kiện%')
                    ->orWhere('name', 'like', '%Livestream%')
                    ->orWhere('name', 'like', '%highlight%'))
                    ->orWhere('title', 'like', '%sự kiện%')
                    ->orWhere('title', 'like', '%hội nghị%')
                    ->orWhere('title', 'like', '%khai trương%')
                    ->orWhere('title', 'like', '%khánh thành%')
                    ->orWhere('title', 'like', '%tất niên%');
            })
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->limit(6)
            ->pluck('id')
            ->map(fn (mixed $id): int => (int) $id)
            ->all();
    }
}
