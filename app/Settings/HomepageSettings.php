<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class HomepageSettings extends Settings
{
    public string $about_title = '';

    public string $about_content = '';

    public array $stats = [];

    public string $commitments = '';

    public string $capabilities = '';

    public string $consultation_title = '';

    public string $consultation_content = '';

    public string $faq_title = '';

    public string $faq_description = '';

    public static function group(): string
    {
        return 'homepage';
    }
}
