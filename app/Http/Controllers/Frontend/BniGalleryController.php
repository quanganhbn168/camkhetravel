<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Models\BniChapter;
use App\Models\BniEvent;
use App\Models\BniGalleryItem;
use App\Models\Comment;
use App\Support\Bni\BniGalleryImageProcessor;
use App\Support\Localization\LocalizedUrl;
use App\Support\Media\MediaUrl;
use App\Support\Seo\FrontendSeoBuilder;
use Awcodes\Curator\Models\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class BniGalleryController extends Controller
{
    public function __construct(
        private readonly FrontendSeoBuilder $seo,
        private readonly BniGalleryImageProcessor $imageProcessor,
    ) {}

    public function index(Request $request): View
    {
        $eventId = $request->integer('event') ?: null;
        $chapterId = $request->integer('chapter') ?: null;
        $events = BniEvent::query()->published()->orderByDesc('starts_at')->get(['id', 'title', 'type']);
        $chapters = BniChapter::query()->where('is_active', true)->orderBy('sort_order')->get(['id', 'name', 'short_name']);
        $galleryItems = BniGalleryItem::query()
            ->published()
            ->with(['event', 'chapter', 'media'])
            ->withCount('approvedComments')
            ->when($eventId, fn ($query) => $query->where('bni_event_id', $eventId))
            ->when($chapterId, fn ($query) => $query->where('bni_chapter_id', $chapterId))
            ->orderByDesc('approved_at')
            ->orderByDesc('id')
            ->paginate(18)
            ->withQueryString();

        return view('frontend.bni-gallery-index', [
            'events' => $events,
            'chapters' => $chapters,
            'galleryItems' => $galleryItems,
            'selectedEventId' => $eventId,
            'selectedChapterId' => $chapterId,
            'seo' => $this->seo->listing(
                'Thư viện ảnh sự kiện BNI | '.$this->seo->siteName(),
                'Xem, gửi và bình luận những khoảnh khắc từ Lễ chuyển giao, Pickleball và các chapter BNI.',
                LocalizedUrl::route('bni.gallery.index'),
            ),
        ]);
    }

    public function show(BniGalleryItem $galleryItem): View
    {
        abort_unless($galleryItem->status === BniGalleryItem::STATUS_APPROVED && $galleryItem->is_active, 404);

        $galleryItem->load([
            'event',
            'chapter',
            'media',
            'uploadedBy',
            'approvedComments' => fn ($query) => $query->with('user')->latest('approved_at'),
        ]);
        $imageUrl = MediaUrl::versioned($galleryItem->media);
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
            'bni_event_id' => ['required', 'integer', 'exists:bni_events,id'],
            'bni_chapter_id' => ['nullable', 'integer', 'exists:bni_chapters,id'],
            'uploader_name' => ['required', 'string', 'max:120'],
            'uploader_email' => ['nullable', 'email', 'max:255'],
            'uploader_phone' => ['required', 'string', 'max:32'],
            'title' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:500'],
            'website' => ['nullable', 'prohibited'],
            'images' => ['required', 'array', 'min:1', 'max:6'],
            'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144', 'dimensions:min_width=320,min_height=320,max_width=6000,max_height=6000'],
        ]);

        $event = BniEvent::query()->published()->findOrFail($data['bni_event_id']);
        $chapter = filled($data['bni_chapter_id'] ?? null)
            ? BniChapter::query()->where('is_active', true)->findOrFail($data['bni_chapter_id'])
            : null;
        $storedPaths = [];

        try {
            DB::transaction(function () use ($request, $data, $event, $chapter, &$storedPaths): void {
                foreach ($request->file('images', []) as $position => $image) {
                    $processed = $this->imageProcessor->store($image);
                    $storedPaths[] = $processed['path'];
                    $media = Media::query()->create([
                        'disk' => 'public',
                        'directory' => $processed['directory'],
                        'visibility' => 'public',
                        'name' => $processed['name'],
                        'path' => $processed['path'],
                        'width' => $processed['width'],
                        'height' => $processed['height'],
                        'size' => $processed['size'],
                        'type' => $processed['type'],
                        'ext' => $processed['ext'],
                        'alt' => trim(strip_tags((string) ($data['title'] ?: 'Khoảnh khắc sự kiện BNI'))),
                        'title' => trim(strip_tags((string) ($data['title'] ?: pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME)))),
                        'caption' => filled($data['caption'] ?? null) ? trim(strip_tags($data['caption'])) : null,
                    ]);

                    BniGalleryItem::query()->create([
                        'bni_event_id' => $event->id,
                        'bni_chapter_id' => $chapter?->id,
                        'uploaded_by_user_id' => $request->user()?->id,
                        'group' => $chapter ? 'chapter' : 'event',
                        'title' => filled($data['title'] ?? null) ? trim(strip_tags($data['title'])) : null,
                        'caption' => filled($data['caption'] ?? null) ? trim(strip_tags($data['caption'])) : null,
                        'uploader_name' => trim(strip_tags($data['uploader_name'])),
                        'uploader_email' => $data['uploader_email'] ?? null,
                        'uploader_phone' => trim(strip_tags($data['uploader_phone'])),
                        'source' => BniGalleryItem::SOURCE_GUEST,
                        'status' => BniGalleryItem::STATUS_PENDING,
                        'media_id' => $media->id,
                        'sort_order' => $position + 1,
                        'is_active' => true,
                    ]);
                }
            });
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($storedPaths);

            report($exception);

            return back()->withInput()->withErrors([
                'images' => 'Hình ảnh chưa được lưu. Anh/chị vui lòng thử lại hoặc chọn tệp nhỏ hơn.',
            ]);
        }

        return back()->with('success', 'Đã nhận '.count($storedPaths).' hình ảnh. Ban tổ chức sẽ duyệt trước khi hiển thị công khai.');
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
}
