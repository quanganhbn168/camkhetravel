<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\SolutionCategory;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_seed_creates_content_without_accounts_roles_or_permissions(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertGreaterThan(0, Service::query()->count());
        $this->assertSame(0, SolutionCategory::query()->count());
        $this->assertSame(0, User::query()->count());
        $this->assertSame(0, Role::query()->count());
        $this->assertSame(0, Permission::query()->count());
        $this->assertFalse(DB::table('settings')
            ->where('group', 'website')
            ->whereIn('name', ['favicon_media_id', 'google_maps_url'])
            ->exists());
    }
}
