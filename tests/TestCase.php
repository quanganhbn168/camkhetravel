<?php

namespace Tests;

use Database\Seeders\AboutSettingsSeeder;
use Database\Seeders\CompanySettingsSeeder;
use Database\Seeders\HomepageSettingsSeeder;
use Database\Seeders\MediaSeeder;
use Database\Seeders\SystemPageSettingsSeeder;
use Database\Seeders\WebsiteSettingsSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! in_array(\Illuminate\Foundation\Testing\RefreshDatabase::class, class_uses_recursive($this), true)) {
            return;
        }

        $this->seed([
            MediaSeeder::class,
            WebsiteSettingsSeeder::class,
            HomepageSettingsSeeder::class,
            CompanySettingsSeeder::class,
            AboutSettingsSeeder::class,
            SystemPageSettingsSeeder::class,
        ]);
    }
}
