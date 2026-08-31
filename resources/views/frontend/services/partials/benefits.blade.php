@if ($benefitItems !== [])
    <section id="loi-ich" class="section-space bg-mist/55">
        <div class="site-shell">
            <header class="mx-auto max-w-3xl text-center">
                <h2 class="display-title text-3xl leading-tight md:text-5xl">{{ $service->benefit_title ?: 'Lợi ích của '.$service->title }}</h2>
                @if ($service->benefit_description)
                    <p class="mx-auto mt-5 max-w-2xl text-base leading-8 text-slate-600">{{ $service->benefit_description }}</p>
                @endif
            </header>
            <div class="mt-10 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($benefitItems as $item)
                    <article class="overflow-hidden rounded-[1.5rem] border border-slate-200 bg-white">
                        @if ($item['media_url'] ?? null)
                            <img class="aspect-[4/3] w-full object-cover" src="{{ $item['media_url'] }}" alt="{{ $item['title'] ?? $service->title }}" loading="lazy">
                        @endif
                        <div class="p-6">
                            <h3 class="text-xl font-bold leading-tight text-ink">{{ $item['title'] ?? '' }}</h3>
                            @if (filled($item['description'] ?? null))
                                <p class="mt-3 text-sm leading-7 text-slate-600">{{ $item['description'] }}</p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
