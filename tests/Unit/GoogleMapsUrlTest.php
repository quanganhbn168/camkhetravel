<?php

namespace Tests\Unit;

use App\Support\Maps\GoogleMapsUrl;
use App\Support\Maps\GoogleMapsShareResolver;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class GoogleMapsUrlTest extends TestCase
{
    public function test_it_extracts_the_src_from_a_google_maps_iframe(): void
    {
        $embedUrl = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3720.5470758615897!2d106.07159277471818!3d21.170415682899268!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31350959196ff7eb%3A0xc4cccab52ce0a631!2sC%C3%94NG%20TY%20TNHH%20THT%20MEDIA!5e0!3m2!1svi!2sus!4v1787731996935!5m2!1svi!2sus';
        $iframe = '<iframe src="'.$embedUrl.'&amp;hl=vi" width="600" height="450" style="border:0;" allowfullscreen loading="lazy"></iframe>';

        $this->assertSame(
            $embedUrl.'&hl=vi',
            GoogleMapsUrl::normalizeEmbed($iframe),
        );
    }

    public function test_it_keeps_a_direct_google_maps_embed_url(): void
    {
        $embedUrl = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3720.5470758615897!2d106.07159277471818!3d21.170415682899268!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31350959196ff7eb%3A0xc4cccab52ce0a631!2sC%C3%94NG%20TY%20TNHH%20THT%20MEDIA!5e0!3m2!1svi!2sus!4v1787731996935!5m2!1svi!2sus';

        $this->assertSame($embedUrl, GoogleMapsUrl::normalizeEmbed($embedUrl));
    }

    public function test_it_does_not_treat_a_share_link_as_an_embed_url(): void
    {
        $this->assertNull(GoogleMapsUrl::normalizeEmbed('https://maps.app.goo.gl/M1iQjB52X9NqzBYd7'));
    }

    public function test_its_filament_compatible_validation_rule_accepts_the_iframe_input(): void
    {
        $iframe = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3720.5470758615897" width="600" height="450"></iframe>';

        $this->assertTrue(Validator::make(
            ['map' => $iframe],
            ['map' => [GoogleMapsUrl::embedValidationRule()]],
        )->passes());
    }

    public function test_it_builds_an_embed_url_from_coordinates_in_a_resolved_share_url(): void
    {
        $resolvedUrl = 'https://www.google.com/maps/place/C%C3%94NG+TY+TNHH+THT+MEDIA/@21.1704157,106.0715928,17z/data=!4m6!3m5!1s0x31350959196ff7eb:0xc4cccab52ce0a631!8m2!3d21.1704107!4d106.0741677!16s%2Fg%2F11vcy2610w';

        $this->assertSame(
            'https://www.google.com/maps?q=21.1704107%2C106.0741677&output=embed',
            app(GoogleMapsShareResolver::class)->embedFromResolvedUrl($resolvedUrl),
        );
    }
}
