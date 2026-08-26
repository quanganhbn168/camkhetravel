<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AboutSettings extends Settings
{
    public array $page_label;

    public array $page_title;

    public array $page_intro;

    public array $story;

    public array $history;

    public array $mission;

    public array $vision;

    public array $core_values;

    public static function group(): string
    {
        return 'about';
    }
}
