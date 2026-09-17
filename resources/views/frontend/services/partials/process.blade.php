@if ($processItems !== [])
    <section id="quy-trinh" class="service-process section-space position-relative overflow-hidden dv-services-partials-process__section-1">
        @if ($processBackgroundUrl)
            <img class="service-process__image" src="{{ $processBackgroundUrl }}" alt="" aria-hidden="true" loading="lazy">
        @endif
        <div class="service-process__overlay"></div>
        <div class="site-container w-100 mx-auto position-relative dv-services-partials-process__div-2">
            <header class="mx-auto text-center dv-services-partials-process__element-3">
                <h2 class="display-title dv-services-partials-process__heading-4">Quy trình</h2>
                <p class="mx-auto dv-services-partials-process__copy-5">{{ $service->process_description ?: 'Từng bước được thống nhất rõ ràng để dịch vụ được triển khai đúng mục tiêu và tiến độ.' }}</p>
            </header>
            <ol class="service-process__timeline">
                @foreach ($processItems as $item)
                    <li class="service-process__item">
                        <span class="service-process__marker" aria-hidden="true">{{ $item['step'] ?? $loop->iteration }}</span>
                        <div class="dv-services-partials-process__div-6">
                            <p class="text-primary-soft fw-semibold text-uppercase dv-services-partials-process__copy-7">Bước {{ $item['step'] ?? $loop->iteration }}</p>
                            <h3 class="fw-bold dv-services-partials-process__heading-8">{{ $item['title'] ?? '' }}</h3>
                            @if (filled($item['description'] ?? null))
                                <p class="dv-services-partials-process__copy-9">{{ $item['description'] }}</p>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>
@endif
