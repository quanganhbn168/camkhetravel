<?php

namespace App\Support\Landing;

/** Canonical route, view and asset registry for the native Landing pages. */
final class LandingRegistry
{
    public const ADS = 'landing_ads';

    public const WEDDING = 'landing_wedding';

    public const CORPORATE_FILM = 'landing_corporate_film';

    public const COMMUNICATIONS = 'landing_communications';

    public const ACADEMY = 'landing_academy';

    public const ACADEMY_V2 = 'landing_academy_v2';

    public const OUTSOURCED_MARKETING = 'landing_outsourced_marketing';

    public const EVENT_MEDIA = 'landing_event_media';

    public const PROFILE = 'landing_profile';

    public const EVENT_ORGANIZATION = 'landing_event_organization';

    public const BRANDING = 'landing_branding';

    public const TIKTOK = 'landing_tiktok';

    /** @var array<string, array{slug: string, label: string, view: string, css: string, js?: string, aliases?: list<string>}> */
    private const PAGES = [
        self::TIKTOK => [
            'slug' => 'xay-kenh-tiktok',
            'label' => 'Xây kênh TikTok',
            'view' => 'frontend.landing.pages.tiktok',
            'css' => 'resources/css/landing/pages/tiktok.css',
            'js' => 'resources/js/landing/pages/tiktok.js',
        ],
        self::BRANDING => [
            'slug' => 'bo-nhan-dien-thuong-hieu',
            'label' => 'Bộ nhận diện thương hiệu',
            'view' => 'frontend.landing.pages.branding',
            'shell' => 'frontend.landing.branding-shell',
            'css' => 'resources/css/landing/pages/branding.css',
            'js' => 'resources/js/landing/pages/branding.js',
        ],
        self::ADS => [
            'slug' => 'dich-vu-quang-cao-truc-tuyen-cho-doanh-nghiep',
            'label' => 'Dịch vụ chạy Ads',
            'view' => 'frontend.landing.pages.ads',
            'aliases' => ['san-xuat-video-va-chay-quang-cao-facebook'],
            'css' => 'resources/css/landing/pages/ads.css',
            'js' => 'resources/js/landing/pages/ads.js',
        ],
        self::WEDDING => [
            'slug' => 'dich-vu-quay-chup-phong-su-cuoi-chat-luong-cao',
            'label' => 'Dịch vụ cưới',
            'view' => 'frontend.landing.pages.wedding',
            'aliases' => ['phong-su-cuoi'],
            'css' => 'resources/css/landing/pages/wedding.css',
            'js' => 'resources/js/landing/pages/wedding.js',
        ],
        self::CORPORATE_FILM => [
            'slug' => 'dich-vu-san-xuat-phim-doanh-nghiep',
            'label' => 'Sản xuất phim doanh nghiệp',
            'view' => 'frontend.landing.pages.corporate-film',
            'aliases' => ['san-xuat-phim-doanh-nghiep'],
            'css' => 'resources/css/landing/pages/corporate-film.css',
            'js' => 'resources/js/landing/pages/corporate-film.js',
        ],
        self::COMMUNICATIONS => [
            'slug' => 'giai-phap-truyen-thong-doanh-nghiep',
            'label' => 'Giải pháp truyền thông doanh nghiệp',
            'view' => 'frontend.landing.pages.communications',
            'css' => 'resources/css/landing/pages/communications.css',
            'js' => 'resources/js/landing/pages/communications.js',
        ],
        self::ACADEMY => [
            'slug' => 'hoc-vien-nhiep-anh-va-sang-tao-noi-dung',
            'label' => 'Học viện Nhiếp ảnh',
            'view' => 'frontend.landing.pages.academy',
            'aliases' => ['khoa-hoc-nhiep-anh-thuc-chien'],
            'css' => 'resources/css/landing/pages/academy.css',
            'js' => 'resources/js/landing/pages/academy.js',
        ],
        self::ACADEMY_V2 => [
            'slug' => 'khoa-hoc-nhiep-anh',
            'label' => 'Học viện Nhiếp ảnh – Version 2',
            'view' => 'frontend.landing.pages.academy-v2',
            'css' => 'resources/css/landing/pages/academy-v2.css',
        ],
        self::OUTSOURCED_MARKETING => [
            'slug' => 'tht-media-phong-marketing-thue-ngoai-gia-re',
            'label' => 'Phòng Marketing thuê ngoài',
            'view' => 'frontend.landing.pages.outsourced-marketing',
            'aliases' => ['phong-marketing-thue-ngoai'],
            'css' => 'resources/css/landing/pages/outsourced-marketing.css',
            'js' => 'resources/js/landing/pages/outsourced-marketing.js',
        ],
        self::EVENT_MEDIA => [
            'slug' => 'quay-phim-chup-anh-su-kien-tai-bac-ninh',
            'label' => 'Quay phim, chụp ảnh sự kiện',
            'view' => 'frontend.landing.pages.event-media',
            'aliases' => ['quay-chup-live-su-kien-chuong-trinh'],
            'css' => 'resources/css/landing/pages/event-media.css',
            'js' => 'resources/js/landing/pages/event-media.js',
        ],
        self::PROFILE => [
            'slug' => 'thiet-ke-profile-doanh-nghiep-ho-so-nang-luc',
            'label' => 'Thiết kế profile doanh nghiệp',
            'view' => 'frontend.landing.pages.profile',
            'aliases' => ['thiet-ke-profile-doanh-nghiep'],
            'css' => 'resources/css/landing/pages/profile.css',
            'js' => 'resources/js/landing/pages/profile.js',
        ],
        self::EVENT_ORGANIZATION => [
            'slug' => 'to-chuc-su-kien-tron-goi-chuyen-nghiep',
            'label' => 'Tổ chức sự kiện trọn gói',
            'view' => 'frontend.landing.pages.event-organization',
            'aliases' => [
                'to-chuc-su-kien-tron-goi',
                'to-chuc-su-kien-tron-goi-cho-doanh-nghiep-chuyen-nghiep-tai-bac-ninh',
            ],
            'css' => 'resources/css/landing/pages/event-organization.css',
            'js' => 'resources/js/landing/pages/event-organization.js',
        ],
    ];

