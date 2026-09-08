@if ($processItems !== [])
    <section id="quy-trinh" class="service-process section-space relative isolate overflow-hidden bg-ink text-white">
        @if ($processBackgroundUrl)
            <img class="service-process__image" src="{{ $processBackgroundUrl }}" alt="" aria-hidden="true" loading="lazy">
        @endif
        <div class="service-process__overlay"></div>
        <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8 relative">
            <header class="mx-auto max-w-3xl text-center">
                <h2 class="display-title text-3xl leading-tight text-white md:text-4xl">Quy trình</h2>
                <p class="mx-auto mt-5 max-w-2xl text-base leading-8 text-slate-200">{{ $service->process_description ?: 'Từng bước được thống nhất rõ ràng để dịch vụ được triển khai đúng mục tiêu và tiến độ.' }}</p>
            </header>
            <ol class="service-process__timeline">
                @foreach ($processItems as $item)
                    <li class="service-process__item">
                        <span class="service-process__marker" aria-hidden="true">{{ $item['step'] ?? $loop->iteration }}</span>
                        <div class="min-w-0 rounded-[1.25rem] border border-white/15 bg-ink/70 p-5 backdrop-blur-sm md:p-6">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-primary-soft">Bước {{ $item['step'] ?? $loop->iteration }}</p>
                            <h3 class="mt-2 text-xl font-bold leading-tight text-white">{{ $item['title'] ?? '' }}</h3>
                            @if (filled($item['description'] ?? null))
                                <p class="mt-3 text-sm leading-7 text-slate-200">{{ $item['description'] }}</p>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>
@endif
