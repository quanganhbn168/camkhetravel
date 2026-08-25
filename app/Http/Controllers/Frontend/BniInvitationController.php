<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BniInvitation;
use App\Support\Localization\LocalizedUrl;
use App\Support\Media\MediaUrl;
use App\Support\Seo\FrontendSeoBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BniInvitationController extends Controller
{
    public function __construct(private readonly FrontendSeoBuilder $seo) {}

    public function show(BniInvitation $invitation): View
    {
        $invitation->load(['event.heroMedia', 'chapter']);

        return view('frontend.bni-invitation', [
            'invitation' => $invitation,
            'heroImageUrl' => MediaUrl::versioned($invitation->event?->heroMedia),
            'seo' => $this->seo->listing(
                'Thư mời Lễ chuyển giao BNI',
                'Thư mời dành riêng cho '.$invitation->guest_name.'.',
                LocalizedUrl::route('bni.invitations.show', ['invitation' => $invitation]),
            ),
        ]);
    }

    public function rsvp(Request $request, BniInvitation $invitation): RedirectResponse
    {
        $data = $request->validate([
            'rsvp_status' => ['required', 'in:attending,declined'],
            'guest_count' => ['required', 'integer', 'min:1', 'max:10'],
            'rsvp_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $invitation->update($data + ['responded_at' => now()]);

        return back()->with('success', 'Cảm ơn anh/chị đã phản hồi thư mời.');
    }
}