    /** @var array<string, array{description: string, use_case: string, palette: array{primary: string, accent: string, surface: string, ink: string}, icon: string, sort_order: int}> */
    private const TEMPLATE_META = [
        self::TIKTOK => [
            'description' => 'Xây kênh TikTok với dự án, video theo ngành và bảng giá điện thoại / máy quay.',
            'use_case' => 'Xây kênh · Nội dung · Quay dựng · Quản trị',
            'palette' => ['primary' => '#fe2c55', 'accent' => '#25f4ee', 'surface' => '#1a1a1a', 'ink' => '#ffffff'],
            'icon' => 'heroicon-o-play-circle',
            'sort_order' => 140,
        ],
        self::BRANDING => [
            'description' => 'Bộ nhận diện thương hiệu giai đoạn 1: profile, website, hình ảnh và phim doanh nghiệp.',
            'use_case' => 'Profile · Website · Hình ảnh · Video',
            'palette' => ['primary' => '#075333', 'accent' => '#d8aa50', 'surface' => '#f5f9f7', 'ink' => '#17232b'],
            'icon' => 'heroicon-o-squares-2x2',
            'sort_order' => 140,
        ],
        self::CORPORATE_FILM => [
            'description' => 'Landing sản xuất phim doanh nghiệp với hero điện ảnh, dự án, bảng giá và CTA tư vấn.',
            'use_case' => 'Hero video · Showreel · Dự án · Bảng giá',
            'palette' => ['primary' => '#0b633a', 'accent' => '#f28c28', 'surface' => '#f3f0e8', 'ink' => '#071b14'],
            'icon' => 'heroicon-o-video-camera',
            'sort_order' => 40,
        ],
        self::EVENT_MEDIA => [
            'description' => 'Landing quay chụp sự kiện với bộ đầu ra, gallery, cấu hình ekip và CTA kiểm tra lịch.',
            'use_case' => 'Ảnh · Video · Flycam · Livestream · Highlight',
            'palette' => ['primary' => '#071b34', 'accent' => '#f3b548', 'surface' => '#f5f7fa', 'ink' => '#071b34'],
            'icon' => 'heroicon-o-camera',
            'sort_order' => 50,
        ],
        self::ADS => [
            'description' => 'Landing quảng cáo trực tuyến theo hành trình audit, chiến lược, tối ưu và báo cáo.',
            'use_case' => 'Quảng cáo · Audit · Tối ưu · Báo cáo',
            'palette' => ['primary' => '#101828', 'accent' => '#f97316', 'surface' => '#fff7ed', 'ink' => '#111827'],
            'icon' => 'heroicon-o-chart-bar',
            'sort_order' => 60,
        ],
        self::WEDDING => [
            'description' => 'Landing giàu hình ảnh cho dịch vụ quay chụp phóng sự cưới.',
            'use_case' => 'Chụp ảnh · Video · Phóng sự cưới',
            'palette' => ['primary' => '#2f1b2e', 'accent' => '#d89a65', 'surface' => '#fbf4ef', 'ink' => '#261a24'],
            'icon' => 'heroicon-o-heart',
            'sort_order' => 70,
        ],
        self::COMMUNICATIONS => [
            'description' => 'Landing giải pháp truyền thông doanh nghiệp theo chiến lược, sản xuất media và quảng cáo.',
            'use_case' => 'Chiến lược · Media · Quảng cáo · Case study',
            'palette' => ['primary' => '#12372a', 'accent' => '#d8a84e', 'surface' => '#f4f7ef', 'ink' => '#14251d'],
            'icon' => 'heroicon-o-megaphone',
            'sort_order' => 80,
        ],
        self::ACADEMY => [
            'description' => 'Landing khóa học nhiếp ảnh thực chiến theo lộ trình học, thực hành và portfolio.',
            'use_case' => 'Khóa học · Thực hành · Portfolio',
            'palette' => ['primary' => '#202b44', 'accent' => '#e1ad5b', 'surface' => '#f4f5f8', 'ink' => '#172033'],
            'icon' => 'heroicon-o-academic-cap',
            'sort_order' => 90,
        ],
        self::ACADEMY_V2 => [
            'description' => 'Phiên bản Academy V2 theo chương trình 12 buổi, workshop, thực hành và đầu ra portfolio.',
            'use_case' => '12 buổi · 10 tuần · Portfolio',
            'palette' => ['primary' => '#172b35', 'accent' => '#e6a84d', 'surface' => '#f5f6f1', 'ink' => '#132229'],
            'icon' => 'heroicon-o-academic-cap',
            'sort_order' => 100,
        ],
        self::OUTSOURCED_MARKETING => [
            'description' => 'Landing phòng marketing thuê ngoài theo hệ thống vận hành, KPI và tối ưu.',
            'use_case' => 'Chiến lược · Nội dung · Media · Ads · SEO',
            'palette' => ['primary' => '#123c3d', 'accent' => '#f2b84b', 'surface' => '#eef7f4', 'ink' => '#102d2d'],
            'icon' => 'heroicon-o-users',
            'sort_order' => 110,
        ],
        self::PROFILE => [
            'description' => 'Landing thiết kế profile theo nội dung, hình ảnh, thiết kế và file bàn giao.',
            'use_case' => 'Nội dung · Hình ảnh · Thiết kế · Hồ sơ năng lực',
            'palette' => ['primary' => '#263b35', 'accent' => '#d49c4b', 'surface' => '#f5f4ee', 'ink' => '#202c28'],
            'icon' => 'heroicon-o-document-text',
            'sort_order' => 120,
        ],
        self::EVENT_ORGANIZATION => [
            'description' => 'Landing tổ chức sự kiện theo master plan, sản xuất và vận hành hiện trường.',
            'use_case' => 'Concept · Kịch bản · Sản xuất · Vận hành',
            'palette' => ['primary' => '#182c47', 'accent' => '#efb04f', 'surface' => '#f2f5f8', 'ink' => '#122238'],
            'icon' => 'heroicon-o-calendar-days',
            'sort_order' => 130,
        ],
    ];

