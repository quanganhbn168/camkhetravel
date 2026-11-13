@extends('layouts.master')
@push('schema')    <x-json-ld :seo="$seo" />@endpush

@push('styles')
    @vite(['resources/css/pages/posts.css', 'resources/css/pages/post-detail.css'])
@endpush

@push('scripts')
    @vite('resources/js/pages/post.js')
@endpush

@use(Illuminate\Support\Str)

@section('body_class', 'post-detail-page')

@section('content')
    <article>
        <header class="position-relative overflow-hidden archive-hero">
            @if ($post->image_url)
                <img class="position-absolute h-100 w-100 object-fit-cover archive-hero__image" src="{{ $post->image_url }}" alt="" aria-hidden="true">
            @endif
            <div class="position-absolute archive-hero__overlay"></div>
            <div class="container position-relative">
                <p class="d-flex align-items-center overflow-hidden small gap-2"><a class="flex-shrink-0 link-light" href="{{ route('home') }}">Trang chủ</a><span class="mx-1">›</span><a class="flex-shrink-0 link-light" href="{{ route('posts.index') }}">Tin tức</a><span class="mx-1">›</span><span class="text-truncate">{{ $post->title }}</span></p>
                <p class="h3 text-white mb-0">Tin tức</p>
            </div>
        </header>

        <section class="section-space">
            <div class="container align-items-start article-layout">
                <div class="article-content">
                    <h1>{{ $post->title }}</h1>
                    <div class="d-flex flex-wrap align-items-center fw-medium gap-3 small text-body-secondary my-3">
                        @if ($post->published_at)<time class="d-inline-flex align-items-center gap-2" datetime="{{ $post->published_at->toDateString() }}"><i class="fa-regular fa-calendar-days text-primary" aria-hidden="true"></i>{{ $post->published_at->translatedFormat('d/m/Y') }}</time>@endif
                        <span class="d-inline-flex align-items-center gap-2"><i class="fa-regular fa-user text-primary" aria-hidden="true"></i>{{ $website->company_name ?: $website->site_name }}</span>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-4" aria-label="Chia sẻ bài viết">
                        <span class="fw-semibold text-uppercase small me-2">Chia sẻ</span>
                        <a class="d-grid share-link" href="{{ $share['facebook'] }}" target="_blank" rel="noopener noreferrer" aria-label="Chia sẻ bài viết lên Facebook">
                            <i class="fa-brands fa-facebook-f" aria-hidden="true"></i>
                        </a>
                        <a class="d-grid share-link" href="{{ $share['linkedin'] }}" target="_blank" rel="noopener noreferrer" aria-label="Chia sẻ bài viết lên LinkedIn">
                            <i class="fa-brands fa-linkedin-in" aria-hidden="true"></i>
                        </a>
                        <a class="d-grid share-link" href="{{ $share['x'] }}" target="_blank" rel="noopener noreferrer" aria-label="Chia sẻ bài viết lên X">
                            <span class="fw-bold" aria-hidden="true">X</span>
                        </a>
                    </div>

                    @if ($post->image_url)
                        <div class="overflow-hidden article-cover"><img class="h-100 w-100 object-fit-cover" src="{{ $post->image_url }}" alt="{{ $post->title }}"></div>
                    @endif

                    <div class="mx-auto">
                        @if ($post->excerpt)
                            <p class="fw-medium lead mt-4">{{ $post->excerpt }}</p>
                        @endif
                        <x-content-toc :toc="$tableOfContents" title="Mục lục bài viết" id="post-toc" class="my-4" />
                        <div class="article-prose post-prose mt-4">{!! $post->body_html !!}</div>
                        <x-hashtags :tags="$post->tags" class="mt-4" />

                        {{-- Khung thông tin liên hệ mặc định ở cuối mọi bài viết, lấy từ Cài đặt website. --}}
                        <x-contact-box class="mt-5" />
                    </div>

                    @if ($previousPost || $nextPost)
                        <section class="mx-auto mt-5 border-top pt-4">
                            <div class="row g-3">
                                @if ($previousPost)
                                    <a class="d-flex align-items-center col-md-6 gap-3 text-decoration-none" href="{{ route('slug.show', ['slug' => $previousPost->slug]) }}">
                                        @if ($previousPost->image_url)<img class="flex-shrink-0 object-fit-cover article-adjacent__image" src="{{ $previousPost->image_url }}" alt="" loading="lazy">@endif
                                        <span class="article-content">
                                            <span class="fw-bold text-uppercase small text-primary-emphasis">Bài viết trước</span>
                                            <span class="d-block fw-semibold text-body">{{ $previousPost->title }}</span>
                                            @if ($previousPost->published_at)<time class="d-block small text-body-secondary" datetime="{{ $previousPost->published_at->toDateString() }}">{{ $previousPost->published_at->translatedFormat('d/m/Y') }}</time>@endif
                                        </span>
                                    </a>
                                @else
                                    <span></span>
                                @endif

                                @if ($nextPost)
                                    <a class="d-flex flex-row-reverse align-items-center text-end col-md-6 gap-3 text-decoration-none" href="{{ route('slug.show', ['slug' => $nextPost->slug]) }}">
                                        @if ($nextPost->image_url)<img class="flex-shrink-0 object-fit-cover article-adjacent__image" src="{{ $nextPost->image_url }}" alt="" loading="lazy">@endif
                                        <span class="article-content">
                                            <span class="fw-bold text-uppercase small text-primary-emphasis">Bài viết tiếp theo</span>
                                            <span class="d-block fw-semibold text-body">{{ $nextPost->title }}</span>
                                            @if ($nextPost->published_at)<time class="d-block small text-body-secondary" datetime="{{ $nextPost->published_at->toDateString() }}">{{ $nextPost->published_at->translatedFormat('d/m/Y') }}</time>@endif
                                        </span>
                                    </a>
                                @endif
                            </div>
                        </section>
                    @endif

                    @if ($relatedPosts->isNotEmpty())
                        <section class="mx-auto mt-5" id="bai-viet-lien-quan">
                            <h2 class="post-section-title">Bài viết liên quan</h2>
                            <div class="row row-cols-1 row-cols-md-3 g-3">
                                @foreach ($relatedPosts as $relatedPost)
                                    <div class="col">@include('frontend.partials.post-card', ['post' => $relatedPost])</div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    <section class="mx-auto mt-5" id="binh-luan">
                        <h2 class="post-section-title">Bình luận <span>({{ $post->approvedComments->count() }})</span></h2>

                        <div class="review-layout">
                            <div class="d-grid gap-4">
                                @forelse ($post->approvedComments as $comment)
                                    <article class="d-flex gap-3">
                                        <span class="d-grid flex-shrink-0 fw-bold review-avatar" aria-hidden="true">{{ Str::upper(Str::substr($comment->author_name, 0, 1)) }}</span>
                                        <div class="article-content">
                                            <div class="d-flex flex-wrap align-items-baseline gap-2">
                                                <h3 class="fw-semibold h6 mb-0">{{ $comment->author_name }}</h3>
                                                <time class="fw-medium small text-body-secondary" datetime="{{ $comment->approved_at?->toDateString() }}">{{ $comment->approved_at?->translatedFormat('d/m/Y H:i') }}</time>
                                            </div>
                                            <p class="mt-2 mb-0">{{ $comment->body }}</p>
                                        </div>
                                    </article>
                                @empty
                                    <p class="empty-state w-100">Chưa có bình luận nào. Hãy là người đầu tiên chia sẻ ý kiến.</p>
                                @endforelse
                            </div>

                            <x-comment-form :post="$post" :compact="true" />
                        </div>
                    </section>
                </div>

                @include('frontend.partials.news-sidebar')
            </div>
        </section>
    </article>
@endsection
