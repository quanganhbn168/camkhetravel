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
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('service_categories', 'services_categories');

        Schema::create('slugs', function (Blueprint $table): void {
            $table->id();
            $table->string('slug');
            $table->morphs('sluggable');
            $table->string('locale', 10)->default('vi');
            $table->timestamps();

            $table->unique(['slug', 'locale']);
        });

        $this->copySlugs('services_categories', ServiceCategory::class, 'name');
        $this->copySlugs('project_categories', ProjectCategory::class, 'name');
        $this->copySlugs('post_categories', PostCategory::class, 'name');
        $this->copySlugs('services', Service::class, 'title');
        $this->copySlugs('projects', Project::class, 'title');
        $this->copySlugs('posts', Post::class, 'title');

        foreach (['services_categories', 'project_categories', 'post_categories', 'services', 'projects', 'posts'] as $table) {
            Schema::table($table, fn (Blueprint $table) => $table->dropColumn('slug'));
        }

        Schema::dropIfExists('pages');
    }

    public function down(): void
    {
        foreach (['services_categories', 'project_categories', 'post_categories', 'services', 'projects', 'posts'] as $table) {
            Schema::table($table, fn (Blueprint $table) => $table->string('slug')->nullable());
        }

        $this->restoreSlugs('services_categories', ServiceCategory::class);
        $this->restoreSlugs('project_categories', ProjectCategory::class);
        $this->restoreSlugs('post_categories', PostCategory::class);
        $this->restoreSlugs('services', Service::class);
        $this->restoreSlugs('projects', Project::class);
        $this->restoreSlugs('posts', Post::class);

        foreach (['services_categories', 'project_categories', 'post_categories', 'services', 'projects', 'posts'] as $table) {
            Schema::table($table, fn (Blueprint $table) => $table->unique('slug'));
        }

        Schema::create('pages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('legacy_content_item_id')->nullable()->unique()->constrained('content_items')->nullOnDelete();
            $table->foreignId('legacy_media_asset_id')->nullable()->constrained('media_assets')->nullOnDelete();
            $table->foreignId('curator_media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('template', 64)->default('standard');
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->string('status', 32)->default('draft')->index();
            $table->text('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::dropIfExists('slugs');
        Schema::rename('services_categories', 'service_categories');
    }

    /**
     * @param class-string<Model> $modelClass
     */
    private function copySlugs(string $table, string $modelClass, string $sourceColumn): void
    {
        $model = new $modelClass;
        $type = $model->getMorphClass();

        DB::table($table)->orderBy('id')->each(function (object $record) use ($sourceColumn, $type): void {
            $baseSlug = Str::slug((string) ($record->slug ?: $record->{$sourceColumn}));

            if ($baseSlug === '') {
                return;
            }

            DB::table('slugs')->insert([
                'slug' => $this->uniqueSlug($baseSlug),
                'sluggable_type' => $type,
                'sluggable_id' => $record->id,
                'locale' => 'vi',
                'created_at' => $record->created_at ?: now(),
                'updated_at' => $record->updated_at ?: now(),
            ]);
        });
    }

    /**
     * @param class-string<Model> $modelClass
     */
    private function restoreSlugs(string $table, string $modelClass): void
    {
        $type = (new $modelClass)->getMorphClass();

        DB::table($table)->orderBy('id')->each(function (object $record) use ($table, $type): void {
            $slug = DB::table('slugs')
                ->where('sluggable_type', $type)
                ->where('sluggable_id', $record->id)
                ->where('locale', 'vi')
                ->value('slug');

            DB::table($table)->where('id', $record->id)->update(['slug' => $slug]);
        });
    }

    private function uniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug;
        $suffix = 2;

        while (DB::table('slugs')->where('slug', $slug)->where('locale', 'vi')->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
};