    /** @return array<string, array{slug: string, label: string, view: string, css: string, js?: string, aliases?: list<string>}> */
    public static function pages(): array
    {
        return self::PAGES;
    }

    /** @return array<string, array<string, mixed>> */
    public static function templateDefinitions(): array
    {
        $definitions = [];

        foreach (self::PAGES as $key => $page) {
            $meta = self::TEMPLATE_META[$key];
            $settings = [
                'landing_brand_label' => 'THT MEDIA',
                'landing_service_line' => $meta['use_case'],
                'landing_nav_cta' => 'Nhận tư vấn',
                'landing_footer_text' => 'THT Media · Đồng hành từ mục tiêu đến đầu ra',
            ];

            $definitions[$key] = [
                'label' => 'Landing / '.$page['label'],
                'description' => $meta['description'],
                'view' => 'frontend.landing.shell',
                'css_class' => 'landing-page--'.str_replace('_', '-', $key),
                'css_source' => $page['css'],
                'use_case' => $meta['use_case'],
                'palette' => $meta['palette'],
                'settings' => $settings,
                'settings_schema' => [
                    'title' => 'Thiết lập '.$page['label'],
                    'description' => 'Nhận diện và CTA chung của landing. Nội dung trình bày được lưu trong database.',
                    'icon' => $meta['icon'],
                    'columns' => 2,
                    'fields' => [
                        ['key' => 'landing_brand_label', 'type' => 'text', 'label' => 'Nhãn thương hiệu', 'max_length' => 120, 'full_width' => true],
                        ['key' => 'landing_service_line', 'type' => 'text', 'label' => 'Dòng dịch vụ', 'max_length' => 255, 'full_width' => true],
                        ['key' => 'landing_nav_cta', 'type' => 'text', 'label' => 'Nhãn CTA đầu trang', 'max_length' => 100],
                        ['key' => 'landing_footer_text', 'type' => 'textarea', 'label' => 'Dòng chân trang', 'max_length' => 255, 'rows' => 2, 'full_width' => true],
                    ],
                ],
                'default_sections' => [],
                'source_name' => 'Landing THT Laravel',
                'source_path' => 'database/seeders/data/landing/'.$key.'.json',
                'icon' => $meta['icon'],
                'is_active' => true,
                'sort_order' => $meta['sort_order'],
            ];
        }

        return $definitions;
    }

