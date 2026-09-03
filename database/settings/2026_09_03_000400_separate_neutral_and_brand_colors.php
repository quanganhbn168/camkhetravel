<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->update(
            'design.color_ink',
            fn (string $value): string => strtolower($value) === '#1f2b25' ? '#111827' : $value,
        );
        $this->migrator->update(
            'design.color_surface',
            fn (string $value): string => strtolower($value) === '#f6faee' ? '#f8fafc' : $value,
        );
        $this->migrator->update(
            'design.color_muted',
            fn (string $value): string => strtolower($value) === '#e5f0d4' ? '#e5e7eb' : $value,
        );
    }
};
