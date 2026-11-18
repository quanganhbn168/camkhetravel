<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        foreach (['head_top_scripts', 'body_end_scripts'] as $key) {
            if (! $this->migrator->exists('website.'.$key)) {
                $this->migrator->add('website.'.$key, '');
            }
        }
    }
};
