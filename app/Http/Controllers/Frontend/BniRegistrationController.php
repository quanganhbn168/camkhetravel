<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BniEvent;
use App\Models\BniRegistration;
use App\Support\Localization\LocalizedUrl;
use App\Support\Media\MediaUrl;
use App\Support\Seo\FrontendSeoBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BniRegistrationController extends Controller
{
    public function __construct(private readonly FrontendSeoBuilder $seo) {}

    public function create(): View
    {
        $event = $this->handoverEvent();

        return view('frontend.bni-registration', [
            'event' => $event,
            'heroImageUrl' => MediaUrl::versioned($event->heroMedia),
            'chapters' => $event->chapters
                ->where('is_active', true)
                ->sortBy('sort_order')
                ->values(),
            'eventDate' => $event->starts_at?->translatedFormat('d/m/Y'),
            'eventTime' => collect([
                $event->starts_at?->format('H:i'),
                $event->ends_at?->format('H:i'),
            ])->filter()->implode(' – '),
            'eventLocation' => collect([$event->venue, $event->address])
                ->filter()
                ->unique()
                ->implode(', '),
            'seo' => $this->seo->listing(
                'Đăng ký '.$event->title.' | '.$this->seo->siteName(),
                'Đăng ký tham dự '.$event->title.' trên hệ thống BNI.',
                LocalizedUrl::route('bni.registrations.create'),
                false,
            ),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $event = $this->handoverEvent();
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:32'],
            'email' => ['nullable', 'email', 'max:255'],
            'bni_chapter_id' => [
                'nullable',
                'integer',
                Rule::exists('bni_chapters', 'id')->where(fn ($query) => $query
                    ->where('bni_event_id', $event->id)
                    ->where('is_active', true)),
            ],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        $event->registrations()->create($data + [
            'status' => BniRegistration::STATUS_PENDING,
        ]);

        return to_route('bni.registrations.create')
            ->with('success', 'Đăng ký đã được ghi nhận. Ban tổ chức BNI sẽ liên hệ xác nhận.');
    }

    private function handoverEvent(): BniEvent
    {
        return BniEvent::query()
            ->published()
            ->where('type', 'handover')
            ->with(['heroMedia', 'chapters'])
            ->orderByDesc('is_featured')
            ->orderByDesc('starts_at')
            ->firstOrFail();
    }
}
