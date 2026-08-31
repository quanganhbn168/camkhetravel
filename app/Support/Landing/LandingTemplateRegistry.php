<?php

namespace App\Support\Landing;

use App\Models\LandingTemplate;
use Illuminate\Support\Facades\Schema;
use Throwable;

final class LandingTemplateRegistry
{
    public const ANNIVERSARY = 'anniversary_campaign';

    public const CONVERSION = 'conversion_offer';

    public const PORTFOLIO = 'portfolio_showcase';

    public const CORPORATE_FILM = 'landing07_corporate_film';

    public const EVENT_MEDIA = 'landing07_event_media';

    /**
     * @return array<string, array{
     *     label: string,
     *     description: string,
     *     view: string,
     *     css_class: string,
     *     css_source: string,
     *     use_case: string,
     *     palette: array{primary: string, accent: string, surface: string, ink: string},
     *     settings: array<string, mixed>
     * }>
     */
    private static function blueprints(): array
    {
        return [
            self::ANNIVERSARY => [
                'label' => 'Tri ân / Sự kiện',
                'description' => 'Phong cách xanh – vàng sang trọng, phù hợp chương trình kỷ niệm, khai trương và ưu đãi có countdown.',
                'view' => 'frontend.landing-pages.templates.anniversary-campaign',
                'css_class' => 'landing-page--anniversary',
                'css_source' => 'resources/css/landing-templates/anniversary.css',
                'use_case' => 'Countdown · Quyền lợi · Bảng giá · Form đăng ký',
                'palette' => [
                    'primary' => '#075c35',
                    'accent' => '#d6aa43',
                    'surface' => '#f7f4e9',
                    'ink' => '#10291d',
                ],
                'settings' => [
                    'anniversary_brand_label' => 'THT MEDIA',
                    'anniversary_nav_cta' => 'Nhận hỗ trợ',
                    'anniversary_notice' => 'Chương trình tri ân dành cho khách hàng THT Media',
                    'anniversary_footer_text' => 'THT Media · Đồng hành cùng doanh nghiệp',
                ],
            ],
            self::CONVERSION => [
                'label' => 'Chuyển đổi / Báo giá',
                'description' => 'Tương phản mạnh, CTA nổi bật và form được ưu tiên cho quảng cáo, dịch vụ hoặc chiến dịch thu lead.',
                'view' => 'frontend.landing-pages.templates.conversion-offer',
                'css_class' => 'landing-page--conversion',
                'css_source' => 'resources/css/landing-templates/conversion-offer.css',
                'use_case' => 'Hero bán hàng · Gói giá · Bằng chứng · Form thu lead',
                'palette' => [
                    'primary' => '#111827',
                    'accent' => '#f97316',
                    'surface' => '#fff7ed',
                    'ink' => '#111827',
                ],
                'settings' => [
                    'conversion_brand_label' => 'THT MEDIA / GROWTH',
                    'conversion_badge' => 'Giải pháp theo nhu cầu thực tế',
                    'conversion_nav_cta' => 'Nhận báo giá',
                    'conversion_proof_text' => 'Tư vấn rõ phạm vi trước khi triển khai',
                    'conversion_footer_text' => 'Sẵn sàng trao đổi mục tiêu của anh/chị?',
                ],
            ],
            self::PORTFOLIO => [
                'label' => 'Dự án / Hồ sơ năng lực',
                'description' => 'Bố cục editorial giàu hình ảnh, dành cho case study, dự án tiêu biểu và hồ sơ năng lực theo chiến dịch.',
                'view' => 'frontend.landing-pages.templates.portfolio-showcase',
                'css_class' => 'landing-page--portfolio',
                'css_source' => 'resources/css/landing-templates/portfolio-showcase.css',
                'use_case' => 'Dự án · Gallery · Nội dung dài · CTA liên hệ',
                'palette' => [
                    'primary' => '#233127',
                    'accent' => '#b7cf63',
                    'surface' => '#f0ede4',
                    'ink' => '#1b211d',
                ],
                'settings' => [
                    'portfolio_brand_label' => 'THT MEDIA / SELECTED WORKS',
                    'portfolio_issue_label' => 'Hồ sơ năng lực',
                    'portfolio_nav_cta' => 'Trao đổi dự án',
                    'portfolio_intro' => 'Những dự án được chọn lọc để thể hiện cách THT Media tiếp cận và triển khai công việc.',
                    'portfolio_footer_text' => 'THT Media · Selected works',
                ],
            ],
            self::CORPORATE_FILM => [
                'label' => 'Landing07 / Phim doanh nghiệp',
                'description' => 'Template Laravel cho landing sản xuất phim: hero điện ảnh, dự án, bảng giá và CTA tư vấn.',
                'view' => 'frontend.landing-pages.templates.landing07-corporate-film',
                'css_class' => 'landing-page--landing07-film',
                'css_source' => 'resources/css/landing-templates/landing07-corporate-film.css',
                'use_case' => 'Hero video · Showreel · Dự án · Bảng giá · Form tư vấn',
                'palette' => [
                    'primary' => '#0b633a',
                    'accent' => '#f28c28',
                    'surface' => '#f3f0e8',
                    'ink' => '#071b14',
                ],
                'settings' => [
                    'film_brand_label' => 'THT FILMS',
                    'film_service_line' => 'Company Profile · TVC · Video thương hiệu · Video nhà máy',
                    'film_hero_video_media_id' => null,
                    'film_showreel_media_id' => null,
                    'film_showreel_label' => 'Xem showreel',
                    'film_nav_cta' => 'Nhận tư vấn miễn phí',
                    'film_quote' => 'Mỗi doanh nghiệp đều có một câu chuyện riêng. Điều tạo nên khác biệt là cách câu chuyện ấy được kể.',
                    'film_footer_text' => 'THT Films · Sản xuất hình ảnh tạo dựng niềm tin',
                ],
            ],
            self::EVENT_MEDIA => [
                'label' => 'Landing07 / Quay chụp sự kiện',
                'description' => 'Template Laravel cho landing event media: hero contact-sheet, lưới đầu ra và bảng giá theo cấu hình ekip.',
                'view' => 'frontend.landing-pages.templates.landing07-event-media',
                'css_class' => 'landing-page--landing07-event',
                'css_source' => 'resources/css/landing-templates/landing07-event-media.css',
                'use_case' => 'Hero sự kiện · Bộ đầu ra · Gallery · Gói ekip · Dự án',
                'palette' => [
                    'primary' => '#071b34',
                    'accent' => '#f3b548',
                    'surface' => '#f5f7fa',
                    'ink' => '#071b34',
                ],
                'settings' => [
                    'event_brand_label' => 'THT EVENT MEDIA',
                    'event_service_line' => 'Ảnh · Video · Flycam · Livestream · Highlight',
                    'event_hero_video_media_id' => null,
                    'event_nav_cta' => 'Gửi lịch sự kiện',
                    'event_brief_label' => 'Gửi lịch sự kiện',
                    'event_brief_title' => 'Kiểm tra ekip còn trống',
                    'event_brief_text' => 'Gửi ngày, địa điểm, thời lượng và nhu cầu ảnh, video hoặc livestream.',
                    'event_brief_items' => [
                        'Ngày và thời lượng tổ chức',
                        'Địa điểm, quy mô chương trình',
                        'Nhu cầu ảnh, video, flycam hoặc livestream',
                    ],
                    'event_caption' => 'Hình ảnh để sự kiện tiếp tục tạo giá trị',
                    'event_footer_text' => 'THT Media · Ghi đúng khoảnh khắc, hoàn thiện đúng đầu ra',
                ],
            ],
        ];
    }