    /** @return array{slug: string, label: string, view: string, css: string, js?: string, aliases?: list<string>}|null */
    public static function find(?string $templateKey): ?array
    {
        return self::PAGES[$templateKey ?? ''] ?? null;
    }

    public static function templateForSlug(?string $slug): ?string
    {
        foreach (self::PAGES as $templateKey => $page) {
            if ($page['slug'] === $slug || in_array($slug, $page['aliases'] ?? [], true)) {
                return $templateKey;
            }
        }

        return null;
    }

    /** @return list<string> */
    public static function slugsForTemplate(?string $templateKey): array
    {
        $page = self::find($templateKey);

        return $page
            ? array_values(array_unique([$page['slug'], ...($page['aliases'] ?? [])]))
            : [];
    }

    /** @return list<string> */
    public static function viteAssets(?string $templateKey): array
    {
        $page = self::find($templateKey);
        if (! $page) {
            return ['resources/css/app.css', 'resources/js/app.js'];
        }

        return array_values(array_filter([
            'resources/css/app.css',
            $page['css'],
            'resources/js/app.js',
            $page['js'] ?? null,
        ]));
    }

    /** @return list<array{label: string, url: string}> */
    public static function footerLinks(): array
    {
        $order = [
            self::CORPORATE_FILM,
            self::EVENT_ORGANIZATION,
            self::EVENT_MEDIA,
            self::OUTSOURCED_MARKETING,
            self::ACADEMY,
            self::COMMUNICATIONS,
            self::PROFILE,
            self::WEDDING,
            self::ADS,
            self::TIKTOK,
        ];

        return array_map(static fn (string $key): array => [
            'label' => self::PAGES[$key]['label'],
            'url' => url('/'.self::PAGES[$key]['slug']),
        ], $order);
    }

    public static function assetUrl(string $path): string
    {
        if ($path === '' || preg_match('#^(?:https?:)?//#i', $path) || in_array($path[0] ?? '', ['#', '/'], true) || preg_match('#^(?:data:|mailto:|tel:)#i', $path)) {
            return $path;
        }

        $normalized = str_replace('\\', '/', ltrim($path, '/'));
        if (str_starts_with($normalized, 'landing/')) {
            $normalized = substr($normalized, strlen('landing/'));
        }

        return asset('storage/media/landing/pages/'.implode('/', array_map('rawurlencode', explode('/', $normalized))));
    }
}
