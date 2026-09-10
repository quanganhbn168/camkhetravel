@php($data = $block['data'])

<section class="landing-cta" id="{{ $block['id'] }}" data-landing-block="cta">
    <div class="landing-shell landing-cta__inner">
        <div>
            @if (filled($data['eyebrow'] ?? null))<p class="landing-eyebrow">{{ $data['eyebrow'] }}</p>@endif
            <h2>{{ $data['title'] ?? 'Sẵn sàng đồng hành cùng DVTEC?' }}</h2>
            @if (filled($data['description'] ?? null))<p>{{ $data['description'] }}</p>@endif
        </div>
        <a class="landing-button landing-button--light" href="{{ $data['cta_url'] ?? '#tu-van' }}">{{ $data['cta_label'] ?? 'Đăng ký ngay' }} <span aria-hidden="true">↗</span></a>
    </div>
</section>
