<?php

namespace Database\Seeders;

use App\Models\Landing;
use App\Models\LandingTemplate;
use App\Models\PricingPlan;
use App\Models\Project;
use App\Support\Landing\Landing07Catalog;
use App\Support\Landing\LandingTemplateRegistry;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Seeder;

class Landing07ContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(LandingTemplateSeeder::class);
        $this->seedCorporateFilm();
        $this->seedEventMedia();
        $this->seedCatalogPages();
        $this->normalizeLandingTemplateContent();
    }

    private function seedCorporateFilm(): void
    {
        $template = LandingTemplate::query()->where('key', LandingTemplateRegistry::CORPORATE_FILM)->firstOrFail();
        $landing = $this->landing('san-xuat-phim-doanh-nghiep', 'Sản xuất phim doanh nghiệp');
        $shouldInitialize = ! $landing->exists
            || $landing->template_key !== $template->key
            || blank($landing->sections);
        $heroMediaId = $landing->curator_media_id ?: $this->firstImageId();
        $galleryMediaIds = $this->mediaIds((array) $landing->backstage_gallery, (array) $landing->gallery);
        $sections = $this->hydrateSections(
            $template->default_sections ?? [],
            $heroMediaId,
            $galleryMediaIds,
        );

        if ($shouldInitialize) {
            $landing->fill([
                'landing_template_id' => $template->id,
                'layout_mode' => 'custom_template',
                'template_key' => $template->key,
                'template_settings' => array_replace($template->default_settings ?? [], [
                    'film_service_line' => 'Company Profile · TVC · Video thương hiệu · Video nhà máy · Event Highlight',
                    'film_showreel_url' => '',
                    'film_quote' => 'Mỗi doanh nghiệp đều có một câu chuyện riêng. Điều tạo nên khác biệt không phải là bạn có câu chuyện gì, mà là cách câu chuyện ấy được kể.',
                ]),
                'theme_settings' => $template->palette,
                'sections' => $sections,
                'title' => 'Sản xuất phim doanh nghiệp',
                'excerpt' => 'Một bộ phim doanh nghiệp không chỉ là công cụ giới thiệu, mà còn là cách doanh nghiệp truyền tải năng lực, khẳng định uy tín và tạo dựng niềm tin với khách hàng, đối tác và nhà đầu tư ngay từ lần đầu tiên tiếp cận.',
                'faq_title' => 'Câu hỏi về sản xuất phim doanh nghiệp',
                'faq_description' => 'Thông tin cần biết trước khi triển khai dự án.',
                'faq_items' => [
                    ['question' => 'Doanh nghiệp chưa có kịch bản thì có làm được không?', 'answer' => 'Có. THT Media sẽ khai thác thông tin, đề xuất ý tưởng và phát triển kịch bản để doanh nghiệp duyệt trước khi quay.'],
                    ['question' => 'Thời gian làm một phim doanh nghiệp là bao lâu?', 'answer' => 'Tiến độ phụ thuộc độ dài, số bối cảnh và yêu cầu hậu kỳ. Lịch chi tiết được chốt cùng phạm vi công việc.'],
                    ['question' => 'Có thể quay tại nhà máy đang vận hành không?', 'answer' => 'Có. Ekip sẽ khảo sát, thống nhất khu vực ghi hình và phối hợp với doanh nghiệp để bảo đảm an toàn, tiến độ.'],
                    ['question' => 'Bản phim có dùng được cho nhiều nền tảng không?', 'answer' => 'Có thể bàn giao thêm phiên bản dọc, ngang, ngắn hoặc có phụ đề theo kế hoạch phân phối đã thống nhất.'],
                ],
                'seo_title' => 'Dịch Vụ Sản Xuất Phim Doanh Nghiệp, TVC Quảng Cáo',
                'seo_description' => 'THT Media chuyên sản xuất phim giới thiệu doanh nghiệp, TVC quảng cáo chuyên nghiệp, video social từ lên kịch bản, quay phim đến hậu kỳ hoàn chỉnh.',
                'show_header' => false,
                'show_footer' => false,
                'tracking_enabled' => true,
            ]);
            $this->publishNewLanding($landing);
            $landing->save();
        }

        $plans = [
            [
                'name' => 'Cơ bản',
                'description' => 'Phù hợp doanh nghiệp cần một video giới thiệu chỉn chu, rõ ràng và tối ưu ngân sách.',
                'price' => 40000000,
                'price_label' => null,
                'badge' => null,
                'features' => [
                    'Định hướng sản xuất: Video giới thiệu doanh nghiệp theo hướng truyền thống, chất lượng hình ảnh ở mức cơ bản.',
                    'Sáng tạo hình ảnh: Idea, kịch bản văn học và kịch bản phân cảnh chi tiết.',
                    'Thiết bị: Sony A7S3, ánh sáng cơ bản, tripod, gimbal và thiết bị thu âm.',
                    'Đội ngũ 5 nhân sự: Đạo diễn, quay phim, 2 kỹ thuật và tổ chức sản xuất.',
                    'Bàn giao video 3–5 phút, chất lượng 4K, voice tiếng Việt.',
                    'Tiến độ dự kiến 10–15 ngày.',
                ],
            ],
            [
                'name' => 'Tiêu chuẩn',
                'description' => 'Nâng cấp hình ảnh thương hiệu bằng thông điệp riêng, diễn xuất và góc máy giàu cảm xúc.',
                'price' => 60000000,
                'price_label' => null,
                'badge' => 'Đề xuất',
                'features' => [
                    'Idea và kịch bản hiện đại, có thông điệp riêng; setup diễn xuất và ánh sáng nghệ thuật.',
                    'Sáng tạo hình ảnh: Idea, kịch bản văn học và kịch bản phân cảnh chi tiết.',
                    'Sony A7S3, monitor, wireless, dana dolly, flycam và thiết bị thu âm.',
                    'Đội ngũ 8 nhân sự gồm đạo diễn, quay phim, gaffer, kỹ thuật và flycam.',
                    'Bàn giao video 3–5 phút, chất lượng 4K.',
                    'Tiến độ dự kiến 15–20 ngày.',
                ],
            ],
            [
                'name' => 'Chuyên nghiệp',
                'description' => 'Dành cho thương hiệu cần câu chuyện khác biệt cùng hình ảnh và hậu kỳ chuyên sâu.',
                'price' => 80000000,
                'price_label' => null,
                'badge' => null,
                'features' => [
                    'Kịch bản sáng tạo, kể câu chuyện riêng; đầu tư hình ảnh, ánh sáng và diễn xuất.',
                    'Graphic 2D cơ bản và voice Anh, Hàn hoặc Trung bản xứ.',
                    'Sony A7S3, trọn bộ lens, flycam, FPV và hệ thiết bị chuyên nghiệp.',
                    'Đội ngũ 15 nhân sự theo cấu hình sản xuất.',
                    'Bàn giao video 3–7 phút, chất lượng 4K.',
                    'Tiến độ dự kiến 28–35 ngày.',
                ],
            ],
            [
                'name' => 'Chất lượng cao',
                'description' => 'Gói sản xuất cao cấp với tiêu chuẩn hình ảnh điện ảnh và nhận diện riêng của thương hiệu.',
                'price' => 100000000,
                'price_label' => 'Từ 100.000.000đ',
                'badge' => null,
                'features' => [
                    'Kịch bản sáng tạo mang đặc trưng riêng; đầu tư hình ảnh, bối cảnh và makeup.',
                    'Graphic 2D chuyên nghiệp và voice bản xứ đa ngôn ngữ.',
                    'Arri Alexa, trọn bộ lens, hệ ánh sáng chuyên dụng, flycam và FPV.',
                    'Đội ngũ 20 nhân sự theo cấu hình sản xuất.',
                    'Bàn giao video 3–7 phút, chất lượng 4K.',
                    'Tiến độ dự kiến 35–40 ngày.',
                ],
            ],
        ];

        $this->seedPlans($landing, $plans, '/01 video');
        $landing->backstageProjects()->syncWithoutDetaching($this->filmProjectIds());
    }

    private function seedEventMedia(): void
    {
        $template = LandingTemplate::query()->where('key', LandingTemplateRegistry::EVENT_MEDIA)->firstOrFail();
        $landing = $this->landing('quay-chup-live-su-kien-chuong-trinh', 'Quay phim – chụp ảnh sự kiện');
        $shouldInitialize = ! $landing->exists
            || $landing->template_key !== $template->key
            || blank($landing->sections);
        $heroMediaId = $landing->curator_media_id ?: $this->firstImageId();
        $galleryMediaIds = $this->mediaIds((array) $landing->gallery, (array) $landing->backstage_gallery);
        $sections = $this->hydrateSections(
            $template->default_sections ?? [],
            $heroMediaId,
            $galleryMediaIds,
        );

        if ($shouldInitialize) {
            $landing->fill([
                'landing_template_id' => $template->id,
                'layout_mode' => 'custom_template',
                'template_key' => $template->key,
                'template_settings' => $template->default_settings,
                'theme_settings' => $template->palette,
                'sections' => $sections,
                'title' => 'Quay phim – chụp ảnh sự kiện',
                'excerpt' => 'Sự kiện chỉ diễn ra một lần; những khoảnh khắc quan trọng không có lần quay lại. THT Media chuẩn bị shot-list, bố trí góc máy và hoàn thiện bộ ảnh, video phù hợp với từng kênh sử dụng.',
                'faq_title' => 'Chuẩn bị ekip quay chụp',
                'faq_description' => 'Các thông tin cần thống nhất trước ngày diễn ra sự kiện.',
                'faq_items' => [
                    ['question' => 'Có được nhận file gốc không?', 'answer' => 'Quy định file gốc cần được thống nhất trong báo giá và hợp đồng trước khi triển khai.'],
                    ['question' => 'Sự kiện chưa có timeline thì có đặt lịch được không?', 'answer' => 'Có. THT có thể tạm giữ thông tin và tư vấn cấu hình, nhưng timeline cần được hoàn thiện trước ngày quay để lập shot-list.'],
                    ['question' => 'Flycam có sử dụng được ở mọi địa điểm không?', 'answer' => 'Việc sử dụng flycam phụ thuộc địa điểm, điều kiện thời tiết, không gian và các yêu cầu liên quan tại khu vực tổ chức.'],
                    ['question' => 'Livestream cần chuẩn bị gì?', 'answer' => 'Cần xác định nền tảng phát, chất lượng đường truyền, số góc máy, âm thanh đầu vào và yêu cầu hiển thị.'],
                    ['question' => 'Có nhận riêng chụp ảnh hoặc quay phim không?', 'answer' => 'Có. THT cấu hình dịch vụ theo nhu cầu thực tế của từng chương trình.'],
                ],
                'seo_title' => 'Quay Phim Chụp Ảnh Sự Kiện Chuyên Nghiệp | THT Media',
                'seo_description' => 'Chụp ảnh, quay phim, flycam, livestream, dựng highlight và video sự kiện. Gửi lịch qua Zalo để kiểm tra ekip và nhận báo giá.',
                'show_header' => false,
                'show_footer' => false,
                'tracking_enabled' => true,
            ]);
            $this->publishNewLanding($landing);
            $landing->save();
        }

        $plans = [
            [
                'name' => 'Chụp ảnh sự kiện',
                'description' => 'Phù hợp khai trương, hội nghị, lễ ký kết, chương trình nội bộ và sự kiện cần bộ ảnh truyền thông.',
                'price' => null,
                'price_label' => 'Báo giá theo brief',
                'badge' => null,
                'features' => ['Chụp toàn cảnh', 'Khách mời và nghi thức', 'Hoạt động và ảnh thương hiệu', 'Hậu kỳ và bàn giao'],
            ],
            [
                'name' => 'Ảnh và video highlight',
                'description' => 'Dành cho doanh nghiệp cần vừa lưu giữ hình ảnh vừa có video tổng kết để đăng tải sau chương trình.',
                'price' => null,
                'price_label' => 'Báo giá theo brief',
                'badge' => 'Phổ biến',
                'features' => ['Ekip chụp ảnh', 'Quay video và thu âm cần thiết', 'Dựng highlight', 'Phiên bản phù hợp mạng xã hội'],
            ],
            [
                'name' => 'Media toàn diện',
                'description' => 'Dành cho sự kiện quy mô lớn, cần nhiều góc máy, flycam, trình chiếu hoặc livestream.',
                'price' => null,
                'price_label' => 'Báo giá theo quy mô',
                'badge' => null,
                'features' => ['Nhiều vị trí máy', 'Ekip ảnh và flycam', 'Livestream hoặc tín hiệu trình chiếu', 'Ghi hình, highlight và phiên bản truyền thông'],
            ],
        ];

        $this->seedPlans($landing, $plans);
        $landing->backstageProjects()->syncWithoutDetaching($this->eventProjectIds());
    }

    private function seedCatalogPages(): void
    {
        foreach (Landing07Catalog::pages() as $slug => $page) {
            $template = LandingTemplate::query()
                ->where('key', $page['template_key'])
                ->firstOrFail();
            $landing = $this->landing($slug, $page['title']);
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
            $landing->backstageProjects()->syncWithoutDetaching($this->catalogProjectIds($page['project_terms']));
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

        Landing::query()
            ->whereIn('template_key', $templateKeys)
            ->get()
            ->each(function (Landing $landing): void {
                $settings = is_array($landing->template_settings) ? $landing->template_settings : [];

                unset($settings['film_showreel_url']);

                $landing->forceFill([
                    'body' => null,
                    'template_settings' => $settings,
                ])->saveQuietly();
            });
    }

    private function landing(string $slug, string $title): Landing
    {
        return Landing::query()
            ->whereHas('slugs', fn ($query) => $query->where('slug', $slug))
            ->first() ?? new Landing(['title' => $title, 'slug' => $slug]);
    }

    private function publishNewLanding(Landing $landing): void
    {
        if (! $landing->exists) {
            $landing->status = 'published';
            $landing->published_at = now();
            $landing->sort_order = ((int) Landing::query()->max('sort_order')) + 1;
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
    private function seedPlans(Landing $landing, array $plans, ?string $priceUnit = null): void
    {
        foreach ($plans as $index => $plan) {
            $pricingPlan = PricingPlan::query()->firstOrNew([
                'landing_id' => $landing->id,
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
