<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('design.color_primary', '#ee6b2d');
        $this->migrator->add('design.color_primary_hover', '#d84d20');
        $this->migrator->add('design.color_ink', '#10233e');
        $this->migrator->add('design.color_midnight', '#081a31');
        $this->migrator->add('design.color_surface', '#f5f7fa');
        $this->migrator->add('design.color_muted', '#e7edf5');
    }
};
