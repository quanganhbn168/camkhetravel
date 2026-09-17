<?php

namespace Tests\Unit;

use App\Support\Design\BrandPalette;
use PHPUnit\Framework\TestCase;

class BrandPaletteTest extends TestCase
{
    public function test_palette_normalizes_colors_and_keeps_text_legible(): void
    {
        $red = BrandPalette::make('#d71920', '#ad1117');
        $this->assertSame('215, 25, 32', $red['rgb']);
        $this->assertSame('#ffffff', $red['contrast']);
        $this->assertSame('#000000', BrandPalette::make('#fff', '#ffc')['contrast']);
        $this->assertSame('#d71920', BrandPalette::make('not-a-color', null)['primary']);
    }
}
