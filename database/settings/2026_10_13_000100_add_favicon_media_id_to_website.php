<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        if (! $this->migrator->exists('website.favicon_media_id')) {
            $this->migrator->add('website.favicon_media_id', null);
        }
    }
};
