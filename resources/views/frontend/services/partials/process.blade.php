@if ($processItems !== [])
    <section id="quy-trinh" class="service-process section-space position-relative overflow-hidden">
        @if ($processBackgroundUrl)
            <img class="service-process__image" src="{{ $processBackgroundUrl }}" alt="" aria-hidden="true" loading="lazy">
        @endif
        <div class="service-process__overlay"></div>
        <div class="container position-relative">
            <header class="mx-auto text-center mb-5">
                <h2 class="display-title h2 text-white">Quy trình</h2>
                <p class="mx-auto lead">{{ $service->process_description ?: 'Từng bước được thống nhất theo lịch trình và nhu cầu của chuyến đi.' }}</p>
            </header>
            <ol class="service-process__timeline">
                @foreach ($processItems as $item)
                    <li class="service-process__item">
                        <span class="service-process__marker" aria-hidden="true">{{ $item['step'] ?? $loop->iteration }}</span>
                        <div>
                            <p class="text-primary fw-semibold text-uppercase small">Bước {{ $item['step'] ?? $loop->iteration }}</p>
                            <h3 class="fw-bold h4">{{ $item['title'] ?? '' }}</h3>
                            @if (filled($item['description'] ?? null))
                                <p class="mb-0">{{ $item['description'] }}</p>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>
@endif
