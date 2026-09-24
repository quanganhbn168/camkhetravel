<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solution_category_id')->constrained('solution_categories')->restrictOnDelete();
            $table->string('seed_key')->nullable()->unique();
            $table->string('title');
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->json('highlights')->nullable();
            foreach (['curator_media_id', 'banner_media_id', 'seo_image_media_id'] as $column) {
                $table->foreignId($column)->nullable()->constrained('curator')->nullOnDelete();
            }
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_home')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->index(['is_active', 'is_home', 'sort_order']);
            $table->index(['solution_category_id', 'is_active', 'sort_order'], 'solutions_category_visibility_order_index');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solutions');
    }
};
