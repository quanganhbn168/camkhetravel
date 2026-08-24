<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_categories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('legacy_term_id')->nullable()->unique()->constrained('terms')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('project_categories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('legacy_term_id')->nullable()->unique()->constrained('terms')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('post_categories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('legacy_term_id')->nullable()->unique()->constrained('terms')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('service_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('legacy_content_item_id')->nullable()->unique()->constrained('content_items')->nullOnDelete();
            $table->foreignId('legacy_media_asset_id')->nullable()->constrained('media_assets')->nullOnDelete();
            $table->foreignId('curator_media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->string('status', 32)->default('draft')->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->text('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('legacy_content_item_id')->nullable()->unique()->constrained('content_items')->nullOnDelete();
            $table->foreignId('legacy_media_asset_id')->nullable()->constrained('media_assets')->nullOnDelete();
            $table->foreignId('curator_media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('client_name')->nullable();
            $table->string('industry')->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->string('video_url', 1024)->nullable();
            $table->date('completed_at')->nullable();
            $table->string('status', 32)->default('draft')->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->text('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('legacy_content_item_id')->nullable()->unique()->constrained('content_items')->nullOnDelete();
            $table->foreignId('legacy_media_asset_id')->nullable()->constrained('media_assets')->nullOnDelete();
            $table->foreignId('curator_media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->string('status', 32)->default('draft')->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->text('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('post_post_category', function (Blueprint $table): void {
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('post_category_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->primary(['post_id', 'post_category_id']);
        });

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

        Schema::create('contact_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 32)->nullable();
            $table->string('company')->nullable();
            $table->string('budget')->nullable();
            $table->string('timeline')->nullable();
            $table->text('message');
            $table->string('status', 32)->default('new')->index();
            $table->timestamp('contacted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_requests');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('post_post_category');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('services');
        Schema::dropIfExists('post_categories');
        Schema::dropIfExists('project_categories');
        Schema::dropIfExists('service_categories');
    }
};
