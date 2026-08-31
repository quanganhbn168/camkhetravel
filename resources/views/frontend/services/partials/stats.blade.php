@if ($statsItems->isNotEmpty())
    <section class="section-space bg-ink text-white">
        <div class="site-shell">
            <header class="mx-auto max-w-3xl text-center">
                <h2 class="display-title text-3xl leading-tight text-white md:text-5xl">{{ $service->stats_title ?: 'Những con số đáng chú ý' }}</h2>
                @if ($service->stats_description)
                    <p class="mt-4 text-base leading-8 text-slate-300">{{ $service->stats_description }}</p>
                @endif
            </header>
            <div class="mx-auto mt-10 grid max-w-5xl gap-5 md:grid-cols-3">
                @foreach ($statsItems as $item)
                    <article class="rounded-[1.5rem] border border-white/15 bg-white/[0.06] p-7 text-center">
                        <p class="text-3xl font-bold text-primary-soft md:text-4xl">{{ $item['label'] }}</p>
                        @if (filled($item['description'] ?? null))
                            <p class="mt-3 text-sm leading-7 text-slate-300">{{ $item['description'] }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
