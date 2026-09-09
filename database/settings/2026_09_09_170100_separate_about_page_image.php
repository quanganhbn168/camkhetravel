<?php

use Illuminate\Support\Facades\DB;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('about.default_image_media_id', json_decode(DB::table('settings')->where('group', 'website')->where('name', 'about_image_media_id')->value('payload') ?? 'null', true));
    }
};
