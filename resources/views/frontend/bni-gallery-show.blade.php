@extends('layouts.master')

@use(App\Support\Localization\LocalizedUrl)
@use(Illuminate\Support\Str)

@section('body_class', 'bni-experience-page bni-gallery-detail-page')
@section('main_id', 'bni-gallery-detail-main')
@section('main_class', 'bni-experience-main overflow-x-clip')

@section('content')
    <section class="bni-gallery-detail-hero">
        <div class="site-shell">
            <a class="bni-back-link" href="{{ LocalizedUrl::route('bni.gallery.index') }}">← Trở lại thư viện ảnh</a>
            <p class="bni-experience-kicker">{{ $galleryItem->galleryGroupLabel() }}</p>
            <h1>{{ $galleryItem->title ?: 'Khoảnh khắc kết nối BNI' }}</h1>
        </div>
    </section>

    <section class="bni-section bni-gallery-detail">
        <div class="site-shell bni-gallery-detail__layout">
            <div>
                <figure class="bni-gallery-detail__image">
                    <img src="{{ $imageUrl }}" alt="{{ $galleryItem->title ?: 'Khoảnh khắc sự kiện BNI' }}">
                    @if ($galleryItem->caption)<figcaption>{{ $galleryItem->caption }}</figcaption>@endif
                </figure>

                <section class="bni-member-comments bni-gallery-comments" id="binh-luan" aria-labelledby="bni-gallery-comments-title">
                    <h2 id="bni-gallery-comments-title">Bình luận ({{ $galleryItem->approvedComments->count() }})</h2>
                    <div class="bni-member-comments__list">
                        @forelse ($galleryItem->approvedComments as $comment)
                            <article>
                                <span>{{ Str::upper(Str::substr($comment->author_name, 0, 1)) }}</span>
                                <div><h3>{{ $comment->author_name }}</h3><p>{{ $comment->body }}</p></div>
                            </article>
                        @empty
                            <p class="bni-empty-copy">Chưa có bình luận được duyệt. Anh/chị có thể gửi lời nhắn đầu tiên.</p>
                        @endforelse
                    </div>

                    <form class="bni-form" method="POST" action="{{ LocalizedUrl::route('bni.gallery.comments.store', ['galleryItem' => $galleryItem]) }}">
                        @csrf
                        <div>
                            <label for="gallery-comment-name">Họ và tên</label>
                            <input id="gallery-comment-name" name="author_name" value="{{ old('author_name', auth()->user()?->name) }}" required>
                            @error('author_name')<p class="bni-form-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="gallery-comment-email">Email (không bắt buộc)</label>
                            <input id="gallery-comment-email" type="email" name="author_email" value="{{ old('author_email', auth()->user()?->email) }}">
                            @error('author_email')<p class="bni-form-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="bni-form__full">
                            <label for="gallery-comment-body">Bình luận</label>
                            <textarea id="gallery-comment-body" name="body" rows="4" required>{{ old('body') }}</textarea>
                            @error('body')<p class="bni-form-error">{{ $message }}</p>@enderror
                        </div>
                        <button class="bni-button bni-button--red bni-form__full" type="submit">Gửi bình luận để duyệt</button>
                    </form>
                </section>
            </div>

            <aside class="bni-gallery-detail__meta">
                <img src="{{ asset('bni-logo-red.svg') }}" alt="BNI">
                <p>Album</p>
                <strong>{{ $galleryItem->galleryGroupLabel() }}</strong>
                <p>Nguồn ảnh</p>
                <strong>{{ $galleryItem->publicSourceLabel() }}</strong>
                @if ($galleryItem->uploader_name)
                    <p>Người chia sẻ</p><strong>{{ $galleryItem->uploader_name }}</strong>
                @endif
                @if ($galleryItem->event)
                    <p>Sự kiện</p><strong>{{ $galleryItem->event->title }}</strong>
                @endif
                <a class="bni-button bni-button--red" href="{{ LocalizedUrl::route('bni.gallery.index') }}#gui-anh">Gửi ảnh của bạn</a>
            </aside>
        </div>
    </section>
@endsection
