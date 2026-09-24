<?php

namespace Tests\Integration;

use App\Models\SolutionCategory;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SolutionMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_fresh_schema_requires_a_real_solution_category(): void
    {
        $this->assertTrue(Schema::hasTable('solution_categories'));
        $this->assertTrue(Schema::hasColumn('solutions', 'solution_category_id'));
        $this->assertFalse(Schema::hasColumn('solution_categories', 'parent_id'));
        $this->assertFalse(Schema::hasColumn('solutions', 'short_title'));

        $this->expectException(QueryException::class);
        DB::table('solutions')->insert([
            'title' => 'Danh mục không tồn tại',
            'solution_category_id' => 99999999,
        ]);
    }

    public function test_fresh_schema_rejects_a_missing_solution_category(): void
    {
        $this->expectException(QueryException::class);
        DB::table('solutions')->insert([
            'title' => 'Thiếu danh mục',
            'solution_category_id' => null,
        ]);
    }

    public function test_database_restricts_deleting_a_category_that_has_solutions(): void
    {
        $category = SolutionCategory::create(['name' => 'Danh mục có bài']);
        $category->solutions()->create(['title' => 'Giải pháp cần giữ']);

        $this->expectException(QueryException::class);
        DB::table('solution_categories')->where('id', $category->id)->delete();
    }
}
