<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Solution;
use App\Support\Pages\SystemPageProfileResolver;
use App\Support\Seo\FrontendSeoBuilder;
use Illuminate\View\View;

class SolutionsController extends Controller
{
    private const INTRO = 'Các phương án được xây dựng theo hiện trạng, tiêu chuẩn kỹ thuật và yêu cầu vận hành của từng công trình.';

    public function __construct(
        private readonly FrontendSeoBuilder $seo,
        private readonly SystemPageProfileResolver $systemPages,
    ) {}

    public function __invoke(): View
    {
        $page = $this->systemPages->require('solutions');

        return view('frontend.solutions.index', [
            'page' => $page,
            'intro' => self::INTRO,
            'solutions' => Solution::published()->with(['category', 'slugs', 'curatorMedia'])->orderBy('sort_order')->orderBy('id')->paginate(12),
            'pageBannerUrl' => $page['banner_url'],
            'seo' => $this->seo->systemPage($page, 'solutions.index'),
        ]);
    }

    public function show(Solution $solution): View
    {
        $solution->load(['category', 'curatorMedia', 'bannerMedia', 'seoImageMedia']);
        abort_unless($solution->is_active && $solution->category?->is_active, 404);

        return view('frontend.solutions.show', [
            'solution' => $solution,
            'bodyHtml' => (string) str((string) $solution->body)->sanitizeHtml(),
            'seo' => $this->seo->solution($solution),
        ]);
    }
}
