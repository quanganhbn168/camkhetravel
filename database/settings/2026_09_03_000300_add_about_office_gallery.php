<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $legacyMediaId = null;

        if (Schema::hasTable('settings')) {
            $payload = DB::table('settings')
                ->where('group', 'about')
                ->where('name', 'office_image_media_id')
                ->value('payload');
            $decoded = is_string($payload) ? json_decode($payload, true) : $payload;
            $legacyMediaId = is_numeric($decoded) ? (int) $decoded : null;
        }

        $this->migrator->add('about.office_gallery', $legacyMediaId ? [$legacyMediaId] : []);
    }
};
