<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('about.page_label', []);
        $this->migrator->add('about.page_title', []);
        $this->migrator->add('about.page_intro', []);
        $this->migrator->add('about.story', []);
        $this->migrator->add('about.history', []);
        $this->migrator->add('about.mission', []);
        $this->migrator->add('about.vision', []);
        $this->migrator->add('about.core_values', []);
    }
};
