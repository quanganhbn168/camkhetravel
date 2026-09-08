<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLES = ['landing_pages', 'services', 'projects', 'posts', 'service_categories', 'project_categories', 'post_categories'];

    public function up(): void
    {
        foreach (self::TABLES as $name) {
            Schema::table($name, function (Blueprint $table): void {
                $table->foreignId('seo_image_media_id')->nullable()->constrained('curator')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        foreach (array_reverse(self::TABLES) as $name) {
            Schema::table($name, function (Blueprint $table): void {
                $table->dropConstrainedForeignId('seo_image_media_id');
            });
        }
    }
};
