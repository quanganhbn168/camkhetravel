<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Keep the applied baseline migration intact. Never drop existing content.
        Schema::table('solutions', function (Blueprint $table): void {
            $table->unsignedBigInteger('solution_category_id')->nullable()->after('id');
        });

        if (DB::table('solutions')->exists()) {
            $now = now();
            // Reuse this category when only this migration was rolled back.
            $categoryId = DB::table('solution_categories')->where('seed_key', 'legacy-unclassified')->value('id');
            if ($categoryId === null) {
                $categoryId = DB::table('solution_categories')->insertGetId([
                    'seed_key' => 'legacy-unclassified',
                    'name' => 'Chưa phân loại',
                    'description' => 'Nội dung từ module cũ. Chọn danh mục kỹ thuật phù hợp trước khi công khai lại.',
                    'is_active' => false,
                    'sort_order' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            } else {
                DB::table('solution_categories')->where('id', $categoryId)->update(['is_active' => false]);
            }
            if (! DB::table('slugs')->where('sluggable_type', 'solution-category')->where('sluggable_id', $categoryId)->exists()) {
                $baseSlug = 'giai-phap-chua-phan-loai';
                $slug = $baseSlug;
                $suffix = 2;
                while (DB::table('slugs')->where('slug', $slug)->exists()) {
                    $slug = $baseSlug.'-'.$suffix++;
                }
                DB::table('slugs')->insert([
                    'slug' => $slug,
                    'sluggable_type' => 'solution-category',
                    'sluggable_id' => $categoryId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
            DB::table('solutions')->update(['solution_category_id' => $categoryId]);
        }

        Schema::table('solutions', function (Blueprint $table): void {
            $table->unsignedBigInteger('solution_category_id')->nullable(false)->change();
            $table->foreign('solution_category_id')->references('id')
                ->on('solution_categories')->restrictOnDelete();
            $table->index(['solution_category_id', 'is_active', 'sort_order'], 'solutions_category_visibility_order_index');
            $table->dropColumn('short_title');
        });
    }

    public function down(): void
    {
        Schema::table('solutions', function (Blueprint $table): void {
            $table->dropIndex('solutions_category_visibility_order_index');
            $table->dropConstrainedForeignId('solution_category_id');
            $table->string('short_title')->nullable()->after('title');
        });
    }
};
