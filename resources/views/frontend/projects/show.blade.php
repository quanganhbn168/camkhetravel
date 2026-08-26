@extends('layouts.master')

@use(App\Support\Localization\LocalizedUrl)

@section('content')
    <section class="resource-detail-hero resource-detail-hero--project">
        <div class="site-shell resource-detail-hero__project-wrap">
            <nav aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-white/65">
                    <li><a class="hover:text-white" href="{{ LocalizedUrl::route('home') }}">Trang chủ</a></li><li aria-hidden="true">/</li>
                    <li><a class="hover:text-white" href="{{ LocalizedUrl::route('projects.index') }}">{{ __('site.projects') }}</a></li>
                    @if ($project->category)<li aria-hidden="true">/</li><li><a class="hover:text-white" href="{{ LocalizedUrl::projectCategory($project->category) }}">{{ $project->category->name }}</a></li>@endif
                </ol>
            </nav>
            <div class="resource-project-hero-grid">
                <div class="resource-project-hero-copy">
                    <h1>{{ $project->title }}</h1>
                    <div class="mt-7 flex flex-wrap gap-3">
                        @if ($projectVideoUrl)<a class="button-secondary" href="{{ $projectVideoUrl }}" target="_blank" rel="noopener">Xem video dự án <span aria-hidden="true">↗</span></a>@endif
                        @if (filled(strip_tags((string) $project->body_html)))<a class="resource-project-hero-link" href="#noi-dung-du-an">Nội dung dự án <span aria-hidden="true">↓</span></a>@endif
                    </div>
                </div>
                <div class="resource-project-media">
                    @if ($project->image_url)<img src="{{ $project->image_url }}" alt="{{ $project->title }}">@else<span class="image-placeholder">THT</span>@endif
                    @if ($projectVideoUrl)<a class="resource-project-media__play" href="{{ $projectVideoUrl }}" target="_blank" rel="noopener" aria-label="Xem video {{ $project->title }}">▶</a>@endif
                </div>
            </div>
        </div>
    </section>

    <section id="noi-dung-du-an" class="project-detail-content">
        <div class="site-shell project-detail-content__grid">
            <article class="article-prose project-detail-content__article">
                @if (filled(strip_tags((string) $project->body_html)))
                    <h2 class="!mt-0">NỘI DUNG DỰ ÁN</h2>
                    {!! $project->body_html !!}
                @else
                    <p>Thông tin chi tiết của dự án sẽ được cập nhật sớm.</p>
                @endif
            </article>

            <aside class="project-detail-sidebar lg:sticky lg:top-28">
                @if ($project->category || $project->client_name || $project->industry || $project->completed_at)
                    <section class="project-detail-sidebar__block" aria-labelledby="thong-tin-du-an">
                        <h2 id="thong-tin-du-an">THÔNG TIN DỰ ÁN</h2>
                        <dl class="project-detail-meta">
                            @if ($project->category)<div><dt>Danh mục</dt><dd><a href="{{ LocalizedUrl::projectCategory($project->category) }}">{{ $project->category->name }}</a></dd></div>@endif
                            @if ($project->client_name)<div><dt>Khách hàng</dt><dd>{{ $project->client_name }}</dd></div>@endif
                            @if ($project->industry)<div><dt>Lĩnh vực</dt><dd>{{ $project->industry }}</dd></div>@endif
                            @if ($project->completed_at)<div><dt>Hoàn thành</dt><dd>{{ $project->completed_at->translatedFormat('m/Y') }}</dd></div>@endif
                        </dl>
                    </section>
                @endif

                <section class="project-detail-sidebar__block project-detail-sidebar__cta" aria-labelledby="tu-van-du-an">
                    <h2 id="tu-van-du-an">CẦN TƯ VẤN DỰ ÁN?</h2>
                    <p>THT Media sẵn sàng cùng anh/chị làm rõ mục tiêu, phạm vi và hướng triển khai phù hợp.</p>
                    <a class="button-dark mt-6 w-full" href="{{ LocalizedUrl::route('contact') }}">Trao đổi dự án <span aria-hidden="true">→</span></a>
                </section>

                <nav class="project-detail-sidebar__block" aria-label="Khám phá thêm">
                    <h2>KHÁM PHÁ THÊM</h2>
                    <div class="project-detail-sidebar__links">
                        @if ($relatedServices->isNotEmpty())<a href="#dich-vu-lien-quan">Dịch vụ liên quan <span aria-hidden="true">→</span></a>@endif
                        @if ($relatedPosts->isNotEmpty())<a href="#bai-viet-lien-quan">Bài viết liên quan <span aria-hidden="true">→</span></a>@endif
                        <a href="#phan-hoi">Đánh giá dự án <span aria-hidden="true">→</span></a>
                    </div>
                </nav>
            </aside>
        </div>
    </section>

    @if ($galleryImages !== [])
        @include('frontend.partials.resource-detail-gallery', [
            'galleryTitle' => 'Hình ảnh dự án',
            'galleryAlt' => $project->title,
        ])
    @endif

    @if ($faqItems->isNotEmpty())
        <section class="home-faq section-space" id="cau-hoi-thuong-gap">
            <div class="site-shell">
                <header class="home-faq__header">
                    <h2 class="display-title text-3xl leading-tight uppercase md:text-4xl">{{ $project->faq_title ?: 'Câu hỏi thường gặp' }}</h2>
                    @if ($project->faq_description)<p class="mt-4 text-base leading-8 text-slate-600 md:text-lg">{{ $project->faq_description }}</p>@endif
                </header>
                <div class="home-faq__list">
                    @foreach ($faqItems as $item)
                        <details class="home-faq__item" @if ($loop->first) open @endif>
                            <summary class="home-faq__question"><span>{{ $item['question'] }}</span><span class="home-faq__indicator" aria-hidden="true">+</span></summary>
                            <div class="home-faq__answer"><p>{{ $item['answer'] }}</p></div>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="resource-related-section" id="phan-hoi">
        <div class="site-shell">
            <header class="mx-auto max-w-2xl text-center">
                <h2 class="display-title text-3xl leading-tight uppercase md:text-4xl">Đánh giá từ khách hàng</h2>
                @if ($ratingSummary['count'])
                    <p class="mt-4 text-sm leading-7 text-slate-600"><strong class="text-lg text-ink">{{ number_format($ratingSummary['average'], 1) }}/5</strong> từ {{ $ratingSummary['count'] }} đánh giá đã được duyệt.</p>
                @else
                    <p class="mt-4 text-sm leading-7 text-slate-600">Những đánh giá đầu tiên sẽ được hiển thị sau khi đội ngũ kiểm duyệt.</p>
                @endif
            </header>

            <div class="mt-9 grid gap-8 border-t border-slate-200 pt-8 lg:grid-cols-[minmax(0,.85fr)_minmax(0,1.15fr)]">
                <div class="grid content-start gap-6">
                    @forelse ($project->approvedComments as $comment)
                        <article class="flex gap-4">
                            <span class="grid size-11 shrink-0 place-items-center rounded-full bg-mist text-sm font-bold text-primary" aria-hidden="true">{{ mb_strtoupper(mb_substr($comment->author_name, 0, 1)) }}</span>
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1"><h3 class="text-sm font-semibold text-ink">{{ $comment->author_name }}</h3><time class="text-xs font-medium text-slate-400" datetime="{{ $comment->approved_at?->toDateString() }}">{{ $comment->approved_at?->translatedFormat('d/m/Y') }}</time></div>
                                @if ($comment->rating)<p class="mt-1 text-sm tracking-[0.08em] text-primary" aria-label="{{ $comment->rating }} trên 5 sao">@for ($star = 1; $star <= 5; $star++)<span class="{{ $star <= $comment->rating ? '' : 'text-slate-200' }}" aria-hidden="true">★</span>@endfor</p>@endif
                                <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">{{ $comment->body }}</p>
                            </div>
                        </article>
                    @empty
                        <p class="rounded-2xl border border-dashed border-slate-300 bg-white p-6 text-sm leading-7 text-slate-500">Chưa có đánh giá nào. Anh/chị có thể là người đầu tiên chia sẻ trải nghiệm.</p>
                    @endforelse
                </div>

                <x-comment-form :project="$project" :rating-enabled="true" :compact="true" />
            </div>
        </div>
    </section>

    @if ($relatedServices->isNotEmpty())
        <section class="resource-related-section" id="dich-vu-lien-quan">
            <div class="site-shell flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                <div><h2 class="display-title text-3xl leading-tight md:text-4xl">Dịch vụ đồng hành cùng dự án</h2></div>
                <a class="section-link" href="{{ LocalizedUrl::route('services.index') }}">Xem tất cả dịch vụ <span aria-hidden="true">→</span></a>
            </div>
            <div class="site-shell mt-9 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">@foreach ($relatedServices as $service) @include('frontend.partials.service-card') @endforeach</div>
        </section>
    @endif

    @if ($relatedPosts->isNotEmpty())
        <section class="resource-related-section" id="bai-viet-lien-quan">
            <div class="site-shell flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                <div><h2 class="display-title text-3xl leading-tight md:text-4xl">Bài viết liên quan</h2></div>
                <a class="section-link" href="{{ LocalizedUrl::route('posts.index') }}">Xem tất cả bài viết <span aria-hidden="true">→</span></a>
            </div>
            <div class="site-shell mt-9 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">@foreach ($relatedPosts as $post) @include('frontend.partials.post-card') @endforeach</div>
        </section>
    @endif

    @if ($relatedProjects->isNotEmpty())
        <section class="resource-related-section">
            <div class="site-shell flex flex-col gap-5 md:flex-row md:items-end md:justify-between"><div><h2 class="display-title text-3xl leading-tight md:text-4xl">Khám phá thêm các dự án đã thực hiện</h2></div><a class="section-link" href="{{ LocalizedUrl::route('projects.index') }}">Xem tất cả dự án <span aria-hidden="true">→</span></a></div>
            <div class="site-shell mt-9 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">@foreach ($relatedProjects as $project) @include('frontend.partials.project-card') @endforeach</div>
        </section>
    @endif
@endsection
