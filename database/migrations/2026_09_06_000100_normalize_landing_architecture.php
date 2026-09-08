<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** @var array<string, string> */
    private const TEMPLATE_KEY_MAP = [
        'landing07_ads' => 'landing_ads',
        'landing07_wedding' => 'landing_wedding',
        'landing07_corporate_film' => 'landing_corporate_film',
        'landing07_communications' => 'landing_communications',
        'landing07_academy' => 'landing_academy',
        'landing07_academy_v2' => 'landing_academy_v2',
        'landing07_outsourced_marketing' => 'landing_outsourced_marketing',
        'landing07_event_media' => 'landing_event_media',
        'landing07_profile' => 'landing_profile',
        'landing07_event_organization' => 'landing_event_organization',
    ];

    /** @var array<string, list<string>> */
    private const TEMPLATE_SLUGS = [
        'landing_ads' => ['dich-vu-quang-cao-truc-tuyen-cho-doanh-nghiep', 'san-xuat-video-va-chay-quang-cao-facebook'],
        'landing_wedding' => ['dich-vu-quay-chup-phong-su-cuoi-chat-luong-cao', 'phong-su-cuoi'],
        'landing_corporate_film' => ['dich-vu-san-xuat-phim-doanh-nghiep', 'san-xuat-phim-doanh-nghiep'],
        'landing_communications' => ['giai-phap-truyen-thong-doanh-nghiep'],
        'landing_academy' => ['hoc-vien-nhiep-anh-va-sang-tao-noi-dung', 'khoa-hoc-nhiep-anh-thuc-chien'],
        'landing_academy_v2' => ['khoa-hoc-nhiep-anh'],
        'landing_outsourced_marketing' => ['tht-media-phong-marketing-thue-ngoai-gia-re', 'phong-marketing-thue-ngoai'],
        'landing_event_media' => ['quay-phim-chup-anh-su-kien-tai-bac-ninh', 'quay-chup-live-su-kien-chuong-trinh'],
        'landing_profile' => ['thiet-ke-profile-doanh-nghiep-ho-so-nang-luc', 'thiet-ke-profile-doanh-nghiep'],
        'landing_event_organization' => ['to-chuc-su-kien-tron-goi-chuyen-nghiep', 'to-chuc-su-kien-tron-goi', 'to-chuc-su-kien-tron-goi-cho-doanh-nghiep-chuyen-nghiep-tai-bac-ninh'],
    ];

    /** @var array<string, string> */
    private const TEMPLATE_LABELS = [
        'landing_ads' => 'Landing / Dịch vụ chạy Ads',
        'landing_wedding' => 'Landing / Dịch vụ cưới',
        'landing_corporate_film' => 'Landing / Sản xuất phim doanh nghiệp',
        'landing_communications' => 'Landing / Giải pháp truyền thông doanh nghiệp',
        'landing_academy' => 'Landing / Học viện Nhiếp ảnh',
        'landing_academy_v2' => 'Landing / Học viện Nhiếp ảnh – Version 2',
        'landing_outsourced_marketing' => 'Landing / Phòng Marketing thuê ngoài',
        'landing_event_media' => 'Landing / Quay phim, chụp ảnh sự kiện',
        'landing_profile' => 'Landing / Thiết kế profile doanh nghiệp',
        'landing_event_organization' => 'Landing / Tổ chức sự kiện trọn gói',
    ];

    public function up(): void
    {
        if (! Schema::hasColumn('landing_pages', 'landing_content')) {
            Schema::table('landing_pages', function (Blueprint $table): void {
                $table->json('landing_content')->nullable();
            });
        }

        if (! Schema::hasColumn('services', 'landing_content')) {
            Schema::table('services', function (Blueprint $table): void {
                $table->json('landing_content')->nullable();
            });
        }

        $this->renameTemplateKeys(self::TEMPLATE_KEY_MAP);
        $this->hydrateContent('landing_pages', 'landing-page');
        $this->hydrateContent('services', 'service');
        $this->normalizeTemplateMetadata();
    }

    public function down(): void
    {
        $this->renameTemplateKeys(array_flip(self::TEMPLATE_KEY_MAP));

        if (Schema::hasColumn('landing_pages', 'landing_content')) {
            Schema::table('landing_pages', function (Blueprint $table): void {
                $table->dropColumn('landing_content');
            });
        }

        if (Schema::hasColumn('services', 'landing_content')) {
            Schema::table('services', function (Blueprint $table): void {
                $table->dropColumn('landing_content');
            });
        }
    }

    /** @param array<string, string> $map */
    private function renameTemplateKeys(array $map): void
    {
        foreach ($map as $from => $to) {
            if (Schema::hasTable('landing_pages')) {
                DB::table('landing_pages')->where('template_key', $from)->update(['template_key' => $to]);
            }

            if (! Schema::hasTable('landing_templates')) {
                continue;
            }

            $fromId = DB::table('landing_templates')->where('key', $from)->value('id');
            $toId = DB::table('landing_templates')->where('key', $to)->value('id');

            if (! $fromId) {
                continue;
            }

            if ($toId) {
                DB::table('landing_pages')->where('landing_template_id', $fromId)->update(['landing_template_id' => $toId]);
                DB::table('landing_templates')->where('id', $fromId)->delete();
            } else {
                DB::table('landing_templates')->where('id', $fromId)->update(['key' => $to]);
            }
        }
    }

    private function hydrateContent(string $table, string $morphType): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasTable('slugs')) {
            return;
        }

        foreach (self::TEMPLATE_SLUGS as $templateKey => $slugs) {
            $path = database_path('seeders/data/landing/'.$templateKey.'.json');

            if (! is_file($path)) {
                continue;
            }

            $payload = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
            $content = is_array($payload['content'] ?? null) ? $payload['content'] : [];
            $ids = DB::table('slugs')
                ->where('sluggable_type', $morphType)
                ->whereIn('slug', $slugs)
                ->pluck('sluggable_id');

            if ($ids->isNotEmpty()) {
                DB::table($table)
                    ->whereIn('id', $ids)
                    ->whereNull('landing_content')
                    ->update([
                        'landing_content' => json_encode($content, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    ]);
            }
        }
    }

    private function normalizeTemplateMetadata(): void
    {
        if (! Schema::hasTable('landing_templates')) {
            return;
        }

        foreach (array_keys(self::TEMPLATE_SLUGS) as $templateKey) {
            $pageName = str_replace('_', '-', substr($templateKey, strlen('landing_')));

            DB::table('landing_templates')->where('key', $templateKey)->update([
                'view_name' => 'frontend.landing.shell',
                'css_class' => 'landing-page--'.$pageName,
                'css_source' => 'resources/css/landing/pages/'.$pageName.'.css',
                'source_name' => 'Landing THT Laravel',
                'source_path' => 'database/seeders/data/landing/'.$templateKey.'.json',
            ]);

            DB::table('landing_templates')
                ->where('key', $templateKey)
                ->where('name', 'like', 'Landing07%')
                ->update(['name' => self::TEMPLATE_LABELS[$templateKey]]);
        }
    }
};
