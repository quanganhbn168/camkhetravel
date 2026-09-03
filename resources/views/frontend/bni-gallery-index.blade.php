@extends('layouts.master')

@use(App\Support\Localization\LocalizedUrl)
@section('body_class', 'bni-experience-page bni-gallery-page')
@section('main_id', 'bni-gallery-main')
@section('main_class', 'bni-experience-main overflow-x-clip')

@section('content')
    <section class="bni-gallery-hero">
        <div class="site-shell bni-gallery-hero__content">
            <a class="bni-back-link" href="{{ LocalizedUrl::route('bni.handover') }}">← Website sự kiện BNI</a>
            <p class="bni-experience-kicker">KHOẢNH KHẮC KẾT NỐI</p>
            <h1>Thư viện ảnh cộng đồng BNI</h1>
            <p>Khám phá ảnh theo từng hoạt động của chương trình, gửi khoảnh khắc của anh/chị và cùng để lại bình luận sau khi nội dung được duyệt.</p>
            <div class="bni-gallery-hero__actions">
                <a class="bni-button bni-button--white" href="#thu-vien">Xem thư viện</a>
                <a class="bni-button bni-button--ghost" href="#gui-anh">Gửi ảnh cá nhân</a>
            </div>
        </div>
    </section>

    <section class="bni-section bni-gallery-browser" id="thu-vien" aria-labelledby="bni-gallery-browser-title">
        <div class="site-shell">
            <div class="bni-news__heading">
                <div>
                    <p class="bni-experience-kicker">ĐÃ KIỂM DUYỆT</p>
                    <h2 id="bni-gallery-browser-title">Khoảnh khắc từ các sự kiện</h2>
                    <p class="bni-section-heading__description">{{ $galleryItems->total() }} hình ảnh đang hiển thị công khai.</p>
                </div>
                <form class="bni-gallery-filter" method="GET" action="{{ LocalizedUrl::route('bni.gallery.index') }}">
                    <label>
                        <span>Hoạt động / album</span>
                        <select name="activity">
                            <option value="">Tất cả hoạt động</option>
                            @foreach ($activityOptions as $activity)
                                <option value="{{ $activity['id'] }}" @selected($selectedActivity === $activity['id'])>{{ $activity['select_label'] }}</option>
                            @endforeach
                        </select>
                    </label>
                    <button class="bni-button bni-button--red" type="submit">Lọc ảnh</button>
                </form>
            </div>

            <div class="bni-community-gallery-grid">
                @forelse ($galleryItems as $galleryItem)
                    @php($imageUrl = $galleryItem->bniMediaUrl('image'))
                    @if ($imageUrl)
                        <article class="bni-community-gallery-card">
                            <a class="bni-community-gallery-card__image" href="{{ LocalizedUrl::route('bni.gallery.show', ['galleryItem' => $galleryItem]) }}">
                                <img src="{{ $imageUrl }}" alt="{{ $galleryItem->title ?: 'Khoảnh khắc sự kiện BNI' }}" loading="lazy">
                                <span>{{ $galleryItem->galleryGroupLabel() }}</span>
                            </a>
                            <div class="bni-community-gallery-card__body">
                                <p>{{ $galleryItem->publicSourceLabel() }}</p>
                                <h3><a href="{{ LocalizedUrl::route('bni.gallery.show', ['galleryItem' => $galleryItem]) }}">{{ $galleryItem->title ?: 'Khoảnh khắc kết nối BNI' }}</a></h3>
                                @if ($galleryItem->caption)<span>{{ $galleryItem->caption }}</span>@endif
                                <div><span>{{ $galleryItem->approved_comments_count }} bình luận</span><strong>Xem ảnh →</strong></div>
                            </div>
                        </article>
                    @endif
                @empty
                    <div class="bni-gallery-empty">
                        <img src="{{ asset('bni-logo-red.svg') }}" alt="BNI">
                        <h3>Thư viện đang chờ những khoảnh khắc đầu tiên</h3>
                        <p>Ảnh từ các hoạt động BNI sẽ hiển thị tại đây sau khi được duyệt.</p>
                    </div>
                @endforelse
            </div>

            @if ($galleryItems->hasPages())
                <div class="bni-gallery-pagination">{{ $galleryItems->links() }}</div>
            @endif
        </div>
    </section>

    <section class="bni-section bni-gallery-upload" id="gui-anh" aria-labelledby="bni-gallery-upload-title">
        <div class="site-shell bni-gallery-upload__layout">
            <div class="bni-gallery-upload__intro">
                <p class="bni-experience-kicker">CHIA SẺ KHOẢNH KHẮC</p>
                <h2 id="bni-gallery-upload-title">Gửi ảnh của anh/chị</h2>
                <p>Mỗi lần có thể gửi tối đa 6 ảnh JPG, PNG hoặc WebP. Ảnh sẽ được chuẩn hóa kích thước và ở trạng thái chờ duyệt; thông tin liên hệ chỉ dùng để Ban tổ chức xác minh khi cần.</p>
                <ul>
                    <li>Ảnh rõ nét, liên quan trực tiếp đến hoạt động BNI.</li>
                    <li>Không đăng thông tin riêng tư hoặc nội dung chưa được phép chia sẻ.</li>
                    <li>Bình luận và ảnh đều được kiểm duyệt trước khi công khai.</li>
                </ul>
            </div>
            <form class="bni-form bni-gallery-upload__form" method="POST" action="{{ LocalizedUrl::route('bni.gallery.store') }}" enctype="multipart/form-data" x-data="{
                fileCount: 0,
                previews: [],
                selectFiles(event) {
                    this.previews.forEach((preview) => URL.revokeObjectURL(preview.url));
                    this.previews = Array.from(event.target.files).map((file) => ({ name: file.name, url: URL.createObjectURL(file) }));
                    this.fileCount = this.previews.length;
                },
                clearFiles() {
                    if (! window.confirm('Xóa toàn bộ ảnh đã chọn?')) return;
                    this.previews.forEach((preview) => URL.revokeObjectURL(preview.url));
                    this.previews = [];
                    this.fileCount = 0;
                    this.$refs.galleryImages.value = '';
                }
            }">
                @csrf
                <div class="bni-form-honeypot" aria-hidden="true">
                    <label for="gallery-website">Website</label>
                    <input id="gallery-website" name="website" type="text" tabindex="-1" autocomplete="off">
                </div>
                <div>
                    <label for="gallery-activity">Hoạt động / album ảnh</label>
                    <select id="gallery-activity" name="bni_activity_id" required>
                        <option value="">Chọn hoạt động</option>
                        @foreach ($activityOptions as $activity)
                            <option value="{{ $activity['id'] }}" @selected((int) old('bni_activity_id') === $activity['id'])>{{ $activity['select_label'] }}</option>
                        @endforeach
                    </select>
                    @error('bni_activity_id')<p class="bni-form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="gallery-uploader-name">Họ và tên</label>
                    <input id="gallery-uploader-name" name="uploader_name" value="{{ old('uploader_name', auth()->user()?->name) }}" required>
                    @error('uploader_name')<p class="bni-form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="gallery-uploader-phone">Số điện thoại</label>
                    <input id="gallery-uploader-phone" type="tel" name="uploader_phone" value="{{ old('uploader_phone') }}" required>
                    @error('uploader_phone')<p class="bni-form-error">{{ $message }}</p>@enderror
                </div>
                <div class="bni-form__full">
                    <label for="gallery-uploader-email">Email (không bắt buộc)</label>
                    <input id="gallery-uploader-email" type="email" name="uploader_email" value="{{ old('uploader_email', auth()->user()?->email) }}">
                    @error('uploader_email')<p class="bni-form-error">{{ $message }}</p>@enderror
                </div>
                <div class="bni-form__full">
                    <label for="gallery-title">Tiêu đề bộ ảnh</label>
                    <input id="gallery-title" name="title" value="{{ old('title') }}" placeholder="Ví dụ: Khoảnh khắc giao lưu tại sự kiện">
                    @error('title')<p class="bni-form-error">{{ $message }}</p>@enderror
                </div>
                <div class="bni-form__full">
                    <label for="gallery-caption">Chú thích</label>
                    <textarea id="gallery-caption" name="caption" rows="3" placeholder="Thông tin ngắn giúp mọi người hiểu hơn về khoảnh khắc này">{{ old('caption') }}</textarea>
                    @error('caption')<p class="bni-form-error">{{ $message }}</p>@enderror
                </div>
                <div class="bni-form__full bni-gallery-file-field">
                    <label for="gallery-images">Chọn ảnh</label>
                    <input id="gallery-images" x-ref="galleryImages" type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple required @change="selectFiles($event)">
                    <div class="bni-gallery-file-actions">
                        <p><span x-text="fileCount"></span> ảnh đã chọn · tối đa 6 ảnh, mỗi ảnh tối đa 6MB.</p>
                        <button type="button" x-show="fileCount > 0" x-cloak @click="clearFiles">Xóa tất cả</button>
                    </div>
                    <div class="bni-gallery-file-preview" x-show="previews.length > 0" x-cloak>
                        <template x-for="preview in previews" :key="preview.url">
                            <figure><img :src="preview.url" alt=""><figcaption x-text="preview.name"></figcaption></figure>
                        </template>
                    </div>
                    @error('images')<p class="bni-form-error">{{ $message }}</p>@enderror
                    @error('images.*')<p class="bni-form-error">{{ $message }}</p>@enderror
                </div>
                <button class="bni-button bni-button--red bni-form__full" type="submit">Gửi ảnh để duyệt</button>
            </form>
        </div>
    </section>
@endsection
