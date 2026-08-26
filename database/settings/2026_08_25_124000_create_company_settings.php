<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('company.tax_code', '');
        $this->migrator->add('company.representative', '');
        $this->migrator->add('company.founded_year', null);
        $this->migrator->add('company.business_license', '');
    }
};
