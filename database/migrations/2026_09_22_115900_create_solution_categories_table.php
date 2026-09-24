<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solution_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('seed_key')->nullable()->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->longText('body')->nullable();
            foreach (['curator_media_id', 'banner_media_id', 'seo_image_media_id'] as $column) {
                $table->foreignId($column)->nullable()->constrained('curator')->nullOnDelete();
            }
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->boolean('is_active')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->index(['is_active', 'sort_order']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        DB::table('slugs')->where('sluggable_type', 'solution-category')
            ->whereIn('sluggable_id', DB::table('solution_categories')->select('id'))
            ->delete();
        Schema::dropIfExists('solution_categories');
    }
};
