<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SystemPageSettings extends Settings
{
    public array $home = [];

    public array $about = [];

    public array $services = [];

    public array $solutions = [];

    public array $contact = [];


    public static function group(): string
    {
        return 'system_pages';
    }
}
