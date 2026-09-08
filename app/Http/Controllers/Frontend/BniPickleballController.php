<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BniChapter;
use App\Models\BniRegistration;
use App\Support\Bni\BniExperienceService;
use App\Support\Localization\LocalizedUrl;
use App\Support\Seo\FrontendSeoBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BniPickleballController extends Controller
{
    public function __construct(
        private readonly BniExperienceService $experience,
        private readonly FrontendSeoBuilder $seo,
    ) {}

    public function index(): View
    {
        return view('frontend.bni.pickleball', $this->experience->pickleball() + [
            'seo' => $this->seo->listing(
                'BNI Pickleball | '.$this->seo->siteName(),
                'Giải đấu pickleball trong khuôn khổ Lễ chuyển giao BNI.',
                LocalizedUrl::route('bni.pickleball'),
            ),
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $event = $this->experience->currentEvent('pickleball') ?? abort(404);
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:32'],
            'bni_chapter_id' => ['nullable', 'integer', 'exists:bni_chapters,id'],
            'team_name' => ['nullable', 'string', 'max:255'],
            'skill_level' => ['nullable', 'string', 'max:32'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        if (filled($data['bni_chapter_id'] ?? null)) {
            BniChapter::query()->where('is_active', true)->findOrFail($data['bni_chapter_id']);
        }

        $event->registrations()->create($data + ['status' => BniRegistration::STATUS_PENDING]);

        $message = 'Đăng ký đã được ghi nhận. Ban tổ chức sẽ liên hệ xác nhận.';

        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 201);
        }

        return back()->with('success', $message);
    }
}
