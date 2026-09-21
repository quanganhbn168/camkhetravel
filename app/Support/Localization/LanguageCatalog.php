<?php

namespace App\Support\Localization;

final class LanguageCatalog
{
    public function active(): \Illuminate\Support\Collection
    {
        return collect(['vi' => (object) ['code' => 'vi', 'name' => 'Tiếng Việt', 'is_default' => true]]);
    }

    public function defaultCode(): string
    {
        return 'vi';
    }

    public function ogLocale(string $code = 'vi'): string
    {
        return 'vi_VN';
    }

    public function languageTag(string $code = 'vi'): string
    {
        return 'vi-VN';
    }
}
