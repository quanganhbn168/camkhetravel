<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class DesignSettings extends Settings
{
    public string $color_primary;

    public string $color_primary_hover;

    public string $color_ink;

    public string $color_surface;

    public string $color_muted;

    public string $font_size_base = '1rem';

    public string $font_size_body = '1rem';

    public string $font_size_small = '0.875rem';

    public string $font_size_h1 = 'clamp(2.25rem, 4.5vw, 4rem)';

    public string $font_size_h2 = 'clamp(1.75rem, 3vw, 2.75rem)';

    public string $font_size_h3 = '1.25rem';

    public string $font_size_stat = 'clamp(2.25rem, 4vw, 3.75rem)';

    public static function group(): string
    {
        return 'design';
    }
}
