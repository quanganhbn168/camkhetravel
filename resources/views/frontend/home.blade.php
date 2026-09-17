@extends('layouts.master')

@php
    /*
    |--------------------------------------------------------------------------
    | Contact
    |--------------------------------------------------------------------------
    */
    $contactPhones = collect($website->phones ?? [])
        ->filter(fn ($phone) => is_array($phone) && filled($phone['number'] ?? null))
        ->values();

    if ($contactPhones->isEmpty()) {
        $contactPhones = collect([
            ['number' => $website->hotline],
            ['number' => $website->contact_phone],
        ])
            ->filter(fn ($phone) => filled($phone['number'] ?? null))
            ->values();
    }

    $primaryPhone = data_get($contactPhones->first(), 'number');

    $contactBranches = collect($website->branches ?? [])
        ->filter(
            fn ($branch) =>
                is_array($branch)
                && ($branch['is_active'] ?? true)
                && filled($branch['address'] ?? null)
        )
        ->values();

    /*
    |--------------------------------------------------------------------------
    | Project home
    |--------------------------------------------------------------------------
    */
    $firstProjectTab = $projectTabs->first();

    $featuredProjects = collect();

    if (is_array($firstProjectTab)) {
        if (! empty($firstProjectTab['primary'])) {
            $featuredProjects->push($firstProjectTab['primary']);
        }

        $featuredProjects = $featuredProjects
            ->concat($firstProjectTab['secondary'] ?? [])
            ->filter()
            ->take(4)
            ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | News
    |--------------------------------------------------------------------------
    */
    $featuredPost = $posts->first();
    $sidePosts = $posts->slice(1, 3)->values();

    /*
    |--------------------------------------------------------------------------
    | Images
    |--------------------------------------------------------------------------
    */
    $solutionImageUrl =
        $featuredServiceCategories->first()?->home_image_url
        ?: $services->first()?->image_url
        ?: $aboutImageUrl
        ?: ($defaultBannerUrl ?? null);

    $whyImageUrl =
        $featuredProjects->first()?->image_url
        ?: $aboutImageUrl
        ?: ($defaultBannerUrl ?? null);

    /*
    |--------------------------------------------------------------------------
    | 4 USP
    |--------------------------------------------------------------------------
    */
    $uspItems = [
        [
            'title' => 'Khảo sát thực tế',
            'description' => 'Đánh giá chính xác yêu cầu',
        ],
        [
            'title' => 'Thi công đồng bộ',
            'description' => 'Đảm bảo chất lượng toàn diện',
        ],
        [
            'title' => 'Hỗ trợ hồ sơ pháp lý',
            'description' => 'Tư vấn đúng quy định',
        ],
        [
            'title' => 'Bảo trì dài hạn',
            'description' => 'Đồng hành sau bàn giao',
        ],
    ];

    /*
    |--------------------------------------------------------------------------
    | Giải pháp
    |--------------------------------------------------------------------------
    */
    $solutions = [
        [
            'key' => 'factory',
            'name' => 'Nhà xưởng',
            'title' => 'Giải pháp PCCC nhà xưởng',
            'description' => 'Hệ thống PCCC được thiết kế đồng bộ theo đặc thù sản xuất, quy mô và mức độ rủi ro của từng nhà máy.',
            'items' => [
                'Hệ thống báo cháy tự động',
                'Hệ thống chữa cháy Sprinkler',
                'Hệ thống cấp nước chữa cháy',
                'Hệ thống bơm và van',
                'Giải pháp thoát hiểm và chỉ dẫn an toàn',
            ],
        ],
        [
            'key' => 'warehouse',
            'name' => 'Kho bãi',
            'title' => 'Giải pháp PCCC kho bãi',
            'description' => 'Tập trung phát hiện sớm, kiểm soát cháy lan và bảo vệ hàng hóa, tài sản trong khu vực lưu trữ.',
            'items' => [
                'Báo cháy tự động',
                'Sprinkler chữa cháy',
                'Họng nước chữa cháy',
                'Bơm chữa cháy',
                'Chiếu sáng và chỉ dẫn thoát nạn',
            ],
        ],
        [
            'key' => 'office',
            'name' => 'Văn phòng',
            'title' => 'Giải pháp PCCC văn phòng',
            'description' => 'Giải pháp đảm bảo an toàn, thẩm mỹ và phù hợp với đặc thù vận hành của khối văn phòng.',
            'items' => [
                'Hệ thống báo cháy',
                'Bình chữa cháy',
                'Đèn exit và chiếu sáng sự cố',
                'Họng nước vách tường',
                'Phương án thoát nạn',
            ],
        ],
        [
            'key' => 'hotel',
            'name' => 'Khách sạn',
            'title' => 'Giải pháp PCCC khách sạn',
            'description' => 'Tăng khả năng phát hiện sớm và đảm bảo an toàn cho các khu vực lưu trú tập trung đông người.',
            'items' => [
                'Báo cháy địa chỉ',
                'Hệ thống Sprinkler',
                'Tăng áp và hút khói',
                'Họng nước chữa cháy',
                'Hệ thống thoát nạn',
            ],
        ],
        [
            'key' => 'apartment',
            'name' => 'Chung cư',
            'title' => 'Giải pháp PCCC chung cư',
            'description' => 'Hệ thống đồng bộ từ phát hiện cháy, chữa cháy đến thoát hiểm, chống khói và cứu nạn.',
            'items' => [
                'Báo cháy tự động',
                'Sprinkler',
                'Tăng áp cầu thang',
                'Hút khói hành lang',
                'Họng nước chữa cháy',
            ],
        ],
        [
            'key' => 'restaurant',
            'name' => 'Nhà hàng',
            'title' => 'Giải pháp PCCC nhà hàng',
            'description' => 'Giải pháp phù hợp khu vực bếp, không gian phục vụ khách và hệ thống điện của nhà hàng.',
            'items' => [
                'Báo cháy tự động',
                'Bình chữa cháy',
                'Giải pháp khu vực bếp',
                'Chiếu sáng sự cố',
                'Lối thoát hiểm',
            ],
        ],
        [
            'key' => 'commercial',
            'name' => 'Trung tâm thương mại',
            'title' => 'Giải pháp PCCC trung tâm thương mại',
            'description' => 'Giải pháp quy mô lớn, đồng bộ nhiều hệ thống và phù hợp khu vực tập trung đông người.',
            'items' => [
                'Báo cháy địa chỉ',
                'Sprinkler tự động',
                'Hút khói và tăng áp',
                'Họng nước chữa cháy',
                'Điều khiển liên động',
            ],
        ],
    ];

    /*
    |--------------------------------------------------------------------------
    | Tại sao chọn
    |--------------------------------------------------------------------------
    */
    $whyChooseUs = [
        [
            'number' => '01',
            'title' => 'Khảo sát kỹ hiện trạng',
            'description' => 'Đánh giá chi tiết để đưa ra phương án phù hợp thực tế.',
        ],
        [
            'number' => '02',
            'title' => 'Phương án tối ưu',
            'description' => 'Giải pháp phù hợp công năng, ngân sách và yêu cầu công trình.',
        ],
        [
            'number' => '03',
            'title' => 'Thi công đồng bộ',
            'description' => 'Đảm bảo chất lượng, tiến độ và tính thống nhất của hệ thống.',
        ],
        [
            'number' => '04',
            'title' => 'Hỗ trợ sau bàn giao',
            'description' => 'Bảo trì định kỳ và đồng hành trong quá trình vận hành.',
        ],
    ];

    /*
    |--------------------------------------------------------------------------
    | Quy trình
    |--------------------------------------------------------------------------
    */
    $processSteps = [
        [
            'number' => '01',
            'title' => 'Khảo sát',
            'description' => 'Nắm hiện trạng',
        ],
        [
            'number' => '02',
            'title' => 'Phân tích',
            'description' => 'Đề xuất giải pháp',
        ],
        [
            'number' => '03',
            'title' => 'Thiết kế',
            'description' => 'Hoàn thiện hồ sơ',
        ],
        [
            'number' => '04',
            'title' => 'Thi công',
            'description' => 'Lắp đặt đồng bộ',
        ],
        [
            'number' => '05',
            'title' => 'Kiểm tra',
            'description' => 'Nghiệm thu bàn giao',
        ],
        [
            'number' => '06',
            'title' => 'Bảo trì',
            'description' => 'Hỗ trợ lâu dài',
        ],
    ];

    /*
    |--------------------------------------------------------------------------
    | Nhóm sản phẩm - tạm fix cứng
    |--------------------------------------------------------------------------
    */
    $productGroups = [
        ['code' => 'BC', 'name' => 'Bình chữa cháy'],
        ['code' => 'TB', 'name' => 'Trung tâm báo cháy'],
        ['code' => 'DB', 'name' => 'Đầu báo khói'],
        ['code' => 'BM', 'name' => 'Máy bơm chữa cháy'],
        ['code' => 'VN', 'name' => 'Van tín hiệu'],
        ['code' => 'TP', 'name' => 'Tủ điều khiển PCCC'],
        ['code' => 'SP', 'name' => 'Sprinkler'],
    ];

    /*
    |--------------------------------------------------------------------------
    | Chứng nhận - placeholder tạm
    |--------------------------------------------------------------------------
    */
    $certificateItems = [
        'Hồ sơ năng lực',
        'Hồ sơ pháp lý',
        'Chứng nhận / chứng chỉ 01',
        'Chứng nhận / chứng chỉ 02',
        'Chứng nhận / chứng chỉ 03',
    ];
@endphp

@section('body_class', 'home-page min-h-screen bg-white')

@section('content')

    {{-- ============================================================
        HERO - GIỮ NGUYÊN SLIDER HIỆN TẠI
    ============================================================ --}}
    <section
        class="pccc-hero brand-gradient-dark relative isolate w-full overflow-hidden text-white"
        data-hero-section
    >
        @forelse ($heroSlides as $slide)

            @if ($loop->first)
                <div class="swiper hero-swiper w-full" data-hero-swiper>
                    <div class="swiper-wrapper">
            @endif

            <article class="pccc-hero__slide swiper-slide relative h-auto overflow-hidden">

                @if ($slide['has_content'])
                    <div class="pccc-hero__veil hero-brand-glow absolute inset-0"></div>
                @endif

                @if ($slide['image_url'])
                    <img
                        class="pccc-hero__image"
                        src="{{ $slide['image_url'] }}"
                        alt=""
                        @if ($loop->first)
                            fetchpriority="high"
                        @else
                            loading="lazy"
                        @endif
                    >
                @endif

                @if ($slide['video_url'])
                    <a
                        class="hero-video-play glightbox"
                        href="{{ $slide['video_url'] }}"
                        data-type="video"
                        data-source="{{ $slide['video_source'] === 'youtube' ? 'youtube' : 'local' }}"
                        data-gallery="hero-video-{{ $loop->index }}"
                        data-title="{{ $slide['title'] ?: 'Video DVTEC' }}"
                        target="_blank"
                        rel="noopener"
                        aria-label="Phát video {{ $slide['title'] ?: 'DVTEC' }}"
                    >
                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M8 5.1v13.8L19 12 8 5.1Z"/>
                        </svg>
                    </a>
                @endif

                @if ($slide['has_content'])
                    <div class="pccc-hero__inner site-container relative z-10 mx-auto grid w-full max-w-7xl items-end px-4 py-16 md:py-24 lg:px-8 lg:py-28">

                        <div class="pccc-hero__copy max-w-4xl pb-24 lg:pb-16">

                            @if ($slide['title'])
                                <h2 class="font-display max-w-4xl text-3xl leading-[1.12] tracking-[-0.045em] text-white sm:text-4xl lg:text-6xl">
                                    {{ $slide['title'] }}
                                </h2>
                            @endif

                            @if ($slide['description'])
                                <p class="mt-7 max-w-2xl text-base leading-8 text-slate-300 md:text-lg">
                                    {{ $slide['description'] }}
                                </p>
                            @endif

                            @if ($slide['has_primary_cta'] || $slide['has_secondary_cta'])
                                <div class="mt-9 flex flex-wrap gap-3">

                                    @if ($slide['has_primary_cta'])
                                        <a
                                            class="button-primary"
                                            href="{{ $slide['primary_url'] }}"
                                        >
                                            {{ $slide['primary_label'] }}
                                            <span aria-hidden="true">↗</span>
                                        </a>
                                    @endif

                                    @if ($slide['has_secondary_cta'])
                                        <a
                                            class="button-secondary"
                                            href="{{ $slide['secondary_url'] }}"
                                        >
                                            {{ $slide['secondary_label'] }}
                                        </a>
                                    @endif

                                </div>
                            @endif

                        </div>
                    </div>

                    @if ($slide['image_url'])
                        <div class="hero-image-overlay" aria-hidden="true"></div>
                    @endif
                @endif

            </article>

            @if ($loop->last)
                    </div>
                </div>
            @endif

        @empty

            <div class="pccc-hero__veil hero-brand-glow absolute inset-0"></div>

            <div class="pccc-hero__inner site-container relative mx-auto grid w-full max-w-7xl items-end px-4 py-24 md:py-32 lg:px-8 lg:py-40">

                <div class="pccc-hero__copy max-w-4xl pb-16">

                    <h2 class="font-display max-w-4xl text-4xl leading-[1.12] tracking-[-0.045em] text-white lg:text-6xl">
                        Kiến tạo hệ thống PCCC an toàn, đồng bộ và bền vững.
                    </h2>

                </div>

            </div>

        @endforelse
    </section>


    {{-- ============================================================
        4 USP
    ============================================================ --}}
    <section class="border-b border-slate-200 bg-white">

        <div class="site-container mx-auto grid w-full max-w-7xl grid-cols-2 px-4 lg:grid-cols-4 lg:px-8">

            @foreach ($uspItems as $item)

                <article class="flex gap-3 border-b border-slate-100 px-3 py-5 lg:border-b-0 lg:border-r lg:px-5 lg:first:border-l">

                    <div class="grid size-10 shrink-0 place-items-center rounded-full bg-red-50 text-primary">

                        <svg
                            class="size-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            aria-hidden="true"
                        >
                            <path d="M20 6 9 17l-5-5"/>
                        </svg>

                    </div>

                    <div>

                        <h3 class="text-xs font-bold uppercase leading-5 text-ink sm:text-sm">
                            {{ $item['title'] }}
                        </h3>

                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            {{ $item['description'] }}
                        </p>

                    </div>

                </article>

            @endforeach

        </div>

    </section>


    {{-- ============================================================
        DỊCH VỤ
    ============================================================ --}}
    <section class="section-space bg-white" id="dich-vu">

        <div class="site-container mx-auto w-full max-w-7xl px-4 lg:px-8">

            <header class="text-center">

                <p class="text-sm font-bold uppercase tracking-[0.16em] text-primary">
                    Dịch vụ PCCC
                </p>

                <h2 class="display-title mt-3 text-3xl uppercase leading-tight md:text-4xl">
                    Dịch vụ PCCC toàn diện
                </h2>

                <p class="mx-auto mt-4 max-w-2xl text-base leading-8 text-slate-600">
                    Đồng hành cùng doanh nghiệp từ khảo sát, thiết kế,
                    thi công đến cải tạo, bảo trì và tư vấn hệ thống PCCC.
                </p>

            </header>


            @if ($featuredServiceCategories->isNotEmpty())

                <div
                    class="mt-9"
                    x-data="{ activeCategory: '{{ $featuredServiceCategories->first()->id }}' }"
                >

                    {{-- Tabs --}}
                    <div class="flex gap-2 overflow-x-auto border-b border-slate-200 pb-0">

                        @foreach ($featuredServiceCategories as $category)

                            <button
                                type="button"
                                class="shrink-0 border-b-2 px-5 py-3 text-sm font-semibold transition"
                                :class="activeCategory === '{{ $category->id }}'
                                    ? 'border-primary bg-primary text-white'
                                    : 'border-transparent text-slate-600 hover:text-primary'"
                                x-on:click="activeCategory = '{{ $category->id }}'"
                            >
                                {{ $category->name }}
                            </button>

                        @endforeach

                    </div>


                    {{-- Content --}}
                    <div class="mt-7">

                        @foreach ($featuredServiceCategories as $category)

                            <div
                                x-cloak
                                x-show="activeCategory === '{{ $category->id }}'"
                                x-transition.opacity.duration.200ms
                            >

                                <div class="grid gap-8 lg:grid-cols-[1.08fr_.92fr] lg:items-stretch">

                                    {{-- Image --}}
                                    <a
                                        href="{{ route('services.category', ['category' => $category->slug]) }}"
                                        class="group relative min-h-[320px] overflow-hidden rounded-xl bg-slate-100 lg:min-h-[430px]"
                                    >

                                        @if ($category->home_image_url)

                                            <img
                                                src="{{ $category->home_image_url }}"
                                                alt="{{ $category->home_image_alt ?: $category->name }}"
                                                class="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-105"
                                                loading="lazy"
                                            >

                                        @else

                                            <div class="absolute inset-0 grid place-items-center text-3xl font-bold text-slate-300">
                                                DVTEC
                                            </div>

                                        @endif

                                        <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent"></div>

                                    </a>


                                    {{-- Text --}}
                                    <div class="flex flex-col justify-center">

                                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-primary">
                                            Dịch vụ nổi bật
                                        </p>

                                        <h3 class="mt-3 text-2xl font-bold uppercase leading-tight text-ink md:text-3xl">
                                            {{ $category->name }}
                                        </h3>

                                        @if ($category->description)
                                            <p class="mt-4 text-base leading-7 text-slate-600">
                                                {{ $category->description }}
                                            </p>
                                        @endif


                                        @if ($category->services->isNotEmpty())

                                            <ul class="mt-6 grid gap-3">

                                                @foreach ($category->services->take(5) as $service)

                                                    <li>

                                                        <a
                                                            href="{{ route('slug.show', ['slug' => $service->slug]) }}"
                                                            class="group flex items-center gap-3 text-sm font-medium text-slate-700 transition hover:text-primary"
                                                        >

                                                            <span class="grid size-6 shrink-0 place-items-center rounded-full bg-red-50 text-xs font-bold text-primary">
                                                                ✓
                                                            </span>

                                                            <span>
                                                                {{ $service->title }}
                                                            </span>

                                                        </a>

                                                    </li>

                                                @endforeach

                                            </ul>

                                        @endif


                                        <div class="mt-8 flex flex-wrap gap-3">

                                            <a
                                                href="{{ route('services.category', ['category' => $category->slug]) }}"
                                                class="button-primary"
                                            >
                                                Xem chi tiết
                                                <span aria-hidden="true">→</span>
                                            </a>

                                            <a
                                                href="{{ route('services.index') }}"
                                                class="button-secondary"
                                            >
                                                Tất cả dịch vụ
                                            </a>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>

                </div>

            @else

                {{-- Fallback nếu chưa set category nổi bật --}}
                <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">

                    @forelse ($services as $service)

                        <article class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

                            <a
                                href="{{ route('slug.show', ['slug' => $service->slug]) }}"
                                class="block aspect-[16/10] overflow-hidden bg-slate-100"
                            >

                                @if ($service->image_url)

                                    <img
                                        src="{{ $service->image_url }}"
                                        alt="{{ $service->title }}"
                                        class="h-full w-full object-cover transition duration-500 hover:scale-105"
                                        loading="lazy"
                                    >

                                @endif

                            </a>

                            <div class="p-5">

                                <h3 class="text-lg font-bold text-ink">
                                    <a href="{{ route('slug.show', ['slug' => $service->slug]) }}">
                                        {{ $service->title }}
                                    </a>
                                </h3>

                            </div>

                        </article>

                    @empty

                        <p class="col-span-full rounded-xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500">
                            Dịch vụ sẽ được cập nhật sớm.
                        </p>

                    @endforelse

                </div>

            @endif

        </div>

    </section>


    {{-- ============================================================
        GIẢI PHÁP
    ============================================================ --}}
    <section
        class="section-space overflow-hidden bg-[#071620] text-white"
        id="giai-phap"
        x-data="{ activeSolution: 'factory' }"
    >

        <div class="site-container mx-auto w-full max-w-7xl px-4 lg:px-8">

            <div class="grid gap-8 lg:grid-cols-[240px_1fr]">

                {{-- Sidebar --}}
                <aside>

                    <p class="text-sm font-bold uppercase tracking-[0.16em] text-primary">
                        Giải pháp PCCC
                    </p>

                    <h2 class="mt-3 text-3xl font-bold uppercase leading-tight text-white">
                        Theo loại công trình
                    </h2>

                    <p class="mt-4 text-sm leading-7 text-slate-400">
                        Mỗi loại công trình có yêu cầu vận hành và mức độ
                        rủi ro khác nhau. Giải pháp cần được thiết kế phù hợp ngay từ đầu.
                    </p>


                    <div class="mt-7 grid">

                        @foreach ($solutions as $solution)

                            <button
                                type="button"
                                class="border-b border-white/10 px-4 py-3 text-left text-sm font-semibold transition"
                                :class="activeSolution === '{{ $solution['key'] }}'
                                    ? 'bg-primary text-white'
                                    : 'text-slate-300 hover:bg-white/5 hover:text-white'"
                                x-on:click="activeSolution = '{{ $solution['key'] }}'"
                            >
                                {{ $solution['name'] }}
                            </button>

                        @endforeach

                    </div>

                </aside>


                {{-- Content --}}
                <div>

                    @foreach ($solutions as $solution)

                        <div
                            x-cloak
                            x-show="activeSolution === '{{ $solution['key'] }}'"
                            x-transition.opacity.duration.200ms
                        >

                            <article class="grid overflow-hidden rounded-xl border border-white/10 bg-[#0b202d] lg:grid-cols-[1fr_320px]">

                                <div class="relative min-h-[420px]">

                                    @if ($solutionImageUrl)

                                        <img
                                            src="{{ $solutionImageUrl }}"
                                            alt="{{ $solution['title'] }}"
                                            class="absolute inset-0 h-full w-full object-cover"
                                            loading="lazy"
                                        >

                                    @endif

                                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>


                                    <div class="absolute inset-x-0 bottom-0 p-7 md:p-9">

                                        <h3 class="text-2xl font-bold text-white md:text-3xl">
                                            {{ $solution['title'] }}
                                        </h3>

                                        <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-300">
                                            {{ $solution['description'] }}
                                        </p>

                                        <a
                                            href="{{ route('services.index') }}"
                                            class="button-primary mt-6"
                                        >
                                            Xem giải pháp
                                            <span aria-hidden="true">→</span>
                                        </a>

                                    </div>

                                </div>


                                <div class="bg-white p-6 text-ink lg:p-8">

                                    <ul class="grid gap-5">

                                        @foreach ($solution['items'] as $item)

                                            <li class="flex gap-3">

                                                <span class="mt-0.5 grid size-5 shrink-0 place-items-center rounded-full bg-red-50 text-xs font-bold text-primary">
                                                    ✓
                                                </span>

                                                <span class="text-sm leading-6">
                                                    {{ $item }}
                                                </span>

                                            </li>

                                        @endforeach

                                    </ul>

                                </div>

                            </article>

                        </div>

                    @endforeach

                </div>

            </div>

        </div>

    </section>


    {{-- ============================================================
        VỀ CHÚNG TÔI
    ============================================================ --}}
    <section class="section-space bg-white" id="gioi-thieu">

        <div class="site-container mx-auto grid w-full max-w-7xl items-center gap-10 px-4 lg:grid-cols-[.9fr_1.1fr] lg:px-8">

            {{-- Text --}}
            <div>

                <p class="text-sm font-bold uppercase tracking-[0.16em] text-primary">
                    Về chúng tôi
                </p>

                <h2 class="display-title mt-3 text-3xl uppercase leading-tight md:text-4xl">
                    Một hệ thống PCCC tốt
                    <br>
                    không chỉ nằm ở thiết bị
                </h2>


                @if ($about['title'])

                    <p class="mt-5 text-base font-semibold leading-7 text-slate-700">
                        {{ $about['title'] }}
                    </p>

                @endif


                @if ($about['content'])

                    <p class="mt-4 text-base leading-8 text-slate-600">
                        {{ $about['content'] }}
                    </p>

                @endif


                <ul class="mt-6 grid gap-3 text-sm text-slate-700">

                    <li class="flex gap-3">
                        <span class="text-primary">●</span>
                        Đội ngũ kỹ sư và kỹ thuật viên có kinh nghiệm
                    </li>

                    <li class="flex gap-3">
                        <span class="text-primary">●</span>
                        Quy trình triển khai rõ ràng, minh bạch
                    </li>

                    <li class="flex gap-3">
                        <span class="text-primary">●</span>
                        Giải pháp tối ưu theo từng loại công trình
                    </li>

                    <li class="flex gap-3">
                        <span class="text-primary">●</span>
                        Hỗ trợ vận hành và bảo trì sau bàn giao
                    </li>

                </ul>


                <div class="mt-8 flex flex-wrap gap-4">

                    <a
                        href="{{ route('about') }}"
                        class="button-primary"
                    >
                        Tìm hiểu về chúng tôi
                        <span aria-hidden="true">→</span>
                    </a>


                    @if ($companyProfileUrl)

                        <a
                            href="{{ $companyProfileUrl }}"
                            download
                            class="inline-flex min-h-12 items-center gap-2 text-sm font-semibold text-ink underline decoration-primary/50 decoration-2 underline-offset-6 hover:text-primary"
                        >
                            Tải hồ sơ năng lực
                        </a>

                    @endif

                </div>

            </div>


            {{-- Images --}}
            <div class="grid grid-cols-5 gap-3">

                <div class="col-span-3 row-span-2 overflow-hidden rounded-xl bg-slate-100">

                    @if ($aboutImageUrl)

                        <img
                            src="{{ $aboutImageUrl }}"
                            alt="{{ $companyName }}"
                            class="h-full min-h-[430px] w-full object-cover"
                            loading="lazy"
                        >

                    @else

                        <div class="grid min-h-[430px] place-items-center text-3xl font-bold text-slate-300">
                            DVTEC
                        </div>

                    @endif

                </div>


                <div class="col-span-2 overflow-hidden rounded-xl bg-slate-100">

                    @if ($solutionImageUrl)

                        <img
                            src="{{ $solutionImageUrl }}"
                            alt="Hệ thống PCCC"
                            class="h-full min-h-[205px] w-full object-cover"
                            loading="lazy"
                        >

                    @endif

                </div>


                <div class="col-span-2 flex min-h-[205px] items-end rounded-xl bg-[#071620] p-6 text-white">

                    <p class="text-lg font-semibold leading-7">
                        Giải pháp an toàn
                        <br>
                        cho hôm nay
                        <br>
                        và tương lai bền vững
                    </p>

                </div>

            </div>

        </div>

    </section>


    {{-- ============================================================
        TẠI SAO CHỌN CHÚNG TÔI
    ============================================================ --}}
    <section class="overflow-hidden bg-[#071620] text-white" id="tai-sao-chon-chung-toi">

        <div class="grid lg:grid-cols-[.75fr_1.25fr]">

            {{-- Image --}}
            <div class="relative min-h-[360px] lg:min-h-[430px]">

                @if ($whyImageUrl)

                    <img
                        src="{{ $whyImageUrl }}"
                        alt="Đội ngũ {{ $companyName }}"
                        class="absolute inset-0 h-full w-full object-cover"
                        loading="lazy"
                    >

                @endif

                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-transparent to-[#071620]"></div>

            </div>


            {{-- Content --}}
            <div class="flex items-center px-6 py-12 lg:px-14">

                <div class="w-full">

                    <p class="text-sm font-bold uppercase tracking-[0.16em] text-primary">
                        Tại sao chọn chúng tôi
                    </p>

                    <h2 class="mt-3 text-3xl font-bold uppercase leading-tight text-white">
                        Năng lực thực tế
                        <span class="text-slate-500">·</span>
                        Cam kết lâu dài
                    </h2>


                    <div class="mt-10 grid gap-7 sm:grid-cols-2 xl:grid-cols-4">

                        @foreach ($whyChooseUs as $item)

                            <article class="border-l border-white/10 pl-5">

                                <span class="text-3xl font-bold text-primary">
                                    {{ $item['number'] }}
                                </span>

                                <h3 class="mt-4 text-sm font-bold uppercase leading-6 text-white">
                                    {{ $item['title'] }}
                                </h3>

                                <p class="mt-2 text-xs leading-6 text-slate-400">
                                    {{ $item['description'] }}
                                </p>

                            </article>

                        @endforeach

                    </div>

                </div>

            </div>

        </div>

    </section>


    {{-- ============================================================
        DỰ ÁN
    ============================================================ --}}
    <section class="section-space bg-white" id="du-an">

        <div class="site-container mx-auto w-full max-w-7xl px-4 lg:px-8">

            <div class="flex flex-col justify-between gap-5 md:flex-row md:items-end">

                <div>

                    <p class="text-sm font-bold uppercase tracking-[0.16em] text-primary">
                        Công trình đã triển khai
                    </p>

                    <h2 class="display-title mt-3 text-3xl uppercase leading-tight md:text-4xl">
                        Dự án tiêu biểu
                    </h2>

                </div>


                <a
                    href="{{ route('projects.index') }}"
                    class="section-link"
                >
                    Xem tất cả dự án
                    <span aria-hidden="true">→</span>
                </a>

            </div>


            @if ($featuredProjects->isNotEmpty())

                <div class="mt-9 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">

                    @foreach ($featuredProjects as $project)

                        <article class="group overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

                            <a
                                href="{{ route('projects.show', ['slug' => $project->slug]) }}"
                                class="relative block aspect-[4/3] overflow-hidden bg-slate-100"
                            >

                                @if ($project->image_url)

                                    <img
                                        src="{{ $project->image_url }}"
                                        alt="{{ $project->title }}"
                                        class="h-full w-full object-cover transition duration-700 group-hover:scale-105"
                                        loading="lazy"
                                    >

                                @endif

                            </a>


                            <div class="p-4">

                                @if ($project->category)

                                    <p class="text-xs font-semibold uppercase tracking-wide text-primary">
                                        {{ $project->category->name }}
                                    </p>

                                @endif


                                <h3 class="mt-2 line-clamp-2 text-base font-bold leading-6 text-ink">

                                    <a
                                        href="{{ route('projects.show', ['slug' => $project->slug]) }}"
                                        class="hover:text-primary"
                                    >
                                        {{ $project->title }}
                                    </a>

                                </h3>


                                @if ($project->excerpt)

                                    <p class="mt-2 line-clamp-2 text-xs leading-6 text-slate-500">
                                        {{ $project->excerpt }}
                                    </p>

                                @endif

                            </div>

                        </article>

                    @endforeach

                </div>

            @else

                <p class="mt-8 rounded-xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500">
                    Dự án đang được cập nhật.
                </p>

            @endif

        </div>

    </section>


    {{-- ============================================================
        QUY TRÌNH
    ============================================================ --}}
    <section class="section-space border-y border-slate-200 bg-slate-50" id="quy-trinh">

        <div class="site-container mx-auto w-full max-w-7xl px-4 lg:px-8">

            <header>

                <p class="text-sm font-bold uppercase tracking-[0.16em] text-primary">
                    Quy trình triển khai
                </p>

                <h2 class="display-title mt-3 text-3xl uppercase leading-tight md:text-4xl">
                    Rõ ràng · Chuyên nghiệp · Minh bạch
                </h2>

            </header>


            <div class="mt-9 grid gap-4 md:grid-cols-3 xl:grid-cols-6">

                @foreach ($processSteps as $step)

                    <article
                        @class([
                            'relative rounded-xl border p-5',
                            'border-primary bg-primary text-white' => $loop->first,
                            'border-slate-200 bg-white text-ink' => ! $loop->first,
                        ])
                    >

                        <span
                            @class([
                                'text-3xl font-bold',
                                'text-white/40' => $loop->first,
                                'text-primary' => ! $loop->first,
                            ])
                        >
                            {{ $step['number'] }}
                        </span>

                        <h3 class="mt-6 text-sm font-bold uppercase">
                            {{ $step['title'] }}
                        </h3>

                        <p
                            @class([
                                'mt-2 text-xs leading-5',
                                'text-white/75' => $loop->first,
                                'text-slate-500' => ! $loop->first,
                            ])
                        >
                            {{ $step['description'] }}
                        </p>

                        @if (! $loop->last)

                            <span class="absolute -right-3 top-1/2 z-10 hidden -translate-y-1/2 text-xl font-bold text-primary xl:block">
                                →
                            </span>

                        @endif

                    </article>

                @endforeach

            </div>

        </div>

    </section>


    {{-- ============================================================
        NĂNG LỰC / STATS
    ============================================================ --}}
    @if ($stats->isNotEmpty())

        <section class="bg-white py-10">

            <div class="site-container mx-auto w-full max-w-7xl px-4 lg:px-8">

                <div class="grid grid-cols-2 gap-7 md:grid-cols-4 md:gap-10">

                    @foreach ($stats as $stat)

                        <article class="text-center">

                            <p
                                class="text-3xl font-bold text-primary md:text-4xl"
                                aria-label="{{ $stat['prefix'] }}{{ collect($stat['segments'])->pluck('value')->join('') }}{{ $stat['suffix'] }}"
                            >

                                @if (filled($stat['prefix']))
                                    <span>{{ $stat['prefix'] }}</span>
                                @endif


                                @foreach ($stat['segments'] as $segment)

                                    @if ($segment['is_number'])

                                        <span data-count-up="{{ $segment['value'] }}">
                                            {{ $segment['value'] }}
                                        </span>

                                    @else

                                        <span>
                                            {{ $segment['value'] }}
                                        </span>

                                    @endif

                                @endforeach


                                @if (filled($stat['suffix']))
                                    <span>{{ $stat['suffix'] }}</span>
                                @endif

                            </p>

                            <p class="mt-2 text-sm font-medium text-slate-600">
                                {{ $stat['label'] }}
                            </p>

                        </article>

                    @endforeach

                </div>

            </div>

        </section>

    @endif


    {{-- ============================================================
        SẢN PHẨM
    ============================================================ --}}
    <section class="section-space border-t border-slate-100 bg-white" id="san-pham">

        <div class="site-container mx-auto w-full max-w-7xl px-4 lg:px-8">

            <div class="flex flex-col justify-between gap-5 md:flex-row md:items-end">

                <div>

                    <p class="text-sm font-bold uppercase tracking-[0.16em] text-primary">
                        Thiết bị PCCC
                    </p>

                    <h2 class="display-title mt-3 text-3xl uppercase leading-tight md:text-4xl">
                        Sản phẩm · Thiết bị
                    </h2>

                    <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">
                        Danh mục thiết bị phục vụ thi công, lắp đặt và vận hành hệ thống PCCC.
                    </p>

                </div>


                <a
                    href="{{ route('products.index') }}"
                    class="section-link"
                >
                    Xem tất cả sản phẩm
                    <span aria-hidden="true">→</span>
                </a>

            </div>


            <div class="mt-9 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-7">

                @foreach ($productGroups as $product)

                    <a
                        href="{{ route('products.index') }}"
                        class="group rounded-xl border border-slate-200 bg-white p-5 text-center transition hover:-translate-y-1 hover:border-primary hover:shadow-lg"
                    >

                        <div class="mx-auto grid size-16 place-items-center rounded-xl bg-red-50 text-lg font-bold text-primary transition group-hover:bg-primary group-hover:text-white">
                            {{ $product['code'] }}
                        </div>

                        <h3 class="mt-4 text-sm font-semibold leading-6 text-ink">
                            {{ $product['name'] }}
                        </h3>

                    </a>

                @endforeach

            </div>

        </div>

    </section>


    {{-- ============================================================
        ĐỐI TÁC
    ============================================================ --}}
    <section class="border-y border-slate-200 bg-slate-50 py-8">

        <div class="site-container mx-auto w-full max-w-7xl px-4 lg:px-8">

            <div class="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-end">

                <div>

                    <p class="text-sm font-bold uppercase tracking-[0.16em] text-primary">
                        Hợp tác & đồng hành
                    </p>

                    <h2 class="mt-2 text-xl font-bold uppercase text-ink">
                        Đối tác · Thương hiệu
                    </h2>

                </div>

            </div>

        </div>


        @if ($marqueePartners->isNotEmpty())

            <div class="partner-marquee">

                <div class="partner-marquee__track">

                    @foreach ($marqueePartners as $partner)

                        @if ($partner->website_url)

                            <a
                                class="partner-marquee__item"
                                href="{{ $partner->website_url }}"
                                target="_blank"
                                rel="noopener noreferrer"
                            >

                                @if ($partner->curatorMedia?->url)

                                    <img
                                        src="{{ $partner->curatorMedia->url }}"
                                        alt="{{ $partner->name }}"
                                        loading="lazy"
                                    >

                                @else

                                    {{ $partner->name }}

                                @endif

                            </a>

                        @else

                            <span class="partner-marquee__item">

                                @if ($partner->curatorMedia?->url)

                                    <img
                                        src="{{ $partner->curatorMedia->url }}"
                                        alt="{{ $partner->name }}"
                                        loading="lazy"
                                    >

                                @else

                                    {{ $partner->name }}

                                @endif

                            </span>

                        @endif

                    @endforeach


                    {{-- duplicate để marquee chạy liên tục --}}
                    @foreach ($marqueePartners as $partner)

                        <span
                            class="partner-marquee__item"
                            aria-hidden="true"
                        >

                            @if ($partner->curatorMedia?->url)

                                <img
                                    src="{{ $partner->curatorMedia->url }}"
                                    alt=""
                                    loading="lazy"
                                >

                            @else

                                {{ $partner->name }}

                            @endif

                        </span>

                    @endforeach

                </div>

            </div>

        @endif

    </section>


    {{-- ============================================================
        CHỨNG NHẬN / NĂNG LỰC
        Hiện tạm dùng placeholder. Sau này gắn DB/media.
    ============================================================ --}}
    <section class="section-space bg-white" id="chung-nhan">

        <div class="site-container mx-auto w-full max-w-7xl px-4 lg:px-8">

            <div class="grid gap-8 lg:grid-cols-[240px_1fr] lg:items-center">

                <div>

                    <p class="text-sm font-bold uppercase tracking-[0.16em] text-primary">
                        Hồ sơ doanh nghiệp
                    </p>

                    <h2 class="display-title mt-3 text-2xl uppercase leading-tight">
                        Chứng nhận · Năng lực
                    </h2>

                    <p class="mt-4 text-sm leading-7 text-slate-500">
                        Minh bạch hồ sơ, năng lực và tài liệu liên quan.
                    </p>

                </div>


                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">

                    @foreach ($certificateItems as $certificate)

                        @if ($loop->first && $companyProfileUrl)

                            <a
                                href="{{ $companyProfileUrl }}"
                                target="_blank"
                                class="group"
                            >

                        @else

                            <div class="group">

                        @endif


                            <div class="aspect-[3/4] rounded-lg border border-slate-200 bg-slate-50 p-4 shadow-sm transition group-hover:border-primary group-hover:shadow-md">

                                <div class="flex h-full flex-col items-center justify-center text-center">

                                    <svg
                                        class="size-10 text-primary"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                    >
                                        <path d="M6 2h9l3 3v17H6z"/>
                                        <path d="M14 2v5h5"/>
                                        <path d="M9 12h6M9 16h6"/>
                                    </svg>

                                    <p class="mt-4 text-xs font-bold uppercase leading-5 text-ink">
                                        {{ $certificate }}
                                    </p>

                                    @if (! ($loop->first && $companyProfileUrl))

                                        <span class="mt-2 text-[10px] text-slate-400">
                                            Cập nhật tài liệu
                                        </span>

                                    @endif

                                </div>

                            </div>


                        @if ($loop->first && $companyProfileUrl)

                            </a>

                        @else

                            </div>

                        @endif

                    @endforeach

                </div>

            </div>

        </div>

    </section>


    {{-- ============================================================
        FORM TƯ VẤN
    ============================================================ --}}
    <section
        class="relative overflow-hidden bg-[#071620] py-12 text-white md:py-16"
        id="tu-van"
    >

        @if ($whyImageUrl)

            <img
                src="{{ $whyImageUrl }}"
                alt=""
                class="absolute inset-0 h-full w-full object-cover opacity-20"
                loading="lazy"
            >

        @endif

        <div class="absolute inset-0 bg-[#071620]/90"></div>


        <div class="site-container relative z-10 mx-auto grid w-full max-w-7xl gap-10 px-4 lg:grid-cols-[.8fr_1.2fr] lg:items-center lg:px-8">

            {{-- Left --}}
            <div>

                <p class="text-sm font-bold uppercase tracking-[0.16em] text-primary">
                    Tư vấn miễn phí
                </p>

                <h2 class="mt-3 text-3xl font-bold leading-tight text-white md:text-4xl">
                    Cần khảo sát hệ thống
                    <br>
                    PCCC cho công trình?
                </h2>

                <p class="mt-5 max-w-lg text-sm leading-7 text-slate-300">
                    Để lại thông tin, đội ngũ của chúng tôi sẽ liên hệ
                    và tư vấn phương án phù hợp với công trình của bạn.
                </p>


                <div class="mt-8 grid gap-4 text-sm">

                    @if ($primaryPhone)

                        <a
                            href="tel:{{ preg_replace('/\s+/', '', $primaryPhone) }}"
                            class="flex items-center gap-3 font-semibold text-white hover:text-primary"
                        >

                            <span class="grid size-10 place-items-center rounded-full bg-primary">
                                ☎
                            </span>

                            {{ $primaryPhone }}

                        </a>

                    @endif


                    @if ($website->contact_email)

                        <a
                            href="mailto:{{ $website->contact_email }}"
                            class="flex items-center gap-3 font-semibold text-white hover:text-primary"
                        >

                            <span class="grid size-10 place-items-center rounded-full bg-primary">
                                @
                            </span>

                            {{ $website->contact_email }}

                        </a>

                    @endif


                    @if ($website->zalo_url)

                        <a
                            href="{{ $website->zalo_url }}"
                            target="_blank"
                            rel="noopener"
                            class="flex items-center gap-3 font-semibold text-white hover:text-primary"
                        >

                            <span class="grid size-10 place-items-center rounded-full bg-primary text-xs">
                                Zalo
                            </span>

                            Chat qua Zalo
                        </a>

                    @endif

                </div>

            </div>


            {{-- Form --}}
            <form
                method="POST"
                action="{{ route('contact.store') }}"
                class="grid gap-4 rounded-xl bg-white p-5 text-ink shadow-2xl md:grid-cols-2 md:p-7"
            >

                @csrf


                <label class="grid gap-2 text-sm font-semibold">

                    Họ và tên

                    <input
                        class="form-field"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                    >

                </label>


                <label class="grid gap-2 text-sm font-semibold">

                    Số điện thoại

                    <input
                        class="form-field"
                        type="tel"
                        name="phone"
                        value="{{ old('phone') }}"
                    >

                </label>


                <label class="grid gap-2 text-sm font-semibold">

                    Dịch vụ quan tâm

                    <select
                        class="form-field"
                        name="service_id"
                    >

                        <option value="">
                            Chọn dịch vụ
                        </option>

                        @foreach ($contactServices as $service)

                            <option
                                value="{{ $service->id }}"
                                @selected(old('service_id') == $service->id)
                            >
                                {{ $service->title }}
                            </option>

                        @endforeach

                    </select>

                </label>


                <label class="grid gap-2 text-sm font-semibold">

                    Nhu cầu của bạn

                    <input
                        class="form-field"
                        type="text"
                        name="message"
                        value="{{ old('message') }}"
                        placeholder="Ví dụ: Khảo sát nhà xưởng..."
                        required
                    >

                </label>


                <div class="md:col-span-2">

                    <button
                        class="button-primary w-full justify-center"
                        type="submit"
                    >
                        Gửi yêu cầu tư vấn
                        <span aria-hidden="true">→</span>
                    </button>

                </div>

            </form>

        </div>

    </section>


    {{-- ============================================================
        TIN TỨC
    ============================================================ --}}
    <section class="section-space bg-white" id="tin-tuc">

        <div class="site-container mx-auto w-full max-w-7xl px-4 lg:px-8">

            <div class="flex flex-col justify-between gap-5 md:flex-row md:items-end">

                <div>

                    <p class="text-sm font-bold uppercase tracking-[0.16em] text-primary">
                        Kiến thức & cập nhật
                    </p>

                    <h2 class="display-title mt-3 text-3xl uppercase leading-tight md:text-4xl">
                        Tin tức PCCC
                    </h2>

                    <p class="mt-3 text-sm leading-7 text-slate-600">
                        Chia sẻ kiến thức, quy định và kinh nghiệm thực tế.
                    </p>

                </div>


                <a
                    href="{{ route('posts.index') }}"
                    class="section-link"
                >
                    Xem tất cả bài viết
                    <span aria-hidden="true">→</span>
                </a>

            </div>


            @if ($featuredPost)

                <div class="mt-9 grid gap-7 lg:grid-cols-[1.1fr_.9fr]">

                    {{-- Featured --}}
                    <article class="overflow-hidden rounded-xl border border-slate-200">

                        <a
                            href="{{ route('posts.show', ['slug' => $featuredPost->slug]) }}"
                            class="block aspect-[16/9] overflow-hidden bg-slate-100"
                        >

                            @if ($featuredPost->image_url)

                                <img
                                    src="{{ $featuredPost->image_url }}"
                                    alt="{{ $featuredPost->title }}"
                                    class="h-full w-full object-cover transition duration-700 hover:scale-105"
                                    loading="lazy"
                                >

                            @endif

                        </a>


                        <div class="p-6">

                            @if ($featuredPost->categories->first())

                                <p class="text-xs font-bold uppercase tracking-wide text-primary">
                                    {{ $featuredPost->categories->first()->name }}
                                </p>

                            @endif

                            <h3 class="mt-3 text-xl font-bold leading-8 text-ink">

                                <a
                                    href="{{ route('posts.show', ['slug' => $featuredPost->slug]) }}"
                                    class="hover:text-primary"
                                >
                                    {{ $featuredPost->title }}
                                </a>

                            </h3>


                            @if ($featuredPost->excerpt)

                                <p class="mt-3 line-clamp-3 text-sm leading-7 text-slate-600">
                                    {{ $featuredPost->excerpt }}
                                </p>

                            @endif

                        </div>

                    </article>


                    {{-- Side --}}
                    <div class="grid gap-4">

                        @foreach ($sidePosts as $post)

                            <article class="grid grid-cols-[130px_1fr] gap-4 rounded-xl border border-slate-200 p-3">

                                <a
                                    href="{{ route('posts.show', ['slug' => $post->slug]) }}"
                                    class="aspect-[4/3] overflow-hidden rounded-lg bg-slate-100"
                                >

                                    @if ($post->image_url)

                                        <img
                                            src="{{ $post->image_url }}"
                                            alt="{{ $post->title }}"
                                            class="h-full w-full object-cover"
                                            loading="lazy"
                                        >

                                    @endif

                                </a>


                                <div class="flex flex-col justify-center">

                                    <h3 class="line-clamp-3 text-sm font-bold leading-6 text-ink">

                                        <a
                                            href="{{ route('posts.show', ['slug' => $post->slug]) }}"
                                            class="hover:text-primary"
                                        >
                                            {{ $post->title }}
                                        </a>

                                    </h3>


                                    @if ($post->published_at)

                                        <p class="mt-2 text-xs text-slate-400">
                                            {{ $post->published_at->format('d/m/Y') }}
                                        </p>

                                    @endif

                                </div>

                            </article>

                        @endforeach

                    </div>

                </div>

            @else

                <p class="mt-8 rounded-xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500">
                    Bài viết sẽ được cập nhật sớm.
                </p>

            @endif

        </div>

    </section>


    {{-- ============================================================
        FAQ
    ============================================================ --}}
    @if ($faqItems->isNotEmpty())

        <section class="section-space border-t border-slate-200 bg-slate-50" id="cau-hoi">

            <div class="site-container mx-auto w-full max-w-7xl px-4 lg:px-8">

                <header class="mb-8">

                    <p class="text-sm font-bold uppercase tracking-[0.16em] text-primary">
                        Giải đáp nhanh
                    </p>

                    <h2 class="display-title mt-3 text-3xl uppercase leading-tight md:text-4xl">
                        {{ $faqTitle ?: 'Câu hỏi thường gặp' }}
                    </h2>


                    @if (filled($faqDescription))

                        <p class="mt-4 max-w-2xl text-base leading-8 text-slate-600">
                            {{ $faqDescription }}
                        </p>

                    @endif

                </header>


                <div class="grid gap-4 md:grid-cols-2">

                    @foreach ($faqItems as $item)

                        <details
                            class="group rounded-xl border border-slate-200 bg-white px-5"
                            @if ($loop->first) open @endif
                        >

                            <summary class="flex cursor-pointer list-none items-center justify-between gap-5 py-5 text-sm font-semibold text-ink">

                                <span>
                                    {{ $item['question'] }}
                                </span>

                                <span class="grid size-7 shrink-0 place-items-center rounded-full bg-slate-100 text-primary">
                                    +
                                </span>

                            </summary>


                            <div class="border-t border-slate-100 pb-5 pt-4">

                                <p class="text-sm leading-7 text-slate-600">
                                    {{ $item['answer'] }}
                                </p>

                            </div>

                        </details>

                    @endforeach

                </div>

            </div>

        </section>

    @endif


    {{-- ============================================================
        CTA CUỐI
    ============================================================ --}}
    <section class="relative isolate overflow-hidden bg-[#071620] py-12 text-white">

        @if ($solutionImageUrl)

            <img
                src="{{ $solutionImageUrl }}"
                alt=""
                class="absolute inset-0 -z-20 h-full w-full object-cover"
                loading="lazy"
            >

        @endif

        <div class="absolute inset-0 -z-10 bg-black/75"></div>


        <div class="site-container mx-auto flex w-full max-w-7xl flex-col gap-7 px-4 md:flex-row md:items-center md:justify-between lg:px-8">

            <div>

                <h2 class="text-2xl font-bold leading-tight text-white md:text-3xl">
                    Cần một phương án PCCC phù hợp
                    <br class="hidden md:block">
                    cho công trình của bạn?
                </h2>

                <p class="mt-3 text-sm leading-7 text-slate-300">
                    Gửi thông tin công trình để đội ngũ kỹ thuật tư vấn giải pháp tối ưu.
                </p>

            </div>


            <div class="flex flex-wrap gap-3">

                <a
                    href="#tu-van"
                    class="button-primary"
                >
                    Nhận tư vấn ngay
                    <span aria-hidden="true">→</span>
                </a>


                @if ($primaryPhone)

                    <a
                        href="tel:{{ preg_replace('/\s+/', '', $primaryPhone) }}"
                        class="inline-flex min-h-12 items-center justify-center rounded-lg border border-white/50 px-6 text-sm font-semibold text-white transition hover:bg-white hover:text-ink"
                    >
                        Gọi hotline
                    </a>

                @endif

            </div>

        </div>

    </section>

@endsection