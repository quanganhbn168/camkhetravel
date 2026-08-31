<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\LandingPage;
use App\Support\Landing\LandingEventRecorder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class LandingTrackingController extends Controller
{
    public function __invoke(
        Request $request,
        LandingPage $landingPage,
        LandingEventRecorder $recorder,
    ): Response {
        abort_unless($landingPage->status === 'published', 404);

        $data = $request->validate([
            'event_name' => ['required', 'string', Rule::in(LandingEventRecorder::EVENT_NAMES)],
            'block_id' => ['nullable', 'string', 'max:100'],
            'visitor_id' => ['nullable', 'string', 'max:64'],
            'session_id' => ['nullable', 'string', 'max:64'],
            'utm_source' => ['nullable', 'string', 'max:255'],
            'utm_medium' => ['nullable', 'string', 'max:255'],
            'utm_campaign' => ['nullable', 'string', 'max:255'],
            'utm_content' => ['nullable', 'string', 'max:255'],
            'utm_term' => ['nullable', 'string', 'max:255'],
            'gclid' => ['nullable', 'string', 'max:255'],
            'fbclid' => ['nullable', 'string', 'max:255'],
            'page_url' => ['nullable', 'string', 'max:2048'],
            'referrer' => ['nullable', 'string', 'max:2048'],
            'payload' => ['nullable', 'array'],
            'payload.label' => ['nullable', 'string', 'max:255'],
            'payload.target_url' => ['nullable', 'string', 'max:2048'],
            'payload.pricing_plan_id' => ['nullable', 'integer'],
            'payload.project_id' => ['nullable', 'integer'],
        ]);

        $recorder->record($landingPage, $data['event_name'], $request, [
            'block_id' => $data['block_id'] ?? null,
            'payload' => $data['payload'] ?? [],
        ]);

        return response()->noContent();
    }
}
