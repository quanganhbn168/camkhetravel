<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('service_category_id')->nullable()->constrained('service_categories')->nullOnDelete();
            $table->foreignId('curator_media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->foreignId('banner_video_media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->foreignId('process_background_media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->foreignId('commitment_media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->json('gallery')->nullable();
            $table->json('backstage_gallery')->nullable();
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
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
