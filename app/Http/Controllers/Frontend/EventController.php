<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BniEvent;
use App\Support\Events\EventCatalog;
use App\Support\Localization\LocalizedUrl;
use App\Support\Seo\FrontendSeoBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function __construct(private readonly FrontendSeoBuilder $seo, private readonly EventCatalog $catalog) {}

    public function index(Request $request): View
    {
        $filter = $request->query('trang-thai', 'tat-ca');
        abort_unless(in_array($filter, ['tat-ca', 'sap-dien-ra', 'da-dien-ra'], true), 404);

        $events = BniEvent::query()->published()->with(['media', 'slides.media'])
            ->when($filter === 'sap-dien-ra', fn ($query) => $query->whereRaw('COALESCE(ends_at, starts_at) >= ?', [now()]))
            ->when($filter === 'da-dien-ra', fn ($query) => $query->whereRaw('COALESCE(ends_at, starts_at) < ?', [now()]))
            ->orderByDesc('is_featured')->orderByDesc('starts_at')->orderByDesc('id')
            ->paginate(12)->withQueryString()
            ->through(fn (BniEvent $event) => $this->catalog->present($event));

        return view('frontend.events.index', [
            'events' => $events,
            'filter' => $filter,
            'filters' => ['tat-ca' => 'Tất cả sự kiện', 'sap-dien-ra' => 'Sắp & đang diễn ra', 'da-dien-ra' => 'Đã diễn ra'],
            'seo' => $this->seo->listing('Sự kiện | Lễ chuyển giao BNI', 'Khám phá các sự kiện kết nối doanh nghiệp, giao lưu và hoạt động trong cộng đồng BNI.', LocalizedUrl::route('bni.events.index')),
        ]);
    }

    public function show(BniEvent $event): View|RedirectResponse
    {
        abort_unless($event->status === 'published', 404);
        $canonical = $this->catalog->url($event);
        if ($canonical !== LocalizedUrl::route('bni.events.show', ['event' => $event->slug])) {
            return redirect()->to($canonical);
        }

        $event->load(['media', 'slides.media', 'scheduleDays' => fn ($query) => $query->where('is_active', true)->with('items')]);
        $details = $this->catalog->present($event);

        return view('frontend.events.show', [
            'event' => $event,
            'details' => $details,
            'seo' => $this->seo->listing($event->title, $details['summary'] ?: $event->title, $canonical),
        ]);
    }
}
