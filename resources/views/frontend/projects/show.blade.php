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
            <div class="mt-8 grid gap-8 lg:grid-cols-[minmax(0,1fr)_minmax(22rem,0.92fr)] lg:items-end">
                <div>
                    @if ($project->category)<p class="eyebrow text-primary-soft">{{ $project->category->name }}</p>@endif
                    <h1 class="mt-3 max-w-3xl font-display text-4xl leading-[1.08] tracking-[-0.045em] text-white md:text-6xl">{{ $project->title }}</h1>
                    @if ($project->excerpt)<p class="mt-5 max-w-2xl text-sm leading-7 text-white/75 md:text-base md:leading-8">{{ $project->excerpt }}</p>@endif
                    @if ($projectVideoUrl)<a class="button-secondary mt-7" href="{{ $projectVideoUrl }}" target="_blank" rel="noopener">Xem video dự án <span aria-hidden="true">↗</span></a>@endif
                </div>
                <div class="resource-project-media">
                    @if ($project->image_url)<img src="{{ $project->image_url }}" alt="{{ $project->title }}">@else<span class="image-placeholder">THT</span>@endif
                    @if ($projectVideoUrl)<a class="resource-project-media__play" href="{{ $projectVideoUrl }}" target="_blank" rel="noopener" aria-label="Xem video {{ $project->title }}">▶</a>@endif
                </div>
            </div>
        </div>
    </section>

    @if ($project->category || $project->client_name || $project->industry || $project->completed_at)
        <section class="resource-project-facts">
            <div class="site-shell grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @if ($project->category)<div><span>Danh mục</span><strong>{{ $project->category->name }}</strong></div>@endif
                @if ($project->client_name)<div><span>Khách hàng</span><strong>{{ $project->client_name }}</strong></div>@endif
                @if ($project->industry)<div><span>Lĩnh vực</span><strong>{{ $project->industry }}</strong></div>@endif
                @if ($project->completed_at)<div><span>Hoàn thành</span><strong>{{ $project->completed_at->translatedFormat('m/Y') }}</strong></div>@endif
            </div>
        </section>
    @endif

    <section class="resource-detail-content">
        <div class="site-shell grid gap-12 lg:grid-cols-[minmax(0,1fr)_19rem] lg:items-start">
            <article class="article-prose max-w-3xl">
                <p class="eyebrow">Tổng quan dự án</p>
                <h2 class="!mt-3">Câu chuyện và kết quả triển khai.</h2>
                @if (filled(strip_tags((string) $project->body_html)))
                    {!! $project->body_html !!}
                @elseif ($project->excerpt)
                    <p>{{ $project->excerpt }}</p>
                @endif
            </article>
            <aside class="resource-detail-aside lg:sticky lg:top-28">
                <p class="eyebrow">Dự án tương tự</p>
                <h2 class="mt-3 text-2xl leading-tight font-bold text-ink">Anh/chị đang có kế hoạch triển khai?</h2>
                <p class="mt-4 text-sm leading-7 text-slate-600">THT Media sẵn sàng trao đổi về mục tiêu và hình thức thực hiện phù hợp.</p>
                <a class="button-dark mt-6 w-full" href="{{ LocalizedUrl::route('contact') }}">Trao đổi dự án <span aria-hidden="true">→</span></a>
                @if ($project->category)<a class="resource-detail-aside__link" href="{{ LocalizedUrl::projectCategory($project->category) }}">Xem dự án cùng danh mục <span aria-hidden="true">→</span></a>@endif
            </aside>
        </div>
    </section>

    @if ($galleryImages !== [])
        @include('frontend.partials.resource-detail-gallery', [
            'galleryTitle' => 'Hình ảnh dự án',
            'galleryAlt' => $project->title,
        ])
    @endif

    @if ($relatedProjects->isNotEmpty())
        <section class="resource-related-section">
            <div class="site-shell flex flex-col gap-5 md:flex-row md:items-end md:justify-between"><div><p class="eyebrow">Dự án liên quan</p><h2 class="display-title mt-3 text-3xl leading-tight md:text-4xl">Khám phá thêm các dự án đã thực hiện</h2></div><a class="section-link" href="{{ LocalizedUrl::route('projects.index') }}">Xem tất cả dự án <span aria-hidden="true">→</span></a></div>
            <div class="site-shell mt-9 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">@foreach ($relatedProjects as $project) @include('frontend.partials.project-card') @endforeach</div>
        </section>
    @endif
@endsection
