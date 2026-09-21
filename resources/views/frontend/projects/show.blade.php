@extends('layouts.master')

@push('styles')
    @vite('resources/css/frontend/pages/projects-show.css')
@endpush

@use(App\Support\Localization\LocalizedUrl)

@section('content')
    <section class="resource-detail-hero resource-detail-hero--project">
        <div class="site-container resource-detail-hero__project-wrap w-100 mx-auto site-projects-show__div-1">
            <nav aria-label="Breadcrumb">
                <ol class="d-flex flex-wrap align-items-center site-projects-show__element-2">
                    <li><a class="site-projects-show__action-3" href="{{ LocalizedUrl::route('home') }}">Trang chủ</a></li><li aria-hidden="true">/</li>
                    <li><a class="site-projects-show__action-3" href="{{ LocalizedUrl::route('projects.index') }}">{{ __('site.projects') }}</a></li>
                    @if ($project->category)<li aria-hidden="true">/</li><li><a class="site-projects-show__action-3" href="{{ LocalizedUrl::projectCategory($project->category) }}">{{ $project->category->name }}</a></li>@endif
                </ol>
            </nav>
            <div class="resource-project-hero-grid">
                <div class="resource-project-hero-copy">
                    @if ($project->category)
                        <p class="resource-project-hero__eyebrow">{{ $project->category->name }}</p>
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
                    <div class="d-flex flex-wrap site-projects-show__div-4">
                        @if ($projectVideoUrl)<a class="btn btn-outline-light button-secondary" href="{{ $projectVideoUrl }}" target="_blank" rel="noopener">Xem video dự án <span aria-hidden="true">↗</span></a>@endif
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
        <div class="site-container project-detail-nav__inner w-100 mx-auto site-projects-show__div-1">
            <a class="is-active" href="#noi-dung-du-an">Tổng quan</a>
            @if ($galleryImages !== [])<a href="#hinh-anh-du-an">Hình ảnh thực tế</a>@endif
            <a href="#yeu-cau-du-an">Yêu cầu & giải pháp</a>
            @if ($faqItems->isNotEmpty())<a href="#cau-hoi-thuong-gap">Câu hỏi thường gặp</a>@endif
            @if ($relatedProjects->isNotEmpty())<a href="#du-an-lien-quan">Dự án liên quan</a>@endif
        </div>
    </nav>

    <section id="noi-dung-du-an" class="project-detail-content">
        <div class="site-container w-100 mx-auto site-projects-show__div-1">
            <div class="project-detail-overview">
                <article class="project-overview__copy">
                    <p class="site-eyebrow">Tổng quan dự án</p>
                    <h2>Thông tin chung</h2>
                    @if ($project->excerpt)<p class="project-overview__lead">{{ $project->excerpt }}</p>@endif
                    @if (filled(strip_tags((string) $project->body_html)))
                        <div class="article-prose project-detail-content__article">{!! $project->body_html !!}</div>
                    @else
                        <p class="project-overview__lead">Thông tin chi tiết của dự án sẽ được cập nhật sớm.</p>
                    @endif

                    @if ($project->category || $project->client_name || $project->industry || $project->completed_at)
                        <dl class="project-overview-meta" aria-label="Thông tin dự án">
                            @if ($project->category)<div><dt>Danh mục</dt><dd><a href="{{ LocalizedUrl::projectCategory($project->category) }}">{{ $project->category->name }}</a></dd></div>@endif
                            @if ($project->client_name)<div><dt>Khách hàng</dt><dd>{{ $project->client_name }}</dd></div>@endif
                            @if ($project->industry)<div><dt>Lĩnh vực</dt><dd>{{ $project->industry }}</dd></div>@endif
                            @if ($project->completed_at)<div><dt>Hoàn thành</dt><dd>{{ $project->completed_at->translatedFormat('m/Y') }}</dd></div>@endif
                        </dl>
                    @endif

                    <a class="project-overview__cta" href="{{ LocalizedUrl::route('contact') }}">
                        <span><strong>Cần tư vấn dự án?</strong><small>{{ $website->site_name }} cùng anh/chị làm rõ mục tiêu và phạm vi triển khai.</small></span>
                        <span aria-hidden="true">→</span>
                    </a>
                </article>

                @if ($galleryImages !== [])
                    <div class="project-overview__gallery" id="hinh-anh-du-an">
                        <div class="project-overview__gallery-heading">
                            <p class="site-eyebrow">Hình ảnh thực tế</p>
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
        <div class="site-container project-challenge-band__inner w-100 mx-auto site-projects-show__div-1">
            <div class="project-challenge-band__copy">
                <p class="site-eyebrow">Yêu cầu bài toán</p>
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
        <div class="site-container project-solution-section__grid w-100 mx-auto site-projects-show__div-1">
            <div>
                <p class="site-eyebrow">Giải pháp triển khai</p>
                <h2 class="display-title">Giải pháp PCCC đồng bộ từ {{ $website->site_name }}</h2>
                <p class="project-solution-section__lead">Từ khảo sát, thiết kế đến thi công và nghiệm thu, các hạng mục được phối hợp theo một kế hoạch rõ ràng để công trình vận hành an toàn.</p>
                <ul class="project-solution-list">
                    @foreach (['Khảo sát và cập nhật hồ sơ hiện trạng', 'Thiết kế, cung cấp và lắp đặt hệ thống phù hợp', 'Kiểm tra, nghiệm thu và hướng dẫn vận hành', 'Bàn giao hồ sơ và đầu mối hỗ trợ sau triển khai'] as $solution)
                        <li><span aria-hidden="true">✓</span>{{ $solution }}</li>
                    @endforeach
                </ul>
                <a class="btn btn-primary button-primary" href="{{ LocalizedUrl::route('contact') }}">Liên hệ tư vấn giải pháp <span aria-hidden="true">→</span></a>
            </div>
            <figure class="project-solution-section__media">
                @if ($project->image_url ?: $defaultBannerUrl)<img src="{{ $project->image_url ?: $defaultBannerUrl }}" alt="Giải pháp PCCC tại {{ $project->title }}" loading="lazy">@else<span class="image-placeholder">{{ $website->site_name }}</span>@endif
            </figure>
        </div>
    </section>

    <section class="project-scope-section section-space">
        <div class="site-container w-100 mx-auto site-projects-show__div-1">
            <header class="resource-list-heading">
                <div><p class="resource-list-heading__eyebrow">Hạng mục thi công</p><h2 class="display-title text-uppercase site-projects-show__heading-5">Các hạng mục chính</h2></div>
            </header>
            <div class="project-scope-grid">
                @foreach (['Báo cháy tự động', 'Chữa cháy Sprinkler', 'Họng nước chữa cháy', 'Bơm chữa cháy', 'Tăng áp - hút khói', 'Tủ điện & điều khiển'] as $scope)
                    <article class="project-scope-card"><span class="project-scope-card__icon" aria-hidden="true">✦</span><h3>{{ $scope }}</h3><span class="project-scope-card__arrow" aria-hidden="true">→</span></article>
                @endforeach
            </div>
        </div>
    </section>

    @if ($faqItems->isNotEmpty())
        <section class="home-faq section-space" id="cau-hoi-thuong-gap">
            <div class="site-container w-100 mx-auto site-projects-show__div-1">
                <header class="home-faq__header">
                    <h2 class="display-title text-uppercase site-projects-show__heading-6">Câu hỏi thường gặp</h2>
                    <p class="site-projects-show__copy-7">Thông tin cần biết trước khi triển khai dự án PCCC.</p>
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
        <div class="site-container w-100 mx-auto site-projects-show__div-1">
            <header class="mx-auto text-center site-projects-show__element-8">
                <h2 class="display-title text-uppercase site-projects-show__heading-6">Đánh giá từ khách hàng</h2>
                @if ($ratingSummary['count'])
                    <p class="site-projects-show__copy-9"><strong class="site-projects-show__element-10">{{ number_format($ratingSummary['average'], 1) }}/5</strong> từ {{ $ratingSummary['count'] }} đánh giá đã được duyệt.</p>
                @else
                    <p class="site-projects-show__copy-9">Những đánh giá đầu tiên sẽ được hiển thị sau khi đội ngũ kiểm duyệt.</p>
                @endif
            </header>

            <div class="site-projects-show__div-11">
                <div class="d-grid site-projects-show__div-12">
                    @forelse ($project->approvedComments as $comment)
                        <article class="d-flex site-projects-show__article-13">
                            <span class="d-grid flex-shrink-0 fw-bold site-projects-show__copy-14" aria-hidden="true">{{ mb_strtoupper(mb_substr($comment->author_name, 0, 1)) }}</span>
                            <div class="site-projects-show__div-15">
                                <div class="d-flex flex-wrap align-items-baseline site-projects-show__div-16"><h3 class="fw-semibold site-projects-show__heading-17">{{ $comment->author_name }}</h3><time class="fw-medium site-projects-show__copy-18" datetime="{{ $comment->approved_at?->toDateString() }}">{{ $comment->approved_at?->translatedFormat('d/m/Y') }}</time></div>
                                @if ($comment->rating)<p class="site-projects-show__copy-19" aria-label="{{ $comment->rating }} trên 5 sao">@for ($star = 1; $star <= 5; $star++)<span class="{{ $star <= $comment->rating ? '' : 'site-projects-show__element-20' }}" aria-hidden="true">★</span>@endfor</p>@endif
                                <p class="site-projects-show__copy-21">{{ $comment->body }}</p>
                            </div>
                        </article>
                    @empty
                        <p class="site-projects-show__copy-22">Chưa có đánh giá nào. Anh/chị có thể là người đầu tiên chia sẻ trải nghiệm.</p>
                    @endforelse
                </div>

                <x-comment-form :project="$project" :rating-enabled="true" :compact="true" />
            </div>
        </div>
    </section>

    @if ($relatedProjects->isNotEmpty())
        <section id="du-an-lien-quan" class="resource-related-section">
            <div class="site-container w-100 mx-auto flex-column site-projects-show__div-23"><div><h2 class="display-title site-projects-show__heading-24">Khám phá thêm các dự án đã thực hiện</h2></div><a class="section-link" href="{{ LocalizedUrl::route('projects.index') }}">Xem tất cả dự án <span aria-hidden="true">→</span></a></div>
            <div class="site-container w-100 mx-auto site-projects-show__div-25">@foreach ($relatedProjects as $project) @include('frontend.partials.project-card') @endforeach</div>
        </section>
    @endif
@endsection
