<?php

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('services_categories', 'landing_categories');
        Schema::rename('services', 'landings');

        Schema::table('landings', function (Blueprint $table): void {
            $table->dropForeign('services_service_category_id_foreign');
            $table->renameColumn('service_category_id', 'landing_category_id');
        });
        Schema::table('landings', function (Blueprint $table): void {
            $table->foreign('landing_category_id')
                ->references('id')
                ->on('landing_categories')
                ->nullOnDelete();
        });

        $this->renameForeignKey('pricing_plans', 'pricing_plans_service_id_foreign', 'service_id', 'landing_id', 'landings');
        $this->renameForeignKey('contact_requests', 'contact_requests_service_id_foreign', 'service_id', 'landing_id', 'landings');

        Schema::rename('project_service', 'landing_project');
        Schema::table('landing_project', function (Blueprint $table): void {
            $table->dropForeign('project_service_project_id_foreign');
            $table->dropForeign('project_service_service_id_foreign');
            $table->dropPrimary(['project_id', 'service_id']);
            $table->renameColumn('service_id', 'landing_id');
        });
        Schema::table('landing_project', function (Blueprint $table): void {
            $table->primary(['project_id', 'landing_id']);
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
            $table->foreign('landing_id')->references('id')->on('landings')->cascadeOnDelete();
        });

        DB::table('slugs')
            ->whereIn('sluggable_type', ['service', 'legacy-service', Service::class])
            ->update(['sluggable_type' => 'landing']);
        DB::table('slugs')
            ->whereIn('sluggable_type', ['service-category', 'legacy-service-category', ServiceCategory::class])
            ->update(['sluggable_type' => 'landing-category']);
        DB::table('comments')
            ->whereIn('commentable_type', ['service', 'legacy-service', Service::class])
            ->update(['commentable_type' => 'landing']);
    }

    public function down(): void
    {
        DB::table('comments')->where('commentable_type', 'landing')->update(['commentable_type' => 'service']);
        DB::table('slugs')->where('sluggable_type', 'landing-category')->update(['sluggable_type' => 'service-category']);
        DB::table('slugs')->where('sluggable_type', 'landing')->update(['sluggable_type' => 'service']);

        Schema::table('landing_project', function (Blueprint $table): void {
            $table->dropForeign(['project_id']);
            $table->dropForeign(['landing_id']);
            $table->dropPrimary(['project_id', 'landing_id']);
            $table->renameColumn('landing_id', 'service_id');
        });

        Schema::table('landings', function (Blueprint $table): void {
            $table->dropForeign(['landing_category_id']);
            $table->renameColumn('landing_category_id', 'service_category_id');
        });
        Schema::rename('landings', 'services');
        Schema::rename('landing_categories', 'services_categories');
        Schema::table('services', function (Blueprint $table): void {
            $table->foreign('service_category_id')
                ->references('id')
                ->on('services_categories')
                ->nullOnDelete();
        });

        $this->renameForeignKey('pricing_plans', 'pricing_plans_landing_id_foreign', 'landing_id', 'service_id', 'services');
        $this->renameForeignKey('contact_requests', 'contact_requests_landing_id_foreign', 'landing_id', 'service_id', 'services');

        Schema::rename('landing_project', 'project_service');
        Schema::table('project_service', function (Blueprint $table): void {
            $table->primary(['project_id', 'service_id']);
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
            $table->foreign('service_id')->references('id')->on('services')->cascadeOnDelete();
        });
    }

    private function renameForeignKey(string $tableName, string $foreignName, string $oldColumn, string $newColumn, string $references): void
    {
        Schema::table($tableName, function (Blueprint $table) use ($foreignName, $oldColumn, $newColumn): void {
            $table->dropForeign($foreignName);
            $table->renameColumn($oldColumn, $newColumn);
        });
        Schema::table($tableName, function (Blueprint $table) use ($newColumn, $references): void {
            $table->foreign($newColumn)->references('id')->on($references)->nullOnDelete();
        });
    }
};
