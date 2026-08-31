<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BniChapter;
use App\Support\Bni\BniExperienceService;
use App\Support\Media\MediaUrl;
use App\Support\Seo\FrontendSeoBuilder;
use Illuminate\View\View;

class BniChapterController extends Controller
{
    public function __construct(
        private readonly FrontendSeoBuilder $seo,
        private readonly BniExperienceService $experience,
    ) {}

    public function show(BniChapter $chapter): View
    {
        abort_unless($chapter->is_active, 404);

        $chapter->loadMissing('event');
        abort_if($chapter->event && $chapter->event->status !== 'published', 404);

        return view('frontend.bni-chapter', $this->experience->chapter($chapter) + [
            'seo' => $this->seo->bniChapter(
                $chapter,
                MediaUrl::versioned($chapter->coverMedia) ?: MediaUrl::versioned($chapter->logoMedia),
            ),
        ]);
    }
}
