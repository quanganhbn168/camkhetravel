@extends('layouts.master')

@push('styles')
    @vite('resources/css/frontend/pages/posts-show.css')
@endpush

@use(App\Support\Localization\LocalizedUrl)
@use(Illuminate\Support\Str)

@section('content')
    <article>
        <header class="position-relative overflow-hidden site-posts-show__element-1">
            @if ($post->image_url)
                <img class="position-absolute h-100 w-100 object-fit-cover site-posts-show__media-2" src="{{ $post->image_url }}" alt="" aria-hidden="true">
            @endif
            <div class="position-absolute site-posts-show__div-3"></div>
            <div class="site-container w-100 mx-auto site-posts-show__div-4">
                <p class="d-flex align-items-center overflow-hidden site-posts-show__copy-5"><a class="flex-shrink-0 site-posts-show__action-6" href="{{ LocalizedUrl::route('home') }}">{{ __('site.home') }}</a><span class="site-posts-show__copy-7">›</span><a class="flex-shrink-0 site-posts-show__action-6" href="{{ LocalizedUrl::route('posts.index') }}">{{ __('site.news') }}</a><span class="site-posts-show__copy-7">›</span><span class="site-posts-show__copy-8">{{ $post->title }}</span></p>
                <p class="site-posts-show__copy-9">Tin tức</p>
            </div>
        </header>

        <section class="section-space">
            <div class="site-container w-100 mx-auto align-items-start site-posts-show__div-10">
                <div class="site-posts-show__div-11">
                    <h1 class="display-title site-posts-show__heading-12">{{ $post->title }}</h1>
                    <div class="d-flex flex-wrap align-items-center fw-medium site-posts-show__div-13">
                        @if ($post->published_at)<time class="d-inline-flex align-items-center site-posts-show__copy-14" datetime="{{ $post->published_at->toDateString() }}"><i class="fa-regular fa-calendar-days site-posts-show__element-15" aria-hidden="true"></i>{{ $post->published_at->translatedFormat('d/m/Y') }}</time>@endif
                        <span class="d-inline-flex align-items-center site-posts-show__copy-14"><i class="fa-regular fa-user site-posts-show__element-15" aria-hidden="true"></i>{{ $website->company_name ?: $website->site_name }}</span>
                    </div>
                    <div class="d-flex flex-wrap align-items-center site-posts-show__div-16" aria-label="Chia sẻ bài viết">
                        <span class="fw-semibold text-uppercase site-posts-show__copy-17">Chia sẻ</span>
                        <a class="d-grid site-posts-show__action-18" href="{{ $share['facebook'] }}" target="_blank" rel="noopener noreferrer" aria-label="Chia sẻ bài viết lên Facebook">
                            <i class="fa-brands fa-facebook-f" aria-hidden="true"></i>
                        </a>
                        <a class="d-grid site-posts-show__action-19" href="{{ $share['linkedin'] }}" target="_blank" rel="noopener noreferrer" aria-label="Chia sẻ bài viết lên LinkedIn">
                            <i class="fa-brands fa-linkedin-in" aria-hidden="true"></i>
                        </a>
                        <a class="d-grid site-posts-show__action-20" href="{{ $share['x'] }}" target="_blank" rel="noopener noreferrer" aria-label="Chia sẻ bài viết lên X">
                            <span class="fw-bold site-posts-show__copy-21" aria-hidden="true">X</span>
                        </a>
                    </div>

                    @if ($post->image_url)
                        <div class="overflow-hidden site-posts-show__div-24"><img class="h-100 w-100 object-fit-cover" src="{{ $post->image_url }}" alt="{{ $post->title }}"></div>
                    @endif

                    <div class="mx-auto site-posts-show__div-26">
                        @if ($post->excerpt)
                            <p class="fw-medium site-posts-show__copy-27">{{ $post->excerpt }}</p>
                        @endif
                        @if ($tableOfContents !== [])
                            <nav class="overflow-hidden site-posts-show__nav-28" aria-labelledby="inline-table-of-contents-title">
                                <button class="d-flex w-100 align-items-center justify-content-between text-start site-posts-show__action-29" type="button" data-bs-toggle="collapse" data-bs-target="#inline-table-of-contents" aria-expanded="true" aria-controls="inline-table-of-contents">
                                    <span class="d-block site-posts-show__copy-30" id="inline-table-of-contents-title">Mục lục bài viết</span>
                                    <span class="d-grid flex-shrink-0 site-posts-show__copy-31" aria-hidden="true">
                                        <svg class="site-posts-show__media-32" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd"/></svg>
                                    </span>
                                </button>
                                <div class="collapse show" id="inline-table-of-contents">
                                    <ol class="site-posts-show__element-33">
                                        @foreach ($tableOfContents as $item)
                                            <li @class(['site-posts-show__element-65', 'site-posts-show__element-66' => $item['level'] === 3])>
                                                <a class="d-block fw-medium site-posts-show__action-34" href="#{{ $item['id'] }}">{{ $item['title'] }}</a>
                                            </li>
                                        @endforeach
                                    </ol>
                                </div>
                            </nav>

                        @endif
                        <div class="article-prose site-posts-show__div-45">{!! $post->body_html !!}</div>
                    </div>

                    @if ($previousPost || $nextPost)
                        <section class="mx-auto site-posts-show__section-46">
                            <div class="site-posts-show__div-47">
                                @if ($previousPost)
                                    <a class="site-hover-group d-flex align-items-center site-posts-show__action-48" href="{{ LocalizedUrl::post($previousPost) }}">
                                        @if ($previousPost->image_url)<img class="flex-shrink-0 object-fit-cover site-posts-show__media-49" src="{{ $previousPost->image_url }}" alt="" loading="lazy">@endif
                                        <span class="site-posts-show__div-11">
                                            <span class="fw-bold text-uppercase site-posts-show__copy-50">Bài viết trước</span>
                                            <span class="d-block fw-semibold site-posts-show__copy-51">{{ $previousPost->title }}</span>
                                            @if ($previousPost->published_at)<time class="d-block site-posts-show__copy-52" datetime="{{ $previousPost->published_at->toDateString() }}">{{ $previousPost->published_at->translatedFormat('d/m/Y') }}</time>@endif
                                        </span>
                                    </a>
                                @else
                                    <span></span>
                                @endif

                                @if ($nextPost)
                                    <a class="site-hover-group d-flex flex-row-reverse align-items-center text-end site-posts-show__action-53" href="{{ LocalizedUrl::post($nextPost) }}">
                                        @if ($nextPost->image_url)<img class="flex-shrink-0 object-fit-cover site-posts-show__media-49" src="{{ $nextPost->image_url }}" alt="" loading="lazy">@endif
                                        <span class="site-posts-show__div-11">
                                            <span class="fw-bold text-uppercase site-posts-show__copy-50">Bài viết tiếp theo</span>
                                            <span class="d-block fw-semibold site-posts-show__copy-51">{{ $nextPost->title }}</span>
                                            @if ($nextPost->published_at)<time class="d-block site-posts-show__copy-52" datetime="{{ $nextPost->published_at->toDateString() }}">{{ $nextPost->published_at->translatedFormat('d/m/Y') }}</time>@endif
                                        </span>
                                    </a>
                                @endif
                            </div>
                        </section>
                    @endif

                    <section class="mx-auto site-posts-show__section-54" id="binh-luan">
                        <h2 class="display-title text-uppercase site-posts-show__heading-55">Bình luận ({{ $post->approvedComments->count() }})</h2>

                        <div class="site-posts-show__div-56">
                            <div class="d-grid site-posts-show__div-57">
                                @forelse ($post->approvedComments as $comment)
                                    <article class="d-flex site-posts-show__article-58">
                                        <span class="d-grid flex-shrink-0 fw-bold site-posts-show__copy-59" aria-hidden="true">{{ Str::upper(Str::substr($comment->author_name, 0, 1)) }}</span>
                                        <div class="site-posts-show__div-11">
                                            <div class="d-flex flex-wrap align-items-baseline site-posts-show__div-60">
                                                <h3 class="fw-semibold site-posts-show__heading-61">{{ $comment->author_name }}</h3>
                                                <time class="fw-medium site-posts-show__copy-62" datetime="{{ $comment->approved_at?->toDateString() }}">{{ $comment->approved_at?->translatedFormat('d/m/Y H:i') }}</time>
                                            </div>
                                            <p class="site-posts-show__copy-63">{{ $comment->body }}</p>
                                        </div>
                                    </article>
                                @empty
                                    <p class="site-posts-show__copy-64">Chưa có bình luận nào. Hãy là người đầu tiên chia sẻ ý kiến.</p>
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
