<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BniEvent;
use App\Models\BniInvitation;
use App\Models\BniRegistration;
use App\Support\Bni\BniExperienceService;
use App\Support\Bni\BniInvitationContent;
use App\Support\Localization\LocalizedUrl;
use App\Support\Seo\FrontendSeoBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class BniInvitationController extends Controller
{
    public function __construct(
        private readonly FrontendSeoBuilder $seo,
        private readonly BniExperienceService $experience,
    ) {}

    public function template(): View
    {
        $event = $this->handoverEvent();
        $invitationContent = BniInvitationContent::resolve(event: $event);
        $canonical = LocalizedUrl::route('bni.invitations.template');

        return view('frontend.bni-invitation', [
            'invitation' => null,
            'event' => $event,
            'chapter' => null,
            'heroImageUrl' => $event->bniMediaUrl('hero'),
            'directionsUrl' => $event->directions_url,
            'guestName' => $invitationContent['default_guest_name'],
            'invitationContent' => $invitationContent,
            'scheduleDays' => $this->experience->scheduleDays($event),
            'featuredEvents' => $this->featuredEventCards(),
            'isInvitationTemplate' => true,
            'seo' => $this->seo->listing(
                $invitationContent['label'].' '.$invitationContent['event_label'].' | '.$this->seo->siteName(),
                $invitationContent['greeting'].' '.$invitationContent['event_label'].' dành cho các chapter BNI.',
                $canonical,
                false,
            ),
        ]);
    }

    public function show(BniInvitation $invitation): View
    {
        $invitation->load(['event.media', 'event.scheduleDays.items', 'event.contacts', 'chapter.contacts']);
        abort_unless($invitation->event, 404);

        $event = $invitation->event;
        $chapter = $invitation->chapter;
        $invitationContent = BniInvitationContent::resolve($chapter, $event);
        $guestName = $invitation->displayGuestName((string) $invitationContent['default_guest_name']);
        $heroImageUrl = $event->bniMediaUrl('hero');

        return view('frontend.bni-invitation', [
            'invitation' => $invitation,
            'event' => $event,
            'chapter' => $chapter,
            'heroImageUrl' => $heroImageUrl,
            'directionsUrl' => $event->directions_url,
            'guestName' => $guestName,
            'invitationContent' => $invitationContent,
            'scheduleDays' => $this->experience->scheduleDays($event),
            'featuredEvents' => $this->featuredEventCards(),
            'isInvitationTemplate' => false,
            'seo' => $this->seo->invitation($invitation, $guestName, $invitationContent, $heroImageUrl),
        ]);
    }

    public function templateRsvp(Request $request): RedirectResponse|JsonResponse
    {
        $event = $this->handoverEvent();
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:32'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        $event->registrations()->create($data + ['status' => BniRegistration::STATUS_PENDING]);

        $message = 'Thông tin RSVP đã được ghi nhận. Ban tổ chức sẽ liên hệ xác nhận.';

        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 201);
        }

        return back()->with('success', $message);
    }

    public function rsvp(Request $request, BniInvitation $invitation): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'rsvp_status' => ['required', 'in:attending,declined'],
            'guest_count' => ['required', 'integer', 'min:1', 'max:10'],
            'rsvp_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $invitation->update($data + ['responded_at' => now()]);

        $message = 'Cảm ơn anh/chị đã phản hồi thư mời.';

        if ($request->expectsJson()) {
            return response()->json(['message' => $message]);
        }

        return back()->with('success', $message);
    }

    private function handoverEvent(): BniEvent
    {
        return $this->experience->currentEvent('handover', ['media', 'scheduleDays.items', 'contacts'])
            ?? abort(404);
    }

    /** @return Collection<int, array<string, mixed>> */
    private function featuredEventCards(): Collection
    {
        return BniEvent::query()
            ->published()
            ->where('is_featured', true)
            ->with('media')
            ->orderByDesc('starts_at')
            ->get()
            ->map(fn (BniEvent $event): array => [
                'title' => $event->title,
                'label' => match ($event->type) {
                    'handover' => 'Lễ chuyển giao',
                    'pickleball' => 'Pickleball',
                    default => 'Sự kiện BNI',
                },
                'date' => $event->starts_at?->translatedFormat('d/m/Y'),
                'venue' => $event->venue,
                'image_url' => $event->bniMediaUrl('hero'),
                'url' => match ($event->type) {
                    'handover' => LocalizedUrl::route('bni.handover'),
                    'pickleball' => LocalizedUrl::route('bni.pickleball'),
                    default => null,
                },
            ]);
    }
}
