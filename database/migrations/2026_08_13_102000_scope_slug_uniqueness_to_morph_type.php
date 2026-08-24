<?php

use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('slugs', function (Blueprint $table): void {
            $table->dropUnique(['slug', 'locale']);
            $table->unique(['slug', 'locale', 'sluggable_type']);
        });

        $this->restoreLegacySlugs('services_categories', ServiceCategory::class, 'legacy_term_id', 'terms');
        $this->restoreLegacySlugs('project_categories', ProjectCategory::class, 'legacy_term_id', 'terms');
        $this->restoreLegacySlugs('post_categories', PostCategory::class, 'legacy_term_id', 'terms');
        $this->restoreLegacySlugs('services', Service::class, 'legacy_content_item_id', 'content_items');
        $this->restoreLegacySlugs('projects', Project::class, 'legacy_content_item_id', 'content_items');
        $this->restoreLegacySlugs('posts', Post::class, 'legacy_content_item_id', 'content_items');
    }

    public function down(): void
    {
        Schema::table('slugs', function (Blueprint $table): void {
            $table->dropUnique(['slug', 'locale', 'sluggable_type']);
        });

        $this->makeSlugsGlobalUnique();

        Schema::table('slugs', function (Blueprint $table): void {
            $table->unique(['slug', 'locale']);
        });
    }

    /**
     * @param class-string<Model> $modelClass
     */
    private function restoreLegacySlugs(string $table, string $modelClass, string $legacyIdColumn, string $legacyTable): void
    {
        $type = (new $modelClass)->getMorphClass();

        DB::table($table)
            ->join($legacyTable, $legacyTable.'.id', '=', $table.'.'.$legacyIdColumn)
            ->orderBy($table.'.id')
            ->select($table.'.id', $legacyTable.'.slug')
            ->each(function (object $record) use ($type): void {
                DB::table('slugs')
                    ->where('sluggable_type', $type)
                    ->where('sluggable_id', $record->id)
                    ->where('locale', 'vi')
                    ->update(['slug' => $record->slug]);
            });
    }

    private function makeSlugsGlobalUnique(): void
    {
        $seen = [];

        DB::table('slugs')->orderBy('id')->each(function (object $record) use (&$seen): void {
            $key = $record->locale.':'.$record->slug;

            if (! isset($seen[$key])) {
                $seen[$key] = true;

                return;
            }

            $baseSlug = $record->slug;
            $suffix = 2;

            do {
                $slug = $baseSlug.'-'.$suffix;
                $suffix++;
                $key = $record->locale.':'.$slug;
            } while (isset($seen[$key]));

            DB::table('slugs')->where('id', $record->id)->update(['slug' => $slug]);
            $seen[$key] = true;
        });
    }
};
