<?php

namespace App\View\Components;

use App\Settings\DesignSettings;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class SiteDesignTokens extends Component
{
    public function __construct(public readonly DesignSettings $design) {}

    public function render(): View
    {
        return view('components.site-design-tokens');
    }
}
