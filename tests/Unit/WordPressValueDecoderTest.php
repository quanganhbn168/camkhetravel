<?php

namespace Tests\Unit;

use App\Services\WordPress\WordPressValueDecoder;
use PHPUnit\Framework\TestCase;

class WordPressValueDecoderTest extends TestCase
{
    public function test_it_decodes_serialized_wordpress_values(): void
    {
        $decoder = new WordPressValueDecoder;

        $this->assertSame(
            ['title' => '%title% %sep% %sitename%'],
            $decoder->decode('a:1:{s:5:"title";s:24:"%title% %sep% %sitename%";}'),
        );
        $this->assertFalse($decoder->decode('b:0;'));
    }

    public function test_it_leaves_normal_text_unchanged(): void
    {
        $decoder = new WordPressValueDecoder;

        $this->assertSame('THT Media', $decoder->decode('THT Media'));
    }
}
