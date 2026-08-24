<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_items', function (Blueprint $table): void {
            $table->id();
            $table->string('source', 32)->default('native');
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('type', 64)->index();
            $table->string('status', 32)->default('draft')->index();
            $table->text('title');
            $table->string('slug')->index();
            $table->string('canonical_path', 512)->nullable()->index();
            $table->text('legacy_url')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('content_items')->nullOnDelete();
            $table->unsignedBigInteger('parent_source_id')->nullable();
            $table->unsignedBigInteger('author_source_id')->nullable();
            $table->integer('menu_order')->default(0);
            $table->longText('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->json('content_data')->nullable();
            $table->unsignedBigInteger('featured_media_source_id')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamp('content_modified_at')->nullable();

            $table->text('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_canonical_url')->nullable();
            $table->json('seo_robots')->nullable();
            $table->text('focus_keyword')->nullable();
            $table->unsignedSmallInteger('seo_score')->nullable();
            $table->boolean('is_pillar_content')->default(false);
            $table->boolean('exclude_from_sitemap')->default(false);
            $table->text('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->text('og_image_url')->nullable();
            $table->text('twitter_title')->nullable();
            $table->text('twitter_description')->nullable();
            $table->text('twitter_image_url')->nullable();
            $table->json('structured_data')->nullable();
            $table->string('seo_source', 32)->default('template');
            $table->boolean('needs_seo_review')->default(false)->index();
            $table->json('legacy_meta')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['source', 'source_id']);
            $table->index(['type', 'status', 'published_at']);
        });

        Schema::create('taxonomies', function (Blueprint $table): void {
            $table->id();
            $table->string('source', 32)->default('native');
            $table->string('key', 96);
            $table->string('label')->nullable();
            $table->boolean('is_hierarchical')->default(false);
            $table->boolean('is_public')->default(true);
            $table->timestamps();
            $table->unique(['source', 'key']);
        });

        Schema::create('terms', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('taxonomy_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('name');
            $table->string('slug')->index();
            $table->longText('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('terms')->nullOnDelete();
            $table->unsignedBigInteger('parent_source_id')->nullable();
            $table->unsignedInteger('source_count')->default(0);
            $table->json('legacy_meta')->nullable();
            $table->timestamps();
            $table->unique(['taxonomy_id', 'source_id']);
            $table->index(['taxonomy_id', 'slug']);
        });

        Schema::create('content_item_term', function (Blueprint $table): void {
            $table->foreignId('content_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('position')->default(0);
            $table->primary(['content_item_id', 'term_id']);
        });

        Schema::create('media_assets', function (Blueprint $table): void {
            $table->id();
            $table->string('source', 32)->default('native');
            $table->unsignedBigInteger('source_id')->nullable();
            $table->unsignedBigInteger('parent_source_id')->nullable();
            $table->string('slug')->nullable()->index();
            $table->text('title')->nullable();
            $table->text('alt_text')->nullable();
            $table->longText('caption')->nullable();
            $table->longText('description')->nullable();
            $table->string('mime_type', 191)->nullable()->index();
            $table->text('source_url')->nullable();
            $table->text('source_path')->nullable();
            $table->string('disk', 64)->nullable();
            $table->text('file_path')->nullable();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->unique(['source', 'source_id']);
        });

        Schema::create('menus', function (Blueprint $table): void {
            $table->id();
            $table->string('source', 32)->default('native');
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('name');
            $table->string('location')->nullable()->index();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['source', 'source_id']);
        });

        Schema::create('menu_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->cascadeOnDelete();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->unsignedBigInteger('parent_source_id')->nullable();
            $table->unsignedBigInteger('linked_source_id')->nullable();
            $table->string('linked_source_type', 64)->nullable();
            $table->string('label');
            $table->text('url')->nullable();
            $table->string('target', 32)->nullable();
            $table->text('css_classes')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->json('legacy_meta')->nullable();
            $table->timestamps();
            $table->unique(['menu_id', 'source_id']);
        });

        Schema::create('redirects', function (Blueprint $table): void {
            $table->id();
            $table->string('source', 32)->default('manual');
            $table->string('from_path', 512)->unique();
            $table->text('to_url');
            $table->unsignedSmallInteger('status_code')->default(301);
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedBigInteger('hits')->default(0);
            $table->timestamp('last_hit_at')->nullable();
            $table->timestamps();
        });

        Schema::create('site_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('group', 96)->default('general');
            $table->string('key', 191);
            $table->string('type', 32)->default('string');
            $table->longText('value')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();
            $table->unique(['group', 'key']);
        });

        Schema::create('import_runs', function (Blueprint $table): void {
            $table->id();
            $table->string('source', 32);
            $table->string('status', 32)->default('running')->index();
            $table->timestamp('started_at');
            $table->timestamp('finished_at')->nullable();
            $table->json('counts')->nullable();
            $table->json('warnings')->nullable();
            $table->longText('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_runs');
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('redirects');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('media_assets');
        Schema::dropIfExists('content_item_term');
        Schema::dropIfExists('terms');
        Schema::dropIfExists('taxonomies');
        Schema::dropIfExists('content_items');
    }
};
