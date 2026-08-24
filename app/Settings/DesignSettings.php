<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class DesignSettings extends Settings
{
    public string $color_primary;

    public string $color_primary_hover;

    public string $color_ink;

    public string $color_midnight;

    public string $color_surface;

    public string $color_muted;

    public string $color_green_light = '#a4cf59';

    public string $color_green_dark = '#247138';

    public string $gradient_green_dark_start = '#268944';

    public string $gradient_green_dark_end = '#247138';

    public string $gradient_green_light_start = '#a4cf59';

    public string $gradient_green_light_end = '#d8ff96';

    public static function group(): string
    {
        return 'design';
    }
}
