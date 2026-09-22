@extends('layouts.master')

@push('styles')
    @vite('resources/scss/pages/posts.scss')
@endpush

@use(Illuminate\Support\Str)

@section('content')
    <article>
        <header class="position-relative overflow-hidden archive-hero">
            @if ($post->image_url)
                <img class="position-absolute h-100 w-100 object-fit-cover archive-hero__image" src="{{ $post->image_url }}" alt="" aria-hidden="true">
            @endif
            <div class="position-absolute archive-hero__overlay"></div>
            <div class="container position-relative">
                <p class="d-flex align-items-center overflow-hidden small gap-2"><a class="flex-shrink-0 link-light" href="{{ route('home') }}">Trang chủ</a><span class="mx-1">›</span><a class="flex-shrink-0 link-light" href="{{ route('posts.index') }}">Tin tức</a><span class="mx-1">›</span><span class="text-truncate">{{ $post->title }}</span></p>
                <p class="h1 text-white mb-0">Tin tức</p>
            </div>
        </header>

        <section class="section-space">
            <div class="container align-items-start article-layout">
                <div class="article-content">
                    <h1 class="display-title">{{ $post->title }}</h1>
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
                        @if ($tableOfContents !== [])
                            <nav class="overflow-hidden article-toc my-4" aria-labelledby="inline-table-of-contents-title">
                                <button class="d-flex w-100 align-items-center justify-content-between text-start article-toc__toggle" type="button" data-bs-toggle="collapse" data-bs-target="#inline-table-of-contents" aria-expanded="true" aria-controls="inline-table-of-contents">
                                    <span class="d-block fw-semibold" id="inline-table-of-contents-title">Mục lục bài viết</span>
                                    <span class="d-grid flex-shrink-0 article-toc__indicator" aria-hidden="true">
                                        <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd"/></svg>
                                    </span>
                                </button>
                                <div class="collapse show" id="inline-table-of-contents">
                                    <ol class="px-4 pb-3 mb-0">
                                        @foreach ($tableOfContents as $item)
                                            <li @class(['', 'ms-3' => $item['level'] === 3])>
                                                <a class="d-block fw-medium py-1" href="#{{ $item['id'] }}">{{ $item['title'] }}</a>
                                            </li>
                                        @endforeach
                                    </ol>
                                </div>
                            </nav>

                        @endif
                        <div class="article-prose mt-4">{!! $post->body_html !!}</div>
                    </div>

                    @if ($previousPost || $nextPost)
                        <section class="mx-auto mt-5 border-top pt-4">
                            <div class="row g-3">
                                @if ($previousPost)
                                    <a class="d-flex align-items-center col-md-6 gap-3 text-decoration-none" href="{{ route('slug.show', ['slug' => $previousPost->slug]) }}">
                                        @if ($previousPost->image_url)<img class="flex-shrink-0 object-fit-cover article-adjacent__image" src="{{ $previousPost->image_url }}" alt="" loading="lazy">@endif
                                        <span class="article-content">
                                            <span class="fw-bold text-uppercase small text-primary">Bài viết trước</span>
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
                                            <span class="fw-bold text-uppercase small text-primary">Bài viết tiếp theo</span>
                                            <span class="d-block fw-semibold text-body">{{ $nextPost->title }}</span>
                                            @if ($nextPost->published_at)<time class="d-block small text-body-secondary" datetime="{{ $nextPost->published_at->toDateString() }}">{{ $nextPost->published_at->translatedFormat('d/m/Y') }}</time>@endif
                                        </span>
                                    </a>
                                @endif
                            </div>
                        </section>
                    @endif

                    <section class="mx-auto mt-5" id="binh-luan">
                        <h2 class="display-title text-uppercase h2">Bình luận ({{ $post->approvedComments->count() }})</h2>

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
