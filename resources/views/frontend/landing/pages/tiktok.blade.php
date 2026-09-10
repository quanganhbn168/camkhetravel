@use(App\Support\Localization\LocalizedUrl)

<div class="tt-page">
    <section class="tt-section tt-hero" id="hero-section">
        <x-landing.container class="tt-hero__grid">
            <div>
                <h1>@include('frontend.landing.parts.tiktok.highlighted-title', ['title' => $content['hero']['title'], 'pink' => 'Xây Kênh', 'cyan' => 'TikTok'])</h1>
                <p class="tt-intro">{{ $content['hero']['description'] }}</p>
                <div class="tt-actions">
                    <a class="tt-button" href="tel:{{ $contact['hotline_1'] }}">Gọi Hotline {{ $contact['hotline_display'] }}</a>
                    <a class="tt-button tt-button--outline" href="{{ $zaloUrl }}" target="_blank" rel="noopener noreferrer">Chat Zalo ↗</a>
                </div>
            </div>
            <div class="tt-hero__visual"><img src="{{ $content['hero']['image'] }}" alt="Đội ngũ xây kênh TikTok THT Media" fetchpriority="high"><span class="tt-orb tt-orb--pink" aria-hidden="true"></span><span class="tt-orb tt-orb--cyan" aria-hidden="true"></span><span class="tt-orb tt-orb--white" aria-hidden="true"></span></div>
        </x-landing.container>
    </section>

    @if ($content['promotion']['enabled'] ?? false)
        <section class="tt-section tt-promotion" id="uu-dai-tiktok">
            <x-landing.container>
                <header class="tt-section-heading"><h2>@include('frontend.landing.parts.tiktok.highlighted-title', ['title' => $content['promotion']['title'], 'pink' => 'Tháng 10', 'cyan' => '6 Năm'])</h2><p>{{ $content['promotion']['description'] }}</p></header>
                <div class="tt-promotion-grid">
                    @foreach ($content['promotion']['items'] as $item)
                        <article class="tt-card"><h3>{{ $item['title'] }}</h3><p>{{ $item['description'] }}</p></article>
                    @endforeach
                </div>
                <div class="tt-actions justify-center"><a class="tt-button" href="#lien-he">Nhận tư vấn miễn phí</a><a class="tt-button tt-button--outline" href="{{ $zaloUrl }}" target="_blank" rel="noopener noreferrer">Tư vấn Zalo ↗</a></div>
            </x-landing.container>
        </section>
    @endif

    <section class="tt-section" id="du-an-tiktok">
        <x-landing.container>
            <header class="tt-section-heading"><h2>{{ $content['projects']['title'] }}</h2></header>
            <div class="tt-carousel" data-tt-carousel="projects">
                <div class="swiper"><div class="swiper-wrapper">
                    @foreach ($content['projects']['items'] as $project)
                        <article class="swiper-slide"><a class="tt-project" href="{{ $project['url'] }}" target="_blank" rel="noopener noreferrer"><img src="{{ $project['image'] }}" alt="{{ $project['alt'] ?? $project['title'] }}" loading="lazy"><h3>{{ $project['title'] }}</h3></a></article>
                    @endforeach
                </div></div>
                @include('frontend.landing.parts.tiktok.carousel-controls', ['label' => 'Dự án'])
            </div>
        </x-landing.container>
    </section>

    <section class="tt-section" id="video-da-trien-khai">
        <x-landing.container>
            <header class="tt-section-heading"><h2>{{ $content['showreel']['title'] }}</h2></header>
            <div class="tt-carousel" data-tt-carousel="videos">
                <div class="swiper"><div class="swiper-wrapper">
                    @foreach ($content['showreel']['videos'] as $video)
                        <div class="swiper-slide">@include('frontend.landing.parts.tiktok.video-card', ['gallery' => 'tiktok-showreel'])</div>
                    @endforeach
                </div></div>
                @include('frontend.landing.parts.tiktok.carousel-controls', ['label' => 'Video đã triển khai'])
            </div>
        </x-landing.container>
    </section>

    <section class="tt-section" id="video-mau" x-data="{ category: 0, tier: 'basic' }">
        <x-landing.container>
            <header class="tt-section-heading"><h2>{{ $content['samples']['title'] }}</h2></header>
            <div class="tt-filters" role="group" aria-label="Danh mục video mẫu">
                @foreach ($content['samples']['categories'] as $category)
                    <button type="button" :aria-pressed="category === {{ $loop->index }}" @click="category = {{ $loop->index }}; tier = 'basic'">{{ $category['name'] }}</button>
                @endforeach
            </div>
            @foreach ($content['samples']['categories'] as $category)
                <div class="tt-category-panel" x-show="category === {{ $loop->index }}" @if (!$loop->first) x-cloak @endif>
                    <div class="tt-filters tt-filters--tiers" role="group" aria-label="Gói video {{ $category['name'] }}">
                        @foreach ($category['tiers'] as $tier)
                            <button type="button" :aria-pressed="tier === '{{ $tier['key'] }}'" @click="tier = '{{ $tier['key'] }}'">{{ $tier['label'] }}</button>
                        @endforeach
                    </div>
                    @foreach ($category['tiers'] as $tier)
                        <div class="tt-video-grid" x-show="tier === '{{ $tier['key'] }}'" @if (!$loop->first) x-cloak @endif>
                            @forelse ($tier['videos'] as $video)
                                @include('frontend.landing.parts.tiktok.video-card', ['gallery' => 'tiktok-'.$category['slug'].'-'.$tier['key']])
                            @empty
                                <p class="tt-empty">Video mẫu cho gói này đang được cập nhật.</p>
                            @endforelse
                        </div>
                    @endforeach
                </div>
            @endforeach
        </x-landing.container>
    </section>

    <section class="tt-section" id="ve-chung-toi">
        <x-landing.container>
            <div class="tt-about-grid"><div><h2>{{ $content['about']['title'] }}</h2><p class="tt-intro">{{ $content['about']['description'] }}</p></div><div class="tt-about-visual"><img class="tt-about-image" src="{{ $content['about']['image'] }}" alt="Đội ngũ THT Media" loading="lazy"></div></div>
            <h3 class="tt-features-title">{{ $content['about']['features_title'] }}</h3>
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($content['about']['features'] as $feature)
                    <article class="tt-feature"><img src="{{ $feature['image'] }}" alt="{{ $feature['title'] }}" loading="lazy"><div><h4>{{ $feature['title'] }}</h4><p>{{ $feature['description'] }}</p></div></article>
                @endforeach
            </div>
        </x-landing.container>
    </section>

    <section class="tt-section" id="khach-hang-tiktok">
        <x-landing.container>
            <header class="tt-section-heading"><h2>{{ $content['testimonials']['title'] }}</h2></header>
            <div class="tt-testimonial-grid">
                @foreach ($content['testimonials']['items'] as $item)
                    <figure class="tt-card tt-testimonial"><img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" loading="lazy"><figcaption>{{ $item['name'] }}</figcaption><blockquote>{{ $item['quote'] }}</blockquote></figure>
                @endforeach
            </div>
        </x-landing.container>
    </section>

    <section class="tt-section" id="bang-gia-tiktok" x-data="{ camera: false }">
        <x-landing.container>
            <header class="tt-section-heading"><h2>{{ $content['pricing']['title'] }}</h2></header>
            <div class="tt-filters tt-price-toggle" role="group" aria-label="Thiết bị quay"><button type="button" :aria-pressed="!camera" @click="camera = false">Quay bằng Điện thoại</button><button type="button" :aria-pressed="camera" @click="camera = true">Quay bằng Máy quay</button></div>
            <div class="tt-price-table" aria-live="polite">
                <div class="tt-price-head" aria-hidden="true"><span>Gói dịch vụ</span><span>Gói 10 VIDEO</span><span>Gói 30 VIDEO</span><span>Gói 60 VIDEO</span></div>
                @foreach ($content['pricing']['rows'] as $row)
                    <article class="tt-price-row"><div class="tt-price-service"><h3>{{ $row['name'] }}</h3><ul>@foreach ($row['features'] as $feature)<li>{{ $feature }}</li>@endforeach</ul></div>
                        @foreach ($row['packages'] as $package)
                            <div class="tt-price-package"><span class="tt-price-quantity">Gói {{ $package['quantity'] }} video</span><strong x-show="!camera">{{ $package['phone_price'] }}</strong><strong x-show="camera" x-cloak>{{ $package['camera_price'] }}</strong><small x-show="!camera">({{ $package['phone_unit'] }} đ/video)</small><small x-show="camera" x-cloak>({{ $package['camera_unit'] }} đ/video)</small><p>{{ $package['note'] }}</p></div>
                        @endforeach
                    </article>
                @endforeach
            </div>
            <div class="tt-actions justify-center"><a class="tt-button" href="#lien-he">Tư vấn ngay</a></div>
        </x-landing.container>
    </section>

    <section class="tt-section tt-contact" id="lien-he">
        <x-landing.container>
            <div class="tt-about-grid"><div><h2>{{ $content['contact_section']['title'] }}</h2><p class="tt-intro">{{ $content['contact_section']['description'] }}</p><div class="tt-actions"><a class="tt-button" href="tel:{{ $contact['hotline_1'] }}">{{ $contact['hotline_display'] }}</a><a class="tt-button tt-button--outline" href="{{ $zaloUrl }}" target="_blank" rel="noopener noreferrer">Chat Zalo ↗</a></div></div>
                <form class="tt-lead-form" action="{{ LocalizedUrl::route('contact.store') }}" method="POST">
                    <x-landing.lead-fields :landing-page="$landingPage ?? null" :service="$service ?? null" block-id="tiktok-contact" return-anchor="lien-he" />
                    @if(session('success'))<p class="tt-form-message" role="status">{{ session('success') }}</p>@endif
                    @if($errors->any())<ul class="tt-form-errors" role="alert">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>@endif
                    <label>Họ và tên<input name="name" value="{{ old('name') }}" autocomplete="name" required></label>
                    <label>Số điện thoại<input name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel" required></label>
                    <label>Nhu cầu xây kênh<textarea name="message" rows="4" maxlength="5000">{{ old('message') }}</textarea></label>
                    <button class="tt-button" type="submit">Nhận tư vấn miễn phí →</button>
                </form>
            </div>
        </x-landing.container>
    </section>
</div>
