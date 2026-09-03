<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bni_article_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('bni_article_category_article', function (Blueprint $table): void {
            $table->foreignId('bni_article_id')->constrained('bni_articles')->cascadeOnDelete();
            $table->foreignId('bni_article_category_id')->constrained('bni_article_categories')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->primary(['bni_article_id', 'bni_article_category_id']);
        });

        $now = now();
        $categories = [
            'event' => ['name' => 'Tin sự kiện', 'slug' => 'tin-su-kien', 'sort_order' => 1],
            'chapter' => ['name' => 'Tin các chapter', 'slug' => 'tin-cac-chapter', 'sort_order' => 2],
            'pickleball' => ['name' => 'Tin Pickleball', 'slug' => 'tin-pickleball', 'sort_order' => 3],
        ];

        DB::table('bni_article_categories')->insert(array_map(
            fn (array $category): array => $category + [
                'description' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            array_values($categories),
        ));

        $categoryIds = DB::table('bni_article_categories')->pluck('id', 'slug');

        foreach ($categories as $type => $category) {
            $categoryId = $categoryIds->get($category['slug']);

            if (! $categoryId) {
                continue;
            }

            DB::table('bni_articles')
                ->where('type', $type)
                ->orderBy('id')
                ->select('id')
                ->chunkById(200, function ($articles) use ($categoryId): void {
                    DB::table('bni_article_category_article')->insertOrIgnore(
                        $articles->map(fn ($article): array => [
                            'bni_article_id' => $article->id,
                            'bni_article_category_id' => $categoryId,
                            'sort_order' => 0,
                        ])->all(),
                    );
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bni_article_category_article');
        Schema::dropIfExists('bni_article_categories');
    }
};
