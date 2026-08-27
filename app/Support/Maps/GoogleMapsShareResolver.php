<?php

namespace App\Support\Maps;

use GuzzleHttp\TransferStats;
use Illuminate\Support\Facades\Http;
use Throwable;

final class GoogleMapsShareResolver
{
    public function resolveEmbed(mixed $value): ?string
    {
        $shareUrl = GoogleMapsUrl::normalizeShare($value);

        if ($shareUrl === null) {
            return null;
        }

        if (($embedUrl = $this->embedFromResolvedUrl($shareUrl)) !== null) {
            return $embedUrl;
        }

        $effectiveUrl = null;

        try {
            $response = Http::connectTimeout(3)
                ->timeout(8)
                ->withOptions([
                    'allow_redirects' => [
                        'max' => 5,
                        'track_redirects' => true,
                    ],
                    'on_stats' => static function (TransferStats $stats) use (&$effectiveUrl): void {
                        $effectiveUrl = (string) $stats->getEffectiveUri();
                    },
                ])
                ->get($shareUrl);
        } catch (Throwable) {
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        return $this->embedFromResolvedUrl($effectiveUrl ?: (string) $response->effectiveUri());
    }

    public function embedFromResolvedUrl(?string $value): ?string
    {
        if (! is_string($value) || $value === '') {
            return null;
        }

        $parts = parse_url($value);

        if (($parts['scheme'] ?? '') !== 'https'
            || ! GoogleMapsUrl::isGoogleMapsHost(strtolower((string) ($parts['host'] ?? '')))) {
            return null;
        }

        $resolvedUrl = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $coordinates = null;

        if (preg_match('/!3d(-?\d+(?:\.\d+)?)!4d(-?\d+(?:\.\d+)?)/', $resolvedUrl, $matches) === 1) {
            $coordinates = [$matches[1], $matches[2]];
        } elseif (preg_match('/@(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)/', $resolvedUrl, $matches) === 1) {
            $coordinates = [$matches[1], $matches[2]];
        }

        if ($coordinates === null
            || (float) $coordinates[0] < -90
            || (float) $coordinates[0] > 90
            || (float) $coordinates[1] < -180
            || (float) $coordinates[1] > 180) {
            return null;
        }

        return GoogleMapsUrl::embedFromCoordinates($coordinates[0], $coordinates[1]);
    }
}
