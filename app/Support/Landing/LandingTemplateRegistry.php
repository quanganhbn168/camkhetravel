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
                'view' => 'frontend.landing.templates.anniversary-campaign',
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
                'view' => 'frontend.landing.templates.conversion-offer',
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
                'view' => 'frontend.landing.templates.portfolio-showcase',
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

        return array_merge($definitions, LandingRegistry::templateDefinitions());
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
        return [];
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
