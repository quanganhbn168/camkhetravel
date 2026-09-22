<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class CompanySettings extends Settings
{
    public string $tax_code = '';

    public string $representative = '';

    public ?int $founded_year = null;

    public string $business_license = '';

    public static function group(): string
    {
        return 'company';
    }
}
