<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name', 100);
            $table->string('native_name', 100);
            $table->string('og_locale', 20);
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_default')->default(false)->index();
            $table->boolean('is_indexable')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        foreach (['service_categories', 'project_categories', 'post_categories'] as $tableName) {
            Schema::create($tableName, function (Blueprint $table) use ($tableName): void {
                $table->id();
                $table->foreignId('seo_image_media_id')->nullable()->constrained('curator')->nullOnDelete();
                $table->string('name');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true)->index();
                $table->unsignedInteger('sort_order')->default(0)->index();

                if ($tableName === 'service_categories') {
                    $table->boolean('is_featured')->default(false)->index();
                    $table->boolean('is_home')->default(false)->index();
                }

                $table->timestamps();
            });
        }

        Schema::create('services', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('service_category_id')->nullable()->constrained('service_categories')->nullOnDelete();
            $table->foreignId('curator_media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->foreignId('banner_video_media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->foreignId('process_background_media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->foreignId('commitment_media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->json('gallery')->nullable();
            $table->json('backstage_gallery')->nullable();
            $table->string('faq_title')->nullable();
            $table->text('faq_description')->nullable();
            $table->json('faq_items')->nullable();
            $table->string('process_title')->nullable();
            $table->text('process_description')->nullable();
            $table->json('process_items')->nullable();
            $table->string('benefit_title')->nullable();
            $table->text('benefit_description')->nullable();
            $table->json('benefit_items')->nullable();
            $table->string('projects_title')->nullable();
            $table->string('stats_title')->nullable();
            $table->text('stats_description')->nullable();
            $table->json('stats_items')->nullable();
            $table->json('reference_videos')->nullable();
            $table->string('commitment_title')->nullable();
            $table->text('commitment_description')->nullable();
            $table->json('commitment_items')->nullable();
            $table->string('title');
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->string('status', 32)->default('draft')->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_home')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->text('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->foreignId('seo_image_media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_category_id')->nullable()->constrained('project_categories')->nullOnDelete();
            $table->foreignId('curator_media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->json('gallery')->nullable();
            $table->string('faq_title')->nullable();
            $table->text('faq_description')->nullable();
            $table->json('faq_items')->nullable();
            $table->string('title');
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
            $table->foreignId('seo_image_media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('curator_media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->string('title');
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->string('status', 32)->default('draft')->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->text('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->foreignId('seo_image_media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('landing_pages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('curator_media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->json('gallery')->nullable();
            $table->json('backstage_gallery')->nullable();
            $table->string('faq_title')->nullable();
            $table->text('faq_description')->nullable();
            $table->json('faq_items')->nullable();
            $table->json('sections')->nullable();
            $table->boolean('show_header')->default(true);
            $table->boolean('show_footer')->default(true);
            $table->string('title');
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->string('status', 32)->default('draft')->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->text('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->foreignId('seo_image_media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('hero_slides', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('curator_media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->string('video_source', 20)->nullable();
            $table->string('video_url', 1024)->nullable();
            $table->foreignId('video_media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->string('eyebrow')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('primary_label')->nullable();
            $table->string('primary_url')->nullable();
            $table->string('secondary_label')->nullable();
            $table->string('secondary_url')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('hero_slide_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('hero_slide_id')->constrained('hero_slides')->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('eyebrow')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('primary_label')->nullable();
            $table->string('primary_url')->nullable();
            $table->string('secondary_label')->nullable();
            $table->string('secondary_url')->nullable();
            $table->timestamps();
            $table->unique(['hero_slide_id', 'locale']);
        });

        Schema::create('about_departments', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('about_team_members', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('about_department_id')->constrained('about_departments')->cascadeOnDelete();
            $table->foreignId('media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->string('name');
            $table->string('position')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['about_department_id', 'is_active', 'sort_order'], 'about_team_members_visibility_order_index');
        });

        Schema::create('partners', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('curator_media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->string('name');
            $table->string('website_url')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('testimonials', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('curator_media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->string('client_name');
            $table->string('client_role')->nullable();
            $table->string('company_name')->nullable();
            $table->text('quote');
            $table->unsignedTinyInteger('rating')->nullable()->default(5);
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('intros', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('icon')->nullable();
            $table->foreignId('curator_media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->longText('content')->nullable();
            $table->string('link')->nullable();
            $table->string('link_text')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(false);
            $table->string('kind')->default('article')->index();
            $table->text('summary')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('keywords')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('menus', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable()->index();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('menu_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('menu_id')->constrained('menus')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->cascadeOnDelete();
            $table->unsignedBigInteger('linked_source_id')->nullable();
            $table->string('linked_source_type', 64)->nullable();
            $table->string('label');
            $table->text('url')->nullable();
            $table->string('target', 32)->nullable();
            $table->text('css_classes')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
            $table->index(['menu_id', 'parent_id', 'position']);
        });

        Schema::create('redirects', function (Blueprint $table): void {
            $table->id();
            $table->string('source', 32)->default('manual');
            $table->string('from_path', 512)->unique();
            $table->text('to_url');
            $table->unsignedSmallInteger('status_code')->default(301);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('slugs', function (Blueprint $table): void {
            $table->id();
            $table->string('slug');
            $table->morphs('sluggable');
            $table->string('locale', 10)->default('vi');
            $table->timestamps();
            $table->unique(['slug', 'locale']);
            $table->unique(['sluggable_type', 'sluggable_id', 'locale']);
        });

        Schema::create('post_post_category', function (Blueprint $table): void {
            $table->foreignId('post_id')->constrained('posts')->cascadeOnDelete();
            $table->foreignId('post_category_id')->constrained('post_categories')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->primary(['post_id', 'post_category_id']);
        });

        Schema::create('project_service', function (Blueprint $table): void {
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['project_id', 'service_id']);
        });

        Schema::create('post_project', function (Blueprint $table): void {
            $table->foreignId('post_id')->constrained('posts')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['post_id', 'project_id']);
        });

        Schema::create('landing_page_project', function (Blueprint $table): void {
            $table->foreignId('landing_page_id')->constrained('landing_pages')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->primary(['landing_page_id', 'project_id']);
        });

        Schema::create('landing_page_service_category', function (Blueprint $table): void {
            $table->foreignId('landing_page_id')->constrained('landing_pages')->cascadeOnDelete();
            $table->foreignId('service_category_id')->constrained('service_categories')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->primary(['landing_page_id', 'service_category_id']);
        });

        Schema::create('landing_page_service', function (Blueprint $table): void {
            $table->foreignId('landing_page_id')->constrained('landing_pages')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->primary(['landing_page_id', 'service_id']);
        });

        Schema::create('landing_page_post', function (Blueprint $table): void {
            $table->foreignId('landing_page_id')->constrained('landing_pages')->cascadeOnDelete();
            $table->foreignId('post_id')->constrained('posts')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->primary(['landing_page_id', 'post_id']);
        });

        Schema::create('pricing_plans', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('landing_page_id')->nullable()->constrained('landing_pages')->nullOnDelete();
            $table->string('name');
            $table->string('badge')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('price')->nullable();
            $table->string('price_label')->nullable();
            $table->string('price_unit')->nullable();
            $table->json('features')->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('service_pricings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('service_id')->unique()->constrained('services')->cascadeOnDelete();
            $table->string('title')->default('Bảng giá dịch vụ');
            $table->text('description')->nullable();
            $table->foreignId('source_media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->text('source_url')->nullable();
            $table->longText('source_json')->nullable();
            $table->json('comparison_rows')->nullable();
            $table->timestamps();
        });

        Schema::create('pricing_packages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('service_pricing_id')->constrained('service_pricings')->cascadeOnDelete();
            $table->string('name');
            $table->string('badge')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('list_price')->nullable();
            $table->string('price_label')->nullable();
            $table->string('price_unit')->nullable();
            $table->string('promotion_type', 32)->nullable();
            $table->decimal('promotion_value', 12, 2)->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('pricing_package_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pricing_package_id')->constrained('pricing_packages')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('contact_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->foreignId('landing_page_id')->nullable()->constrained('landing_pages')->nullOnDelete();
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

        Schema::create('comments', function (Blueprint $table): void {
            $table->id();
            $table->morphs('commentable');
            $table->foreignId('parent_id')->nullable()->constrained('comments')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('author_name', 120);
            $table->string('author_email')->nullable();
            $table->text('body');
            $table->unsignedTinyInteger('rating')->nullable();
            $table->string('status', 32)->default('pending')->index();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->index(['commentable_type', 'commentable_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
        Schema::dropIfExists('contact_requests');
        Schema::dropIfExists('pricing_package_items');
        Schema::dropIfExists('pricing_packages');
        Schema::dropIfExists('service_pricings');
        Schema::dropIfExists('pricing_plans');
        Schema::dropIfExists('landing_page_post');
        Schema::dropIfExists('landing_page_service');
        Schema::dropIfExists('landing_page_service_category');
        Schema::dropIfExists('landing_page_project');
        Schema::dropIfExists('post_project');
        Schema::dropIfExists('project_service');
        Schema::dropIfExists('post_post_category');
        Schema::dropIfExists('slugs');
        Schema::dropIfExists('redirects');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('intros');
        Schema::dropIfExists('about_team_members');
        Schema::dropIfExists('about_departments');
        Schema::dropIfExists('hero_slide_translations');
        Schema::dropIfExists('hero_slides');
        Schema::dropIfExists('landing_pages');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('services');
        Schema::dropIfExists('post_categories');
        Schema::dropIfExists('project_categories');
        Schema::dropIfExists('service_categories');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('partners');
        Schema::dropIfExists('languages');
    }
};