    /** @return array<string, array<string, mixed>> */
    public static function all(): array
    {
        if (! self::databaseReady()) {
            return self::seedDefinitions();
        }

        try {
            $definitions = [];

            foreach (LandingTemplate::query()->active()->orderBy('sort_order')->orderBy('id')->get() as $template) {
                $definitions[$template->key] = $template->toRegistryDefinition();
            }

            return $definitions;
        } catch (Throwable) {
            return self::seedDefinitions();
        }
    }

    /** @return array<string, array<string, mixed>> */
    public static function seedDefinitions(): array
    {
        $metadata = self::metadata();
        $schemas = self::settingsSchemas();
        $definitions = [];

        foreach (self::blueprints() as $key => $definition) {
            $definitions[$key] = $definition + [
                'source_name' => $metadata[$key]['source_name'],
                'source_path' => $metadata[$key]['source_path'],
                'icon' => $metadata[$key]['icon'],
                'settings_schema' => $schemas[$key],
                'default_sections' => self::blueprintSections($key),
                'is_active' => true,
                'sort_order' => $metadata[$key]['sort_order'],
            ];
        }

        return array_merge($definitions, Landing07Catalog::templates());
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::all())
            ->mapWithKeys(fn (array $template, string $key): array => [$key => $template['label']])
            ->all();
    }

    /** @return array<string, mixed>|null */
    public static function find(?string $key): ?array
    {
        if (blank($key)) {
            return null;
        }

        if (self::databaseReady()) {
            try {
                return LandingTemplate::query()->where('key', $key)->first()?->toRegistryDefinition();
            } catch (Throwable) {
                // Fall back to the code blueprint while migrations are running.
            }
        }

        return self::seedDefinitions()[$key] ?? null;
    }

    public static function id(?string $key): ?int
    {
        $id = self::find($key)['id'] ?? null;

        return is_numeric($id) ? (int) $id : null;
    }

    /** @return array{primary: string, accent: string, surface: string, ink: string} */
    public static function palette(?string $key): array
    {
        return self::find($key)['palette'] ?? self::seedDefinitions()[self::ANNIVERSARY]['palette'];
    }

    /** @return array<string, mixed> */
    public static function defaultSettings(?string $key): array
    {
        return self::find($key)['settings'] ?? [];
    }

    /** @return array<string, mixed> */
    public static function settingsSchema(?string $key): array
    {
        return self::find($key)['settings_schema'] ?? [];
    }

    /** @return list<array{type: string, data: array<string, mixed>}> */
    public static function defaultSections(?string $key): array
    {
        $sections = self::find($key)['default_sections'] ?? [];

        return is_array($sections) ? array_values($sections) : [];
    }

    /** @return list<array{type: string, data: array<string, mixed>}> */
    private static function blueprintSections(?string $key): array
    {
        return match ($key) {
            self::CORPORATE_FILM => [
                [
                    'type' => 'hero',
                    'data' => [
                        'block_id' => 'hero',
                        'eyebrow' => 'Dịch vụ sản xuất phim doanh nghiệp',
                        'title' => 'Khi khách hàng chưa đến doanh nghiệp, hãy để bộ phim kể câu chuyện thay bạn.',
                        'subtitle' => 'THT Media đồng hành từ mục tiêu, kịch bản, ghi hình đến hậu kỳ để tạo nên tư liệu có giá trị sử dụng lâu dài.',
                        'cta_label' => 'Nhận tư vấn miễn phí',
                        'cta_url' => '#tu-van',
                        'secondary_label' => 'Xem dự án',
                        'secondary_url' => '#du-an',
                    ],
                ],
                [
                    'type' => 'benefits',
                    'data' => [
                        'block_id' => 'gia-tri-bo-phim',
                        'eyebrow' => 'Giá trị bộ phim',
                        'title' => 'Một bộ phim doanh nghiệp có thể giúp bạn',
                        'items' => [
                            ['title' => 'Tạo dựng niềm tin', 'description' => 'Thể hiện trực quan quy mô, năng lực và câu chuyện thương hiệu.', 'features' => ['Khẳng định năng lực doanh nghiệp', 'Tăng sự tin tưởng từ khách hàng và đối tác']],
                            ['title' => 'Dùng lâu dài', 'description' => 'Tạo nguồn tư liệu cho website, bán hàng, tuyển dụng và truyền thông.', 'features' => ['Tối ưu cho nhiều nền tảng', 'Hỗ trợ đội ngũ kinh doanh']],
                        ],
                    ],
                ],
                ['type' => 'projects', 'data' => ['block_id' => 'du-an', 'eyebrow' => 'Năng lực sản xuất', 'title' => 'Các sản phẩm tiêu biểu', 'limit' => 6]],
                ['type' => 'pricing', 'data' => ['block_id' => 'bang-gia', 'eyebrow' => 'Phương án sản xuất', 'title' => 'Gói dịch vụ theo mục tiêu và phạm vi']],
                ['type' => 'gallery', 'data' => ['block_id' => 'hau-truong', 'eyebrow' => 'Behind the scenes', 'title' => 'Hình ảnh ekip và quá trình sản xuất']],
                ['type' => 'faqs', 'data' => ['block_id' => 'cau-hoi', 'eyebrow' => 'Thông tin cần biết', 'title' => 'Câu hỏi về sản xuất phim doanh nghiệp']],
                ['type' => 'lead_form', 'data' => ['block_id' => 'tu-van', 'title' => 'Trao đổi ý tưởng cùng THT Films', 'description' => 'Cho THT biết mục tiêu, bối cảnh và thời điểm dự kiến để nhận phương án phù hợp.', 'button_label' => 'Gửi yêu cầu tư vấn']],
            ],
            self::EVENT_MEDIA => [
                [
                    'type' => 'hero',
                    'data' => [
                        'block_id' => 'hero',
                        'eyebrow' => 'Dịch vụ quay chụp sự kiện',
                        'title' => 'Quay phim – chụp ảnh sự kiện',
                        'subtitle' => 'Sự kiện chỉ diễn ra một lần. THT Media chuẩn bị shot-list, ghi lại toàn cảnh, nghi thức, cảm xúc và nhận diện thương hiệu theo đúng kênh sử dụng.',
                        'cta_label' => 'Gửi lịch – kiểm tra ekip',
                        'cta_url' => '#tu-van',
                        'secondary_label' => 'Xem bộ đầu ra',
                        'secondary_url' => '#bo-dau-ra',
                    ],
                ],
                [
                    'type' => 'benefits',
                    'data' => [
                        'block_id' => 'bo-dau-ra',
                        'eyebrow' => 'Bộ đầu ra',
                        'title' => 'Không chỉ giao file – giao bộ tài nguyên có thể sử dụng ngay',
                        'items' => [
                            ['title' => 'Ảnh và video sự kiện', 'description' => 'Tư liệu được chọn lọc theo diễn biến, nghi thức và nhận diện thương hiệu.', 'features' => ['Album ảnh hậu kỳ', 'Video ghi hình và highlight']],
                            ['title' => 'Đa nền tảng', 'description' => 'Cấu hình thêm flycam, livestream hoặc phiên bản video dọc khi cần.', 'features' => ['Reels, TikTok và Zalo', 'Flycam hoặc livestream']],
                        ],
                    ],
                ],
                ['type' => 'gallery', 'data' => ['block_id' => 'tu-lieu', 'eyebrow' => 'Bốn góc nhìn', 'title' => 'Toàn cảnh, nghi thức, cảm xúc và thương hiệu']],
                ['type' => 'pricing', 'data' => ['block_id' => 'bang-gia', 'eyebrow' => 'Phương án dịch vụ', 'title' => 'Cấu hình ekip theo quy mô và mục tiêu sử dụng']],
                ['type' => 'projects', 'data' => ['block_id' => 'du-an', 'eyebrow' => 'Dự án tham khảo', 'title' => 'Dấu ấn của từng chương trình', 'limit' => 6]],
                ['type' => 'faqs', 'data' => ['block_id' => 'cau-hoi', 'eyebrow' => 'Câu hỏi thường gặp', 'title' => 'Chuẩn bị ekip quay chụp']],
                ['type' => 'lead_form', 'data' => ['block_id' => 'tu-van', 'title' => 'Đừng đợi tới sát ngày mới tìm ekip quay chụp', 'description' => 'Gửi lịch dự kiến để THT kiểm tra nhân sự, thiết bị và đề xuất số lượng góc máy phù hợp.', 'button_label' => 'Kiểm tra lịch ekip']],
            ],
            default => [],
        };
    }

    private static function databaseReady(): bool
    {
        try {
            return Schema::hasTable('landing_templates');
        } catch (Throwable) {
            return false;
        }
    }

    /** @return array<string, array{source_name: string, source_path: string|null, icon: string, sort_order: int}> */
    private static function metadata(): array
    {
        return [
            self::ANNIVERSARY => [
                'source_name' => 'THT Laravel landing builder',
                'source_path' => null,
                'icon' => 'heroicon-o-sparkles',
                'sort_order' => 10,
            ],
            self::CONVERSION => [
                'source_name' => 'THT Laravel landing builder',
                'source_path' => null,
                'icon' => 'heroicon-o-cursor-arrow-rays',
                'sort_order' => 20,
            ],
            self::PORTFOLIO => [
                'source_name' => 'THT Laravel landing builder',
                'source_path' => null,
                'icon' => 'heroicon-o-photo',
                'sort_order' => 30,
            ],
            self::CORPORATE_FILM => [
                'source_name' => 'Landing07 source snapshot · Laravel catalog',
                'source_path' => 'landing-07/dichvulamphimdoanhnghiep/data.php',
                'icon' => 'heroicon-o-video-camera',
                'sort_order' => 40,
            ],
            self::EVENT_MEDIA => [
                'source_name' => 'Landing07 source snapshot · Laravel catalog',
                'source_path' => 'landing-07/quaychupsukien/data.php',
                'icon' => 'heroicon-o-camera',
                'sort_order' => 50,
            ],
        ];
    }

    /** @return array<string, array<string, mixed>> */
    private static function settingsSchemas(): array
    {
        return [
            self::ANNIVERSARY => self::schema(
                'Thiết lập template Tri ân / Sự kiện',
                'Các trường chỉ thuộc template xanh – vàng. Nội dung block vẫn quản lý ở phần Các khối nội dung.',
                'heroicon-o-sparkles',
                [
                    self::field('anniversary_brand_label', 'text', 'Tên thương hiệu trên thanh đầu trang', maxLength: 120, fullWidth: true),
                    self::field('anniversary_nav_cta', 'text', 'Nhãn CTA đầu trang', maxLength: 100),
                    self::field('anniversary_notice', 'text', 'Nhãn chương trình', maxLength: 180),
                    self::field('anniversary_footer_text', 'textarea', 'Dòng chân trang', maxLength: 255, rows: 2, fullWidth: true),
                ],
            ),
            self::CONVERSION => self::schema(
                'Thiết lập template Chuyển đổi / Báo giá',
                'Tập trung CTA, gói giá và form thu lead cho chiến dịch quảng cáo.',
                'heroicon-o-cursor-arrow-rays',
                [
                    self::field('conversion_brand_label', 'text', 'Tên chiến dịch / thương hiệu', maxLength: 120, fullWidth: true),
                    self::field('conversion_badge', 'text', 'Nhãn nổi bật', maxLength: 180),
                    self::field('conversion_nav_cta', 'text', 'Nhãn CTA đầu trang', maxLength: 100),
                    self::field('conversion_proof_text', 'textarea', 'Thông điệp tạo tin cậy', maxLength: 300, rows: 2, fullWidth: true),
                    self::field('conversion_footer_text', 'textarea', 'Dòng chốt cuối trang', maxLength: 255, rows: 2, fullWidth: true),
                ],
            ),
            self::PORTFOLIO => self::schema(
                'Thiết lập template Dự án / Hồ sơ năng lực',
                'Bố cục editorial dành cho dự án, gallery và nội dung giới thiệu năng lực.',
                'heroicon-o-photo',
                [
                    self::field('portfolio_brand_label', 'text', 'Tên bộ sưu tập / thương hiệu', maxLength: 150, fullWidth: true),
                    self::field('portfolio_issue_label', 'text', 'Nhãn ấn phẩm', maxLength: 100),
                    self::field('portfolio_nav_cta', 'text', 'Nhãn CTA đầu trang', maxLength: 100),
                    self::field('portfolio_intro', 'textarea', 'Lời dẫn đầu trang', maxLength: 600, rows: 3, fullWidth: true),
                    self::field('portfolio_footer_text', 'textarea', 'Dòng chân trang', maxLength: 255, rows: 2, fullWidth: true),
                ],
            ),
            self::CORPORATE_FILM => self::schema(
                'Thiết lập Landing07 / Phim doanh nghiệp',
                'Schema Laravel cho landing sản xuất phim: hero video, showreel và CTA dự án.',
                'heroicon-o-video-camera',
                [
                    self::field('film_brand_label', 'text', 'Tên thương hiệu trên đầu trang', maxLength: 120, fullWidth: true),
                    self::field('film_service_line', 'text', 'Dòng loại hình sản xuất', maxLength: 255),
                    self::field('film_hero_video_media_id', 'media', 'Video nền hero', helperText: 'Ưu tiên MP4 hoặc WebM ngắn, không âm thanh; ảnh của block Hero được dùng làm poster dự phòng.', fullWidth: true, mediaRole: 'hero_video'),
                    self::field('film_showreel_media_id', 'media', 'Video showreel local', helperText: 'Chỉ chọn video đã lưu trong kho media Laravel. Để trống sẽ dùng nút xem dự án.', fullWidth: true, mediaRole: 'showreel'),
                    self::field('film_showreel_label', 'text', 'Nhãn nút showreel', maxLength: 100),
                    self::field('film_nav_cta', 'text', 'Nhãn CTA đầu trang', maxLength: 100),
                    self::field('film_quote', 'textarea', 'Thông điệp điện ảnh', maxLength: 500, rows: 3, fullWidth: true),
                    self::field('film_footer_text', 'textarea', 'Dòng chân trang', maxLength: 255, rows: 2, fullWidth: true),
                ],
            ),
            self::EVENT_MEDIA => self::schema(
                'Thiết lập Landing07 / Quay chụp sự kiện',
                'Schema chuyển thể từ landing Event Media: hero tư liệu, brief kiểm tra ekip và lưới đầu ra.',
                'heroicon-o-camera',
                [
                    self::field('event_brand_label', 'text', 'Tên thương hiệu trên đầu trang', maxLength: 120, fullWidth: true),
                    self::field('event_service_line', 'text', 'Dòng loại hình dịch vụ', maxLength: 255),
                    self::field('event_hero_video_media_id', 'media', 'Video nền hero', helperText: 'Nếu không chọn video, template dùng ảnh của block Hero làm nền.', fullWidth: true, mediaRole: 'hero_video'),
                    self::field('event_nav_cta', 'text', 'Nhãn CTA đầu trang', maxLength: 100),
                    self::field('event_brief_label', 'text', 'Nhãn khung brief', maxLength: 100),
                    self::field('event_brief_title', 'text', 'Tiêu đề khung brief', maxLength: 180, fullWidth: true),
                    self::field('event_brief_text', 'textarea', 'Hướng dẫn gửi brief', maxLength: 500, rows: 3, fullWidth: true),
                    self::field('event_brief_items', 'tags', 'Thông tin khách hàng cần gửi', helperText: 'Nhập một ý rồi nhấn Enter.', fullWidth: true),
                    self::field('event_caption', 'text', 'Chú thích cuối hero', maxLength: 255, fullWidth: true),
                    self::field('event_footer_text', 'textarea', 'Dòng chân trang', maxLength: 255, rows: 2, fullWidth: true),
                ],
            ),
        ];
    }

    /** @param list<array<string, mixed>> $fields */
    private static function schema(string $title, string $description, string $icon, array $fields): array
    {
        return compact('title', 'description', 'icon', 'fields') + ['columns' => 2];
    }

    /** @return array<string, mixed> */
    private static function field(
        string $key,
        string $type,
        string $label,
        ?int $maxLength = null,
        ?int $rows = null,
        ?string $helperText = null,
        bool $fullWidth = false,
        ?string $mediaRole = null,
    ): array {
        return array_filter([
            'key' => $key,
            'type' => $type,
            'label' => $label,
            'max_length' => $maxLength,
            'rows' => $rows,
            'helper_text' => $helperText,
            'full_width' => $fullWidth,
            'media_role' => $mediaRole,
        ], fn (mixed $value): bool => $value !== null);
    }
}
