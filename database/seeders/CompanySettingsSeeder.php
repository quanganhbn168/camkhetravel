<?php

namespace Database\Seeders;

use App\Settings\CompanySettings;
use Illuminate\Database\Seeder;

final class CompanySettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = new CompanySettings([
            'tax_code' => '',
            'representative' => '',
            'founded_year' => now()->year,
            'business_license' => '',
        ]);
        $settings->settingsConfig()->resetDefaultValueLoadedProperties();
        $settings->save();
    }
}
