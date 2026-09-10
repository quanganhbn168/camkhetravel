<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('bni_invitation.event_prefix', 'Tới tham dự chương trình chào mừng');
    }
};
