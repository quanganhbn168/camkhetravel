<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class HomepageSettings extends Settings
{
    public array $about_eyebrow;

    public array $about_title;

    public array $about_content;

    public array $stats;

    public array $commitments;

    public array $capabilities;

    public array $consultation_title;

    public array $consultation_content;

    public array $faq_title;

    public array $faq_description;

    public static function group(): string
    {
        return 'homepage';
    }
}
