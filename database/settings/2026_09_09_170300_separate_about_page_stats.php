<?php

use Illuminate\Support\Facades\DB;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('about.page_stats', json_decode(DB::table('settings')->where('group', 'homepage')->where('name', 'stats')->value('payload') ?? '[]', true));
    }
};
