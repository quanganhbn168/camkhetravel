<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const RESERVED_SLUGS = [
        'dich-vu', 'du-an', 'gioi-thieu', 'lien-he', 'tin-tuc',
        'sitemap.xml', 'robots.txt', '404-not-found', 'search',
        'under-construction', 'test', 'blog', 'danh-muc-dich-vu',
        'danh-muc-du-an', 'landing-cate',
    ];

    public function up(): void
    {
        Schema::table('slugs', function (Blueprint $table): void {
            $table->dropUnique(['slug', 'locale', 'sluggable_type']);
        });

        $this->makeSlugsGloballyUnique();

        Schema::table('slugs', function (Blueprint $table): void {
            $table->unique(['slug', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::table('slugs', function (Blueprint $table): void {
            $table->dropUnique(['slug', 'locale']);
            $table->unique(['slug', 'locale', 'sluggable_type']);
        });
    }

    private function makeSlugsGloballyUnique(): void
    {
        $seen = [];
        foreach (self::RESERVED_SLUGS as $slug) {
            $seen['vi:'.$slug] = true;
        }

        DB::table('slugs')
            ->orderByRaw("CASE sluggable_type
                WHEN 'service' THEN 1
                WHEN 'project' THEN 2
                WHEN 'post' THEN 3
                WHEN 'service-category' THEN 4
                WHEN 'project-category' THEN 5
                WHEN 'post-category' THEN 6
                ELSE 9 END")
            ->orderBy('id')
            ->each(function (object $record) use (&$seen): void {
                $baseSlug = $record->slug;
                $slug = $baseSlug;
                $suffix = 2;
                $key = $record->locale.':'.$slug;

                while (isset($seen[$key])) {
                    $slug = $baseSlug.'-'.$suffix;
                    $suffix++;
                    $key = $record->locale.':'.$slug;
                }

                if ($slug !== $record->slug) {
                    DB::table('slugs')->where('id', $record->id)->update(['slug' => $slug]);
                }

                $seen[$key] = true;
            });
    }
};
