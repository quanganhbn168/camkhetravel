@extends('layouts.master')

@push('styles')
    @vite('resources/scss/pages/project.scss')
@endpush

@section('content')
<div class="project-case">
    <section class="project-case__hero">
        @if ($content['hero']['image_url'])<img class="project-case__hero-image" src="{{ $content['hero']['image_url'] }}" alt="" fetchpriority="high">@endif
        <div class="container position-relative">
            <nav class="project-case__breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Trang chủ</a><span>›</span><a href="{{ route('projects.index') }}">Dự án</a><span>›</span><span>{{ $project->title }}</span>
            </nav>
            <div class="project-case__hero-content">
                <div>
                    @if ($project->category)<span class="project-case__badge">{{ $project->category->name }}</span>@endif
                    <h1 class="text-white">{{ $project->title }}</h1>
                    @if ($project->excerpt)<p>{{ $project->excerpt }}</p>@endif
                    <div class="project-case__meta">
                        @if (filled(data_get($content, 'hero.location')))<span><i class="fa-solid fa-location-dot" aria-hidden="true"></i> {{ $content['hero']['location'] }}</span>@endif
                        @if ($project->completed_at)<span><i class="fa-regular fa-calendar" aria-hidden="true"></i> {{ $project->completed_at->format('Y') }}</span>@endif
                        @if (filled(data_get($content, 'hero.area')))<span><i class="fa-solid fa-ruler-combined" aria-hidden="true"></i> {{ $content['hero']['area'] }}</span>@endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <nav class="project-case__nav" aria-label="Điều hướng nội dung dự án">
        <div class="container">
            <a href="#tong-quan"><i class="fa-regular fa-clipboard" aria-hidden="true"></i>Tổng quan</a>
            @if (count($content['solution']['items']) || filled($content['solution']['description'] ?? null) || $content['solution']['image_url'])<a href="#giai-phap-du-an"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i>Giải pháp triển khai</a>@endif
            @if (count($content['scope']['items']))<a href="#hang-muc"><i class="fa-solid fa-gears" aria-hidden="true"></i>Hạng mục thi công</a>@endif
            @if (count($content['construction']['images']))<a href="#hinh-anh-du-an"><i class="fa-regular fa-image" aria-hidden="true"></i>Hình ảnh thực tế</a>@endif
            @if (count($content['results']['items']))<a href="#ket-qua"><i class="fa-solid fa-clipboard-check" aria-hidden="true"></i>Kết quả</a>@endif
            @if ($relatedProjects->isNotEmpty())<a href="#du-an-lien-quan"><i class="fa-solid fa-diagram-project" aria-hidden="true"></i>Dự án liên quan</a>@endif
        </div>
    </nav>

    <section id="tong-quan" class="project-case__section">
        <div class="container">
            @if (filled($content['notice'] ?? null))<p class="project-case__preview">{{ $content['notice'] }}</p>@endif
            <div class="row g-4 g-xl-5 align-items-start">
                <div class="{{ count($content['gallery']) ? 'col-lg-5' : 'col-12' }}">
                    @include('frontend.projects.partials.section-heading', ['section' => $content['overview']])
                    <div class="article-prose">{!! $content['overview']['body_html'] !!}</div>
                    @if ($projectVideoUrl)<a href="{{ $projectVideoUrl }}" class="section-link" target="_blank" rel="noopener">Xem video dự án →</a>@endif
                    @if (count($content['overview']['facts']))<dl class="project-case__facts">
                        @foreach ($content['overview']['facts'] as $fact)
                            <div><dt>{{ $fact['label'] }}</dt><dd>{{ $fact['value'] }}</dd></div>
                        @endforeach
                    </dl>@endif
                </div>
                @if (count($content['gallery']))
                <div class="col-lg-7">
                    <div class="project-case__gallery">
                        <div class="swiper" data-project-gallery aria-label="Ảnh tổng quan dự án">
                            <div class="swiper-wrapper">
                                @foreach ($content['gallery'] as $image)
                                    <div class="swiper-slide"><img src="{{ $image['url'] }}" alt="{{ $image['alt'] }}" loading="lazy"></div>
                                @endforeach
                            </div>
                            <button type="button" class="project-case__arrow project-case__arrow--prev" data-project-prev aria-label="Ảnh trước"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></button>
                            <button type="button" class="project-case__arrow project-case__arrow--next" data-project-next aria-label="Ảnh tiếp theo"><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></button>
                        </div>
                        <div class="swiper project-case__thumbs" data-project-thumbs aria-label="Chọn ảnh dự án">
                            <div class="swiper-wrapper">
                                @foreach ($content['gallery'] as $image)
                                    <div class="swiper-slide"><button type="button" aria-label="Xem {{ $image['alt'] }}"><img src="{{ $image['url'] }}" alt="" loading="lazy"></button></div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>

    @if (count($content['challenges']['items']))
    <section class="project-case__section project-case__band">
        <div class="container row-gap-4 project-case__band-grid">
            <div>@include('frontend.projects.partials.section-heading', ['section' => $content['challenges']])<p class="mb-0">{{ $content['challenges']['description'] ?? '' }}</p></div>
            <div class="project-case__tiles project-case__tiles--four">
                @foreach ($content['challenges']['items'] as $item)
                    <article class="project-case__tile">@include('frontend.projects.partials.item-icon')<h3>{{ $item['title'] }}</h3>@if (filled($item['description'] ?? null))<p>{{ $item['description'] }}</p>@endif</article>
                @endforeach
            </div>
        </div>
    </section>

    @endif
    @if (count($content['solution']['items']) || filled($content['solution']['description'] ?? null) || $content['solution']['image_url'])
    <section id="giai-phap-du-an" class="project-case__section">
        <div class="container row-gap-4 project-case__solution">
            <div>@include('frontend.projects.partials.section-heading', ['section' => $content['solution']])<p>{{ $content['solution']['description'] ?? '' }}</p>@if (filled($content['solution']['cta_label'] ?? null))<a class="btn btn-primary" href="{{ route('contact') }}">{{ $content['solution']['cta_label'] }} <i class="fa-solid fa-arrow-right ms-2" aria-hidden="true"></i></a>@endif</div>
            <ul class="project-case__solutions">
                @foreach ($content['solution']['items'] as $item)<li><i class="fa-solid fa-circle-check" aria-hidden="true"></i>{{ $item['text'] }}</li>@endforeach
            </ul>
            @if ($content['solution']['image_url'])
                <figure class="project-case__solution-image mb-0"><img src="{{ $content['solution']['image_url'] }}" alt="{{ $content['solution']['caption'] ?? '' }}" loading="lazy">@if (filled($content['solution']['caption'] ?? null))<figcaption>{{ $content['solution']['caption'] }}</figcaption>@endif</figure>
            @endif
        </div>
    </section>

    @endif
    @if (count($content['scope']['items']))
    <section id="hang-muc" class="project-case__section project-case__section--line">
        <div class="container">
            @include('frontend.projects.partials.section-heading', ['section' => $content['scope']])
            <div class="project-case__tiles project-case__tiles--six">
                @foreach ($content['scope']['items'] as $item)
                    <article class="project-case__tile">@include('frontend.projects.partials.item-icon')<h3>{{ $item['title'] }}</h3>@if (filled($item['description'] ?? null))<p>{{ $item['description'] }}</p>@endif</article>
                @endforeach
            </div>
        </div>
    </section>

    @endif
    @if (count($content['construction']['images']))
    <section id="hinh-anh-du-an" class="project-case__section project-case__section--line">
        <div class="container">
            @include('frontend.projects.partials.section-heading', ['section' => $content['construction']])
            <div class="project-case__slider-shell">
                <div class="swiper" data-project-site-swiper>
                    <div class="swiper-wrapper">
                        @foreach ($content['construction']['images'] as $image)
                            <div class="swiper-slide"><a href="{{ $image['url'] }}" target="_blank" rel="noopener" aria-label="Mở ảnh {{ $image['alt'] }}"><img src="{{ $image['url'] }}" alt="{{ $image['alt'] }}" loading="lazy"></a></div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
                <button type="button" class="project-case__arrow project-case__arrow--prev" data-strip-prev aria-label="Nhóm ảnh trước"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></button>
                <button type="button" class="project-case__arrow project-case__arrow--next" data-strip-next aria-label="Nhóm ảnh tiếp theo"><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></button>
            </div>
        </div>
    </section>

    @endif
    @if (count($content['results']['items']))
    <section id="ket-qua" class="project-case__section project-case__band">
        <div class="container project-case__band-grid">
            <div>@include('frontend.projects.partials.section-heading', ['section' => $content['results']])<p class="mb-0">{{ $content['results']['description'] ?? '' }}</p></div>
            <div class="project-case__tiles project-case__tiles--four">
                @foreach ($content['results']['items'] as $item)
                    <article class="project-case__tile">@include('frontend.projects.partials.item-icon')<h3 class="project-case__result-title">{{ $item['title'] }}</h3><p>{{ $item['description'] ?? '' }}</p></article>
                @endforeach
            </div>
        </div>
    </section>

    @endif
    @if (filled($content['testimonial']['quote'] ?? null))
    <section class="project-case__section project-case__quote">
        @if ($content['testimonial']['background_url'])<img class="project-case__quote-background" src="{{ $content['testimonial']['background_url'] }}" alt="" loading="lazy">@endif
        <div class="container row align-items-center g-4 mx-auto">
            <div class="col-12">
                <div class="d-flex gap-4">
                    <i class="fa-solid fa-quote-left project-case__quote-icon" aria-hidden="true"></i>
                    <div><blockquote>{{ $content['testimonial']['quote'] }}</blockquote><div class="d-flex align-items-center gap-3">
                        @if ($content['testimonial']['avatar_url'])<img class="project-case__avatar" src="{{ $content['testimonial']['avatar_url'] }}" alt="{{ $content['testimonial']['name'] ?? '' }}" loading="lazy">@endif
                        <div><strong>{{ $content['testimonial']['name'] ?? '' }}</strong><small class="d-block text-body-secondary">{{ $content['testimonial']['role'] ?? '' }}</small></div>
                    </div></div>
                </div>
            </div>
        </div>
    </section>
    @endif

    @if ($relatedProjects->isNotEmpty())
    <section id="du-an-lien-quan" class="project-case__section">
        <div class="container">
            <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4"><div>@include('frontend.projects.partials.section-heading', ['section' => $content['related'] ?? []])</div><a class="section-link" href="{{ route('projects.index') }}">Xem tất cả dự án →</a></div>
            @if ($relatedProjects->isNotEmpty())
                <div class="project-case__slider-shell">
                    <div class="swiper" data-project-related-swiper><div class="swiper-wrapper">
                        @foreach ($relatedProjects as $relatedProject)<div class="swiper-slide">@include('frontend.partials.project-card', ['project' => $relatedProject])</div>@endforeach
                    </div></div>
                    <button type="button" class="project-case__arrow project-case__arrow--prev" data-strip-prev aria-label="Dự án trước">‹</button>
                    <button type="button" class="project-case__arrow project-case__arrow--next" data-strip-next aria-label="Dự án tiếp theo">›</button>
                </div>
            @endif
        </div>
    </section>

    @endif
    <section class="project-case__section project-case__section--line" id="phan-hoi">
        <div class="container">
            <details class="project-case__feedback"><summary>Gửi đánh giá về dự án</summary><div class="pt-4"><x-comment-form :project="$project" :rating-enabled="true" :compact="true" /></div></details>
            @foreach ($project->approvedComments as $comment)
                <article class="mt-4"><h3 class="h6">{{ $comment->author_name }}</h3><p>{{ $comment->body }}</p></article>
            @endforeach
            @if ($faqItems->isNotEmpty())
                <div class="mt-4"><h2>Câu hỏi thường gặp</h2>@foreach ($faqItems as $item)<details class="py-3 border-bottom"><summary>{{ $item['question'] }}</summary><p class="mt-3">{{ $item['answer'] }}</p></details>@endforeach</div>
            @endif
        </div>
    </section>
</div>
@endsection
