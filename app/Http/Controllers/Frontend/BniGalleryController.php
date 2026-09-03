<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Models\BniActivity;
use App\Models\BniChapter;
use App\Models\BniGalleryItem;
use App\Models\Comment;
use App\Support\Bni\BniMediaService;
use App\Support\Localization\LocalizedUrl;
use App\Support\Seo\FrontendSeoBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class BniGalleryController extends Controller
{
    public function __construct(
        private readonly FrontendSeoBuilder $seo,
        private readonly BniMediaService $media,
    ) {}

    public function index(Request $request): View
    {
        $activityOptions = $this->galleryActivities();
        $selectedActivity = $request->integer('activity') ?: null;
        $selectedActivity = $activityOptions->contains('id', $selectedActivity) ? $selectedActivity : null;
        $galleryItems = BniGalleryItem::query()
            ->published()
            ->forActivity($selectedActivity)
            ->with(['activity', 'event', 'media'])
            ->withCount('approvedComments')
            ->orderByDesc('approved_at')
            ->orderByDesc('id')
            ->paginate(18)
            ->withQueryString();

        return view('frontend.bni-gallery-index', [
            'activityOptions' => $activityOptions,
            'galleryItems' => $galleryItems,
            'selectedActivity' => $selectedActivity,
            'seo' => $this->seo->listing(
                'Thư viện ảnh sự kiện BNI | '.$this->seo->siteName(),
                'Xem, gửi và bình luận những khoảnh khắc từ các hoạt động trong chương trình BNI.',
                LocalizedUrl::route('bni.gallery.index'),
            ),
        ]);
    }

    public function show(BniGalleryItem $galleryItem): View
    {
        abort_unless($galleryItem->status === BniGalleryItem::STATUS_APPROVED && $galleryItem->is_active, 404);

        $galleryItem->load([
            'activity',
            'event',
            'media',
            'uploadedBy',
            'approvedComments' => fn ($query) => $query->with('user')->latest('approved_at'),
        ]);
        $imageUrl = $galleryItem->bniMediaUrl('image');
        abort_unless($imageUrl, 404);

        return view('frontend.bni-gallery-show', [
            'galleryItem' => $galleryItem,
            'imageUrl' => $imageUrl,
            'seo' => $this->seo->listing(
                ($galleryItem->title ?: 'Khoảnh khắc sự kiện BNI').' | '.$this->seo->siteName(),
                $galleryItem->caption ?: 'Hình ảnh được chia sẻ trong thư viện sự kiện BNI.',
                LocalizedUrl::route('bni.gallery.show', ['galleryItem' => $galleryItem]),
            ),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'bni_activity_id' => ['required', 'integer', Rule::exists('bni_activities', 'id')->where('is_active', true)],
            'uploader_name' => ['required', 'string', 'max:120'],
            'uploader_email' => ['nullable', 'email', 'max:255'],
            'uploader_phone' => ['required', 'string', 'max:32'],
            'title' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:500'],
            'website' => ['nullable', 'prohibited'],
            'images' => ['required', 'array', 'min:1', 'max:6'],
            'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144', 'dimensions:min_width=320,min_height=320,max_width=6000,max_height=6000'],
        ]);

        $activity = BniActivity::query()
            ->with('event')
            ->whereKey($data['bni_activity_id'])
            ->where('is_active', true)
            ->whereHas('event', fn ($query) => $query->published())
            ->first();

        if (! $activity || ! $activity->event) {
            throw ValidationException::withMessages([
                'bni_activity_id' => 'Hoạt động này hiện không nhận ảnh.',
            ]);
        }

        $event = $activity->event;
        $chapterId = BniChapter::query()
            ->where('is_active', true)
            ->whereKey($request->user()?->bni_chapter_id ?? 0)
            ->value('id');
        $createdItems = collect();

        try {
            foreach ($request->file('images', []) as $position => $image) {
                $title = filled($data['title'] ?? null)
                    ? trim(strip_tags($data['title']))
                    : null;
                $caption = filled($data['caption'] ?? null)
                    ? trim(strip_tags($data['caption']))
                    : null;
                $item = BniGalleryItem::query()->create([
                    'bni_event_id' => $event->id,
                    'bni_activity_id' => $activity->id,
                    'bni_chapter_id' => $chapterId,
                    'uploaded_by_user_id' => $request->user()?->id,
                    'group' => $activity->type,
                    'title' => $title,
                    'caption' => $caption,
                    'uploader_name' => trim(strip_tags($data['uploader_name'])),
                    'uploader_email' => $data['uploader_email'] ?? null,
                    'uploader_phone' => trim(strip_tags($data['uploader_phone'])),
                    'source' => BniGalleryItem::SOURCE_GUEST,
                    'status' => BniGalleryItem::STATUS_PENDING,
                    'sort_order' => $position + 1,
                    'is_active' => true,
                ]);
                $createdItems->push($item);

                $this->media->attachUpload(
                    $item,
                    $image,
                    'image',
                    $title ?: pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME),
                    array_filter([
                        'alt' => $title ?: 'Khoảnh khắc sự kiện BNI',
                        'caption' => $caption,
                    ]),
                );
            }
        } catch (Throwable $exception) {
            $createdItems->each->delete();

            report($exception);

            return back()->withInput()->withErrors([
                'images' => 'Hình ảnh chưa được lưu. Anh/chị vui lòng thử lại hoặc chọn tệp nhỏ hơn.',
            ]);
        }

        return back()->with('success', 'Đã nhận '.$createdItems->count().' hình ảnh. Ban tổ chức sẽ duyệt trước khi hiển thị công khai.');
    }

    public function comment(StoreCommentRequest $request, BniGalleryItem $galleryItem): RedirectResponse
    {
        abort_unless($galleryItem->status === BniGalleryItem::STATUS_APPROVED && $galleryItem->is_active, 404);

        $data = $request->validated();
        $galleryItem->comments()->create([
            'user_id' => $request->user()?->id,
            'author_name' => trim(strip_tags($data['author_name'])),
            'author_email' => $data['author_email'] ?? null,
            'body' => trim(strip_tags($data['body'])),
            'status' => Comment::STATUS_PENDING,
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 512, ''),
        ]);

        return back()->with('success', 'Bình luận đã được gửi và đang chờ Ban tổ chức duyệt.');
    }

    /** @return Collection<int, array{id: int, title: string, event_title: string, select_label: string}> */
    private function galleryActivities(): Collection
    {
        return BniActivity::query()
            ->where('is_active', true)
            ->whereHas('event', fn ($query) => $query->published())
            ->with('event')
            ->orderBy('bni_event_id')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (BniActivity $activity): array => [
                'id' => (int) $activity->getKey(),
                'title' => $activity->title,
                'event_title' => $activity->event?->title ?: 'Sự kiện BNI',
                'select_label' => $activity->title.' — '.($activity->event?->title ?: 'Sự kiện BNI'),
            ]);
    }
}
