<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('design.font_size_base', '1rem');
        $this->migrator->add('design.font_size_body', '1rem');
        $this->migrator->add('design.font_size_small', '0.875rem');
        $this->migrator->add('design.font_size_h1', 'clamp(2.25rem, 4.5vw, 4rem)');
        $this->migrator->add('design.font_size_h2', 'clamp(1.75rem, 3vw, 2.75rem)');
        $this->migrator->add('design.font_size_h3', '1.25rem');
        $this->migrator->add('design.font_size_stat', 'clamp(2.25rem, 4vw, 3.75rem)');
    }
};
