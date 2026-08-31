<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $customLandingIds = DB::table('landings')
            ->where('layout_mode', 'custom_template')
            ->pluck('id')
            ->map(fn (mixed $id): int => (int) $id)
            ->all();
        $serviceLandingIds = DB::table('landings')
            ->whereNotIn('id', $customLandingIds ?: [0])
            ->pluck('id')
            ->map(fn (mixed $id): int => (int) $id)
            ->all();

        $this->dropForeignIfExists('pricing_plans', 'landing_id');
        $this->dropForeignIfExists('contact_requests', 'landing_id');
        $this->dropForeignIfExists('landing_events', 'landing_id');

        if (Schema::hasTable('landing_categories')) {
            Schema::rename('landing_categories', 'service_categories');
        }

        if (Schema::hasTable('landing_project')) {
            Schema::rename('landing_project', 'landing_content_project_source');
        }

        Schema::rename('landings', 'landing_content_source');
        $this->dropAllForeignKeys('landing_content_source');
        $this->dropAllForeignKeys('landing_content_project_source');

        $this->createServicesTable();
        $this->createLandingPagesTable();

        DB::table('landing_content_source')
            ->whereIn('id', $serviceLandingIds ?: [0])
            ->orderBy('id')
            ->get()
            ->each(function (object $record): void {
                DB::table('services')->insert([
                    'id' => $record->id,
                    'service_category_id' => $record->landing_category_id,
                    'curator_media_id' => $record->curator_media_id,
                    'pricing_media_id' => $record->pricing_media_id,
                    'gallery' => $record->gallery,
                    'backstage_gallery' => $record->backstage_gallery,
                    'faq_title' => $record->faq_title,
                    'faq_description' => $record->faq_description,
                    'faq_items' => $record->faq_items,
                    'title' => $record->title,
                    'excerpt' => $record->excerpt,
                    'body' => $record->body,
                    'status' => $record->status,
                    'is_featured' => $record->is_featured,
                    'sort_order' => $record->sort_order,
                    'seo_title' => $record->seo_title,
                    'seo_description' => $record->seo_description,
                    'published_at' => $record->published_at,
                    'created_at' => $record->created_at,
                    'updated_at' => $record->updated_at,
                ]);
            });

        DB::table('landing_content_source')
            ->whereIn('id', $customLandingIds ?: [0])
            ->orderBy('id')
            ->get()
            ->each(function (object $record): void {
                DB::table('landing_pages')->insert([
                    'id' => $record->id,
                    'curator_media_id' => $record->curator_media_id,
                    'gallery' => $record->gallery,
                    'backstage_gallery' => $record->backstage_gallery,
                    'faq_title' => $record->faq_title,
                    'faq_description' => $record->faq_description,
                    'faq_items' => $record->faq_items,
                    'layout_mode' => $record->layout_mode,
                    'template_key' => $record->template_key,
                    'landing_template_id' => $record->landing_template_id,
                    'template_settings' => $record->template_settings,
                    'sections' => $record->sections,
                    'theme_settings' => $record->theme_settings,
                    'campaign_starts_at' => $record->campaign_starts_at,
                    'campaign_ends_at' => $record->campaign_ends_at,
                    'expired_behavior' => $record->expired_behavior,
                    'expired_message' => $record->expired_message,
                    'show_header' => $record->show_header,
                    'show_footer' => $record->show_footer,
                    'tracking_enabled' => $record->tracking_enabled,
                    'title' => $record->title,
                    'excerpt' => $record->excerpt,
                    'body' => $record->body,
                    'status' => $record->status,
                    'is_featured' => $record->is_featured,
                    'sort_order' => $record->sort_order,
                    'seo_title' => $record->seo_title,
                    'seo_description' => $record->seo_description,
                    'published_at' => $record->published_at,
                    'created_at' => $record->created_at,
                    'updated_at' => $record->updated_at,
                ]);
            });

        $this->createLandingPageRelations($customLandingIds);
        $this->splitPricingPlans($customLandingIds, $serviceLandingIds);
        $this->splitContactRequests($customLandingIds, $serviceLandingIds);
        $this->splitLandingEvents($customLandingIds, $serviceLandingIds);

        DB::table('slugs')
            ->where('sluggable_type', 'landing')
            ->whereIn('sluggable_id', $serviceLandingIds ?: [0])
            ->update(['sluggable_type' => 'service']);
        DB::table('slugs')
            ->where('sluggable_type', 'landing')
            ->whereIn('sluggable_id', $customLandingIds ?: [0])
            ->update(['sluggable_type' => 'landing-page']);
        DB::table('slugs')
            ->where('sluggable_type', 'landing-category')
            ->update(['sluggable_type' => 'service-category']);

        DB::table('comments')
            ->where('commentable_type', 'landing')
            ->whereIn('commentable_id', $serviceLandingIds ?: [0])
            ->update(['commentable_type' => 'service']);
        DB::table('comments')
            ->where('commentable_type', 'landing')
            ->whereIn('commentable_id', $customLandingIds ?: [0])
            ->update(['commentable_type' => 'landing-page']);

        DB::table('menu_items')
            ->where('linked_source_type', 'App\\Models\\Landing')
            ->whereIn('linked_source_id', $serviceLandingIds ?: [0])
            ->update(['linked_source_type' => 'App\\Models\\Service']);
        DB::table('menu_items')
            ->where('linked_source_type', 'App\\Models\\Landing')
            ->whereIn('linked_source_id', $customLandingIds ?: [0])
            ->update(['linked_source_type' => 'App\\Models\\LandingPage']);

        Schema::dropIfExists('landing_content_project_source');
        Schema::dropIfExists('landing_content_source');
    }

    public function down(): void
    {
        throw new LogicException('The service and landing-page split is irreversible. Restore the database backup instead.');
    }

    private function createServicesTable(): void
    {
        Schema::create('services', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('service_category_id')->nullable()->constrained('service_categories')->nullOnDelete();
            $table->foreignId('curator_media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->foreignId('pricing_media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->json('gallery')->nullable();
            $table->json('backstage_gallery')->nullable();
            $table->string('faq_title')->nullable();
            $table->text('faq_description')->nullable();
            $table->json('faq_items')->nullable();
            $table->string('title');
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
    }

    private function createLandingPagesTable(): void
    {
        Schema::create('landing_pages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('curator_media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->json('gallery')->nullable();
            $table->json('backstage_gallery')->nullable();
            $table->string('faq_title')->nullable();
            $table->text('faq_description')->nullable();
            $table->json('faq_items')->nullable();
            $table->string('layout_mode', 32)->default('custom_template')->index();
            $table->string('template_key', 64)->nullable();
            $table->foreignId('landing_template_id')->nullable()->constrained('landing_templates')->nullOnDelete();
            $table->json('template_settings')->nullable();
            $table->json('sections')->nullable();
            $table->json('theme_settings')->nullable();
            $table->timestamp('campaign_starts_at')->nullable()->index();
            $table->timestamp('campaign_ends_at')->nullable()->index();
            $table->string('expired_behavior', 32)->default('show_message');
            $table->text('expired_message')->nullable();
            $table->boolean('show_header')->default(true);
            $table->boolean('show_footer')->default(true);
            $table->boolean('tracking_enabled')->default(true)->index();
            $table->string('title');
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
    }

    /** @param list<int> $customLandingIds */
    private function createLandingPageRelations(array $customLandingIds): void
    {
        Schema::create('project_service', function (Blueprint $table): void {
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['project_id', 'service_id']);
        });

        Schema::create('landing_page_project', function (Blueprint $table): void {
            $table->foreignId('landing_page_id')->constrained('landing_pages')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
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
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->primary(['landing_page_id', 'post_id']);
        });

        if (! Schema::hasTable('landing_content_project_source')) {
            return;
        }

        DB::table('landing_content_project_source')
            ->orderBy('project_id')
            ->get()
            ->each(function (object $record) use ($customLandingIds): void {
                $landingId = (int) $record->landing_id;

                if (in_array($landingId, $customLandingIds, true)) {
                    DB::table('landing_page_project')->insert([
                        'landing_page_id' => $landingId,
                        'project_id' => $record->project_id,
                        'sort_order' => 0,
                        'created_at' => $record->created_at,
                        'updated_at' => $record->updated_at,
                    ]);

                    return;
                }

                DB::table('project_service')->insert([
                    'project_id' => $record->project_id,
                    'service_id' => $landingId,
                    'created_at' => $record->created_at,
                    'updated_at' => $record->updated_at,
                ]);
            });

        DB::table('landing_content_source')
            ->whereIn('id', $customLandingIds ?: [0])
            ->whereNotNull('landing_category_id')
            ->get(['id', 'landing_category_id'])
            ->each(fn (object $record) => DB::table('landing_page_service_category')->insert([
                'landing_page_id' => $record->id,
                'service_category_id' => $record->landing_category_id,
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
    }

    /** @param list<int> $customLandingIds @param list<int> $serviceLandingIds */
    private function splitPricingPlans(array $customLandingIds, array $serviceLandingIds): void
    {
        Schema::table('pricing_plans', function (Blueprint $table): void {
            $table->foreignId('service_id')->nullable()->after('landing_id');
            $table->foreignId('landing_page_id')->nullable()->after('service_id');
        });

        DB::table('pricing_plans')
            ->whereIn('landing_id', $serviceLandingIds ?: [0])
            ->update(['service_id' => DB::raw('landing_id')]);
        DB::table('pricing_plans')
            ->whereIn('landing_id', $customLandingIds ?: [0])
            ->update(['landing_page_id' => DB::raw('landing_id')]);

        Schema::table('pricing_plans', function (Blueprint $table): void {
            $table->dropColumn('landing_id');
            $table->foreign('service_id')->references('id')->on('services')->nullOnDelete();
            $table->foreign('landing_page_id')->references('id')->on('landing_pages')->nullOnDelete();
        });
    }

    /** @param list<int> $customLandingIds @param list<int> $serviceLandingIds */
    private function splitContactRequests(array $customLandingIds, array $serviceLandingIds): void
    {
        Schema::table('contact_requests', function (Blueprint $table): void {
            $table->foreignId('service_id')->nullable()->after('landing_id');
            $table->foreignId('landing_page_id')->nullable()->after('service_id');
        });

        DB::table('contact_requests')
            ->whereIn('landing_id', $serviceLandingIds ?: [0])
            ->update(['service_id' => DB::raw('landing_id')]);
        DB::table('contact_requests')
            ->whereIn('landing_id', $customLandingIds ?: [0])
            ->update(['landing_page_id' => DB::raw('landing_id')]);

        Schema::table('contact_requests', function (Blueprint $table): void {
            $table->dropColumn('landing_id');
            $table->foreign('service_id')->references('id')->on('services')->nullOnDelete();
            $table->foreign('landing_page_id')->references('id')->on('landing_pages')->nullOnDelete();
        });
    }

    /** @param list<int> $customLandingIds @param list<int> $serviceLandingIds */
    private function splitLandingEvents(array $customLandingIds, array $serviceLandingIds): void
    {
        Schema::table('landing_events', function (Blueprint $table): void {
            $table->foreignId('service_id')->nullable()->after('landing_id');
            $table->foreignId('landing_page_id')->nullable()->after('service_id');
        });

        DB::table('landing_events')
            ->whereIn('landing_id', $serviceLandingIds ?: [0])
            ->update(['service_id' => DB::raw('landing_id')]);
        DB::table('landing_events')
            ->whereIn('landing_id', $customLandingIds ?: [0])
            ->update(['landing_page_id' => DB::raw('landing_id')]);

        Schema::table('landing_events', function (Blueprint $table): void {
            $table->dropColumn('landing_id');
            $table->foreign('service_id')->references('id')->on('services')->nullOnDelete();
            $table->foreign('landing_page_id')->references('id')->on('landing_pages')->nullOnDelete();
        });
    }

    private function dropForeignIfExists(string $table, string $column): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return;
        }

        $constraints = DB::select(
            'select distinct constraint_name from information_schema.key_column_usage where constraint_schema = database() and table_name = ? and column_name = ? and referenced_table_name is not null',
            [$table, $column],
        );

        foreach ($constraints as $constraint) {
            DB::statement(sprintf(
                'alter table `%s` drop foreign key `%s`',
                $table,
                $constraint->CONSTRAINT_NAME ?? $constraint->constraint_name,
            ));
        }
    }

    private function dropAllForeignKeys(string $table): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $constraints = DB::select(
            'select distinct constraint_name from information_schema.key_column_usage where constraint_schema = database() and table_name = ? and referenced_table_name is not null',
            [$table],
        );

        foreach ($constraints as $constraint) {
            DB::statement(sprintf(
                'alter table `%s` drop foreign key `%s`',
                $table,
                $constraint->CONSTRAINT_NAME ?? $constraint->constraint_name,
            ));
        }
    }
};
