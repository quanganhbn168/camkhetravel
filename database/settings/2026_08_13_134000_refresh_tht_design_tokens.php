<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->update('design.color_primary', fn (): string => '#f68e1e');
        $this->migrator->update('design.color_primary_hover', fn (): string => '#db7815');
        $this->migrator->update('design.color_ink', fn (): string => '#1f2b25');
        $this->migrator->update('design.color_midnight', fn (): string => '#247138');
        $this->migrator->update('design.color_surface', fn (): string => '#f6faee');
        $this->migrator->update('design.color_muted', fn (): string => '#e5f0d4');

        $this->migrator->add('design.color_green_light', '#a4cf59');
        $this->migrator->add('design.color_green_dark', '#247138');
        $this->migrator->add('design.gradient_green_dark_start', '#268944');
        $this->migrator->add('design.gradient_green_dark_end', '#247138');
        $this->migrator->add('design.gradient_green_light_start', '#a4cf59');
        $this->migrator->add('design.gradient_green_light_end', '#d8ff96');
    }
};
