<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class LandingThemeTokens extends Component
{
    /** @param array<string, string> $theme */
    public function __construct(
        public readonly array $theme,
        public readonly string $selector,
    ) {}

    public function render(): View
    {
        return view('components.landing-theme-tokens');
    }
}
