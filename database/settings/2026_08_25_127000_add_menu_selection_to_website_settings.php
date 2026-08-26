<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('website.header_menu_id', null);
        $this->migrator->add('website.footer_menu_id', null);
    }
};
