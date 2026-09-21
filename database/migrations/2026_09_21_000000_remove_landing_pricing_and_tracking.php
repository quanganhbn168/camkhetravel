<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('settings')) {
            DB::table('settings')->where('group', 'tracking')->delete();
        }

        foreach (['comments' => 'commentable_type', 'faqs' => 'faqable_type', 'slugs' => 'sluggable_type', 'taggables' => 'taggable_type'] as $table => $typeColumn) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, $typeColumn)) {
                DB::table($table)->where($typeColumn, 'landing-page')->delete();
            }
        }

        if (Schema::hasTable('contact_requests') && Schema::hasColumn('contact_requests', 'landing_page_id')) {
            Schema::table('contact_requests', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('landing_page_id');
            });
        }

        foreach ([
            'pricing_package_items',
            'pricing_packages',
            'service_pricings',
            'pricing_plans',
            'landing_page_post',
            'landing_page_project',
            'landing_page_service',
            'landing_page_service_category',
            'landing_pages',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }

    public function down(): void
    {
        throw new \RuntimeException('Landing, pricing and tracking removal is intentionally irreversible. Restore from a database backup to recover removed content.');
    }
};
