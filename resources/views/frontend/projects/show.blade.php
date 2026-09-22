@extends('layouts.master')

@push('styles')
    @vite('resources/scss/pages/project.scss')
@endpush

@section('content')
    <section class="resource-detail-hero">
        <div class="container">
            <nav aria-label="Breadcrumb">
                <ol class="d-flex flex-wrap align-items-center list-unstyled gap-2 small">
                    <li><a class="link-light" href="{{ route('home') }}">Trang chủ</a></li><li aria-hidden="true">/</li>
                    <li><a class="link-light" href="{{ route('projects.index') }}">Dự án</a></li>
                    @if ($project->category)<li aria-hidden="true">/</li><li><a class="link-light" href="{{ route('projects.category', ['slug' => $project->category->slug]) }}">{{ $project->category->name }}</a></li>@endif
                </ol>
            </nav>
            <div class="resource-project-hero-grid">
                <div class="resource-project-hero-copy">
                    @if ($project->category)
                    @endif
                    <h1>{{ $project->title }}</h1>
                    @if ($project->excerpt)
                        <p class="resource-project-hero__excerpt">{{ $project->excerpt }}</p>
                    @endif
                    <div class="resource-project-hero__meta">
                        @if ($project->client_name)<span>{{ $project->client_name }}</span>@endif
                        @if ($project->completed_at)<span>{{ $project->completed_at->translatedFormat('Y') }}</span>@endif
                        @if ($project->industry)<span>{{ $project->industry }}</span>@endif
                    </div>
                    <div class="d-flex flex-wrap gap-3 mt-4">
                        @if ($projectVideoUrl)<a class="btn btn-outline-light" href="{{ $projectVideoUrl }}" target="_blank" rel="noopener">Xem video dự án <span aria-hidden="true">↗</span></a>@endif
                        @if (filled(strip_tags((string) $project->body_html)))<a class="resource-project-hero-link" href="#noi-dung-du-an">Nội dung dự án <span aria-hidden="true">↓</span></a>@endif
                    </div>
                </div>
                <div class="resource-project-media">
                    @if ($project->image_url ?: $defaultBannerUrl)<img src="{{ $project->image_url ?: $defaultBannerUrl }}" alt="{{ $project->title }}" loading="eager">@else<span class="image-placeholder">DV</span>@endif
                    @if ($projectVideoUrl)<a class="resource-project-media__play" href="{{ $projectVideoUrl }}" target="_blank" rel="noopener" aria-label="Xem video {{ $project->title }}">▶</a>@endif
                </div>
            </div>
        </div>
    </section>

    <nav class="project-detail-nav" aria-label="Điều hướng nội dung dự án">
        <div class="container project-detail-nav__inner">
            <a class="is-active" href="#noi-dung-du-an">Tổng quan</a>
            @if ($galleryImages !== [])<a href="#hinh-anh-du-an">Hình ảnh thực tế</a>@endif
            <a href="#yeu-cau-du-an">Yêu cầu & giải pháp</a>
            @if ($faqItems->isNotEmpty())<a href="#cau-hoi-thuong-gap">Câu hỏi thường gặp</a>@endif
            @if ($relatedProjects->isNotEmpty())<a href="#du-an-lien-quan">Dự án liên quan</a>@endif
        </div>
    </nav>

    <section id="noi-dung-du-an" class="project-detail-content">
        <div class="container">
            <div class="project-detail-overview">
                <article >
                    <h2>Thông tin chung</h2>
                    @if ($project->excerpt)<p class="project-overview__lead">{{ $project->excerpt }}</p>@endif
                    @if (filled(strip_tags((string) $project->body_html)))
                        <div class="article-prose">{!! $project->body_html !!}</div>
                    @else
                        <p class="project-overview__lead">Thông tin chi tiết của dự án sẽ được cập nhật sớm.</p>
                    @endif

                    @if ($project->category || $project->client_name || $project->industry || $project->completed_at)
                        <dl class="project-overview-meta" aria-label="Thông tin dự án">
                            @if ($project->category)<div><dt>Danh mục</dt><dd><a href="{{ route('projects.category', ['slug' => $project->category->slug]) }}">{{ $project->category->name }}</a></dd></div>@endif
                            @if ($project->client_name)<div><dt>Khách hàng</dt><dd>{{ $project->client_name }}</dd></div>@endif
                            @if ($project->industry)<div><dt>Lĩnh vực</dt><dd>{{ $project->industry }}</dd></div>@endif
                            @if ($project->completed_at)<div><dt>Hoàn thành</dt><dd>{{ $project->completed_at->translatedFormat('m/Y') }}</dd></div>@endif
                        </dl>
                    @endif

                    <a class="project-overview__cta" href="{{ route('contact') }}">
                        <span><strong>Cần tư vấn dự án?</strong><small>{{ $website->site_name }} cùng anh/chị làm rõ mục tiêu và phạm vi triển khai.</small></span>
                        <span aria-hidden="true">→</span>
                    </a>
                </article>

                @if ($galleryImages !== [])
                    <div class="project-overview__gallery" id="hinh-anh-du-an">
                        <div >
                            <h2>Một số hình ảnh trong quá trình triển khai</h2>
                        </div>
                        <div class="project-overview__gallery-grid">
                            @foreach ($galleryImages as $imageUrl)
                                <a class="project-overview__gallery-item  {{ $loop->first ? 'is-featured' : '' }}" href="{{ $imageUrl }}" target="_blank" rel="noopener" aria-label="Mở ảnh {{ $loop->iteration }} của {{ $project->title }}">
                                    <img src="{{ $imageUrl }}" alt="{{ $project->title }} — ảnh {{ $loop->iteration }}" loading="eager">
                                    <span aria-hidden="true">↗</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section id="yeu-cau-du-an" class="project-challenge-band">
        <div class="container">
            <div >
                <h2>Thách thức của dự án</h2>
                <p>{{ $project->excerpt ?: 'Mỗi công trình có đặc thù riêng về quy mô, tiến độ, tiêu chuẩn và khả năng vận hành liên tục.' }}</p>
            </div>
            <div class="project-challenge-grid">
                @foreach ([['icon' => '01', 'title' => 'Đặc thù công trình', 'text' => 'Bám sát hiện trạng và công năng sử dụng.'], ['icon' => '02', 'title' => 'Yêu cầu kỹ thuật', 'text' => 'Đồng bộ hồ sơ, thiết bị và quy chuẩn.'], ['icon' => '03', 'title' => 'Tiến độ bàn giao', 'text' => 'Phối hợp rõ đầu việc và mốc nghiệm thu.'], ['icon' => '04', 'title' => 'Vận hành ổn định', 'text' => 'Dễ kiểm tra, hướng dẫn và bảo trì sau bàn giao.']] as $item)
                    <article class="project-challenge-card"><span>{{ $item['icon'] }}</span><h3>{{ $item['title'] }}</h3><p>{{ $item['text'] }}</p></article>
                @endforeach
            </div>
        </div>
    </section>

    <section id="giai-phap-du-an" class="project-solution-section section-space">
        <div class="container project-solution-section__grid">
            <div>
                <h2 class="display-title">Giải pháp PCCC đồng bộ từ {{ $website->site_name }}</h2>
                <p class="project-solution-section__lead">Từ khảo sát, thiết kế đến thi công và nghiệm thu, các hạng mục được phối hợp theo một kế hoạch rõ ràng để công trình vận hành an toàn.</p>
                <ul class="project-solution-list">
                    @foreach (['Khảo sát và cập nhật hồ sơ hiện trạng', 'Thiết kế, cung cấp và lắp đặt hệ thống phù hợp', 'Kiểm tra, nghiệm thu và hướng dẫn vận hành', 'Bàn giao hồ sơ và đầu mối hỗ trợ sau triển khai'] as $solution)
                        <li><span aria-hidden="true">✓</span>{{ $solution }}</li>
                    @endforeach
                </ul>
                <a class="btn btn-primary" href="{{ route('contact') }}">Liên hệ tư vấn giải pháp <span aria-hidden="true">→</span></a>
            </div>
            <figure class="project-solution-section__media">
                @if ($project->image_url ?: $defaultBannerUrl)<img src="{{ $project->image_url ?: $defaultBannerUrl }}" alt="Giải pháp PCCC tại {{ $project->title }}" loading="lazy">@else<span class="image-placeholder">{{ $website->site_name }}</span>@endif
            </figure>
        </div>
    </section>

    <section class="section-space">
        <div class="container">
            <header class="resource-list-heading">
                <div><h2 class="display-title text-uppercase h2">Các hạng mục chính</h2></div>
            </header>
            <div class="project-scope-grid">
                @foreach (['Báo cháy tự động', 'Chữa cháy Sprinkler', 'Họng nước chữa cháy', 'Bơm chữa cháy', 'Tăng áp - hút khói', 'Tủ điện & điều khiển'] as $scope)
                    <article class="project-scope-card"><span class="project-scope-card__icon" aria-hidden="true">✦</span><h3>{{ $scope }}</h3><span  aria-hidden="true">→</span></article>
                @endforeach
            </div>
        </div>
    </section>

    @if ($faqItems->isNotEmpty())
        <section class="faq section-space" id="cau-hoi-thuong-gap">
            <div class="container">
                <header class="faq__header">
                    <h2 class="display-title text-uppercase h2">Câu hỏi thường gặp</h2>
                    <p class="text-body">Thông tin cần biết trước khi triển khai dự án PCCC.</p>
                </header>
                <div class="faq__list">
                    @foreach ($faqItems as $item)
                        <details class="faq__item" @if ($loop->first) open @endif>
                            <summary class="faq__question"><span>{{ $item['question'] }}</span><span class="faq__indicator" aria-hidden="true">+</span></summary>
                            <div class="faq__answer"><p>{{ $item['answer'] }}</p></div>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="resource-related-section" id="phan-hoi">
        <div class="container">
            <header class="mx-auto text-center mb-4">
                <h2 class="display-title text-uppercase h2">Đánh giá từ khách hàng</h2>
                @if ($ratingSummary['count'])
                    <p class="mt-3"><strong class="text-primary">{{ number_format($ratingSummary['average'], 1) }}/5</strong> từ {{ $ratingSummary['count'] }} đánh giá đã được duyệt.</p>
                @else
                    <p class="mt-3">Những đánh giá đầu tiên sẽ được hiển thị sau khi đội ngũ kiểm duyệt.</p>
                @endif
            </header>

            <div class="review-layout">
                <div class="d-grid gap-4">
                    @forelse ($project->approvedComments as $comment)
                        <article class="d-flex gap-3">
                            <span class="d-grid flex-shrink-0 fw-bold review-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($comment->author_name, 0, 1)) }}</span>
                            <div class="flex-grow-1">
                                <div class="d-flex flex-wrap align-items-baseline gap-2"><h3 class="fw-semibold h6 mb-0">{{ $comment->author_name }}</h3><time class="fw-medium small text-body-secondary" datetime="{{ $comment->approved_at?->toDateString() }}">{{ $comment->approved_at?->translatedFormat('d/m/Y') }}</time></div>
                                @if ($comment->rating)<p class="review-rating mt-2 mb-0" aria-label="{{ $comment->rating }} trên 5 sao">@for ($star = 1; $star <= 5; $star++)<span class="{{ $star <= $comment->rating ? '' : 'text-body-tertiary' }}" aria-hidden="true">★</span>@endfor</p>@endif
                                <p class="mt-2 mb-0">{{ $comment->body }}</p>
                            </div>
                        </article>
                    @empty
                        <p class="empty-state w-100">Chưa có đánh giá nào. Anh/chị có thể là người đầu tiên chia sẻ trải nghiệm.</p>
                    @endforelse
                </div>

                <x-comment-form :project="$project" :rating-enabled="true" :compact="true" />
            </div>
        </div>
    </section>

    @if ($relatedProjects->isNotEmpty())
        <section id="du-an-lien-quan" class="resource-related-section">
            <div class="container flex-column d-flex gap-3 mb-4"><div><h2 class="display-title h2">Khám phá thêm các dự án đã thực hiện</h2></div><a class="section-link" href="{{ route('projects.index') }}">Xem tất cả dự án <span aria-hidden="true">→</span></a></div>
            <div class="container"><div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">@foreach ($relatedProjects as $project) <div class="col">@include('frontend.partials.project-card')</div> @endforeach</div></div>
        </section>
    @endif
@endsection
