<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('about.story_title', []);
        $this->migrator->add('about.history_title', []);
        $this->migrator->add('about.history_description', []);
        $this->migrator->add('about.principles_title', []);
        $this->migrator->add('about.services_title', []);
        $this->migrator->add('about.services_link_label', []);
        $this->migrator->add('about.stats_title', []);
        $this->migrator->add('about.cta_title', []);
        $this->migrator->add('about.cta_button_label', []);
    }
};
