<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Support\Bni\BniExperienceService;
use App\Support\Localization\LocalizedUrl;
use App\Support\Seo\FrontendSeoBuilder;
use Illuminate\View\View;

class BniHandoverController extends Controller
{
    public function __construct(
        private readonly FrontendSeoBuilder $seo,
        private readonly BniExperienceService $experience,
    ) {}

    public function __invoke(): View
    {
        $data = $this->experience->handover();

        return view('frontend.bni.handover', $data + [
            'seo' => $this->seo->listing(
                'Lễ chuyển giao BNI | '.$this->seo->siteName(),
                'Lễ chuyển giao BNI, kết nối 4 chapter KINHBAC, KBG, IMPACT và FAMOUS.',
                LocalizedUrl::route('bni.handover'),
                image: ($data['event'] ?? null)?->bniMediaUrl('seo_image') ?: ($data['heroImageUrl'] ?? null),
            ),
        ]);
    }
}
