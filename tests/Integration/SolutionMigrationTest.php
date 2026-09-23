<?php

namespace Tests\Integration;

use App\Models\Solution;
use App\Models\SolutionCategory;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SolutionMigrationTest extends TestCase
{
    // DDL must not run inside RefreshDatabase transactions on MySQL.
    use DatabaseMigrations;

    private function categoryMigration(): object
    {
        return require database_path('migrations/2026_09_23_120000_create_solution_categories_table.php');
    }

    private function solutionMigration(): object
    {
        return require database_path('migrations/2026_09_23_120100_categorize_solutions_table.php');
    }

    public function test_fresh_schema_is_flat_and_requires_a_real_category(): void
    {
        $this->assertTrue(Schema::hasTable('solution_categories'));
        $this->assertTrue(Schema::hasColumn('solutions', 'solution_category_id'));
        $this->assertFalse(Schema::hasColumn('solution_categories', 'parent_id'));
        $this->assertFalse(Schema::hasColumn('solutions', 'short_title'));

        $this->expectException(QueryException::class);
        DB::table('solutions')->insert(['title' => 'Danh mục không tồn tại', 'solution_category_id' => 99999999]);
    }

    public function test_fresh_schema_rejects_a_missing_category(): void
    {
        $this->expectException(QueryException::class);
        DB::table('solutions')->insert(['title' => 'Thiếu khóa ngoại', 'solution_category_id' => null]);
    }

    public function test_upgrade_preserves_existing_content_and_unrelated_slugs(): void
    {
        $this->solutionMigration()->down();
        $this->categoryMigration()->down();
        $id = DB::table('solutions')->insertGetId([
            'title' => 'Nội dung cũ QA', 'short_title' => 'Tên ngắn cũ',
            'body' => '<p>Giữ nguyên nội dung</p>', 'sort_order' => 12,
            'is_active' => true, 'is_home' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('slugs')->insert([
            ['slug' => 'noi-dung-cu-qa', 'sluggable_type' => 'solution', 'sluggable_id' => $id, 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'giai-phap-chua-phan-loai', 'sluggable_type' => 'post', 'sluggable_id' => 9999, 'created_at' => now(), 'updated_at' => now()],
        ]);
        $this->categoryMigration()->up();
        $this->solutionMigration()->up();

        $solution = Solution::findOrFail($id);
        $this->assertSame('Nội dung cũ QA', $solution->title);
        $this->assertSame('<p>Giữ nguyên nội dung</p>', $solution->body);
        $this->assertSame(12, $solution->sort_order);
        $this->assertTrue($solution->is_active);
        $this->assertSame('noi-dung-cu-qa', $solution->slug);
        $this->assertSame('legacy-unclassified', $solution->category->seed_key);
        $this->assertFalse($solution->category->is_active);
        $this->assertSame('giai-phap-chua-phan-loai-2', $solution->category->slug);
        $this->assertSame(0, Solution::published()->count());
        $this->assertDatabaseHas('slugs', ['slug' => 'giai-phap-chua-phan-loai', 'sluggable_type' => 'post']);
    }

    public function test_reapplying_only_the_link_migration_reuses_the_legacy_category(): void
    {
        $category = SolutionCategory::create(['name' => 'Danh mục trước rollback QA']);
        $solution = $category->solutions()->create(['title' => 'Giữ khi chạy lại QA']);
        $this->solutionMigration()->down();
        $this->solutionMigration()->up();
        $legacyId = Solution::findOrFail($solution->id)->solution_category_id;

        $this->solutionMigration()->down();
        $this->solutionMigration()->up();
        $this->assertSame($legacyId, Solution::findOrFail($solution->id)->solution_category_id);
        $this->assertSame(1, DB::table('solution_categories')->where('seed_key', 'legacy-unclassified')->count());
        $this->assertSame(1, DB::table('slugs')->where('sluggable_type', 'solution-category')->where('sluggable_id', $legacyId)->count());
    }

    public function test_database_restricts_category_deletion_even_without_model_events(): void
    {
        $category = SolutionCategory::create(['name' => 'Danh mục có bài QA']);
        $category->solutions()->create(['title' => 'Giữ lại QA']);
        $this->expectException(QueryException::class);
        DB::table('solution_categories')->where('id', $category->id)->delete();
    }

    public function test_rollback_cleans_category_slugs_and_can_be_migrated_again(): void
    {
        $category = SolutionCategory::create(['name' => 'Danh mục rollback QA']);
        $solution = $category->solutions()->create(['title' => 'Bài rollback QA']);
        $categorySlug = $category->slug;
        $solutionSlug = $solution->slug;
        $this->solutionMigration()->down();
        $this->categoryMigration()->down();

        $this->assertFalse(Schema::hasTable('solution_categories'));
        $this->assertFalse(Schema::hasColumn('solutions', 'solution_category_id'));
        $this->assertTrue(Schema::hasColumn('solutions', 'short_title'));
        $this->assertDatabaseMissing('slugs', ['slug' => $categorySlug]);
        $this->assertDatabaseHas('slugs', ['slug' => $solutionSlug]);
        $this->assertDatabaseHas('solutions', ['id' => $solution->id, 'title' => 'Bài rollback QA']);

        $this->categoryMigration()->up();
        $this->solutionMigration()->up();
        $this->assertNotNull(Solution::findOrFail($solution->id)->solution_category_id);
    }
}
