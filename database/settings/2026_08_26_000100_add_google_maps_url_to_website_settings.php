<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add(
            'website.google_maps_url',
            'https://maps.app.goo.gl/xmxdkWznfpRqxdad7',
        );
    }
};
