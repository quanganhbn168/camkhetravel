<?php

namespace App\Support\Landing;

use App\Models\Landing;
use App\Models\LandingEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class LandingEventRecorder
{
    /** @var list<string> */
    public const EVENT_NAMES = [
        'page_view',
        'cta_click',
        'pricing_view',
        'project_click',
        'phone_click',
        'zalo_click',
        'countdown_view',
        'countdown_expired',
        'lead_submit',
    ];

    /** @param array<string, mixed> $attributes */
    public function record(Landing $landing, string $eventName, Request $request, array $attributes = []): ?LandingEvent
    {
        if (! $landing->tracking_enabled || ! in_array($eventName, self::EVENT_NAMES, true)) {
            return null;
        }

        $payload = Arr::only((array) ($attributes['payload'] ?? []), [
            'label',
            'target_url',
            'contact_request_id',
            'pricing_plan_id',
            'project_id',
        ]);

        return $landing->events()->create([
            'event_name' => $eventName,
            'block_id' => $this->stringValue($attributes['block_id'] ?? $request->input('block_id'), 100),
            'visitor_id' => $this->stringValue($request->input('visitor_id'), 64),
            'session_id' => $this->stringValue($request->input('session_id'), 64),
            'utm_source' => $this->stringValue($request->input('utm_source'), 255),
            'utm_medium' => $this->stringValue($request->input('utm_medium'), 255),
            'utm_campaign' => $this->stringValue($request->input('utm_campaign'), 255),
            'utm_content' => $this->stringValue($request->input('utm_content'), 255),
            'utm_term' => $this->stringValue($request->input('utm_term'), 255),
            'gclid' => $this->stringValue($request->input('gclid'), 255),
            'fbclid' => $this->stringValue($request->input('fbclid'), 255),
            'page_url' => $this->stringValue($request->input('page_url') ?: $request->fullUrl(), 2048),
            'referrer' => $this->stringValue($request->input('referrer'), 2048),
            'payload' => $payload !== [] ? $payload : null,
            'ip_hash' => $request->ip()
                ? hash_hmac('sha256', now()->toDateString().'|'.$request->ip(), (string) config('app.key'))
                : null,
            'user_agent' => $this->stringValue($request->userAgent(), 2048),
            'occurred_at' => now(),
        ]);
    }

    private function stringValue(mixed $value, int $limit): ?string
    {
        if (! is_scalar($value) || blank((string) $value)) {
            return null;
        }

        return Str::limit(trim((string) $value), $limit, '');
    }
}
