@use(App\Support\Localization\LocalizedUrl)

@if ($backstageProjects->isNotEmpty())
    <section id="du-an-noi-bat" class="resource-related-section">
        <div class="site-shell">
            <header class="mx-auto max-w-3xl text-center">
                <h2 class="display-title text-3xl leading-tight md:text-5xl">{{ $service->projects_title ?: 'Các dự án nổi bật' }}</h2>
                <p class="mx-auto mt-4 max-w-2xl text-base leading-8 text-slate-600">Những dự án đã được triển khai và gắn với dịch vụ này.</p>
            </header>
            <div class="mt-10 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($backstageProjects->take(3) as $project)
                    <article class="group overflow-hidden rounded-[1.5rem] border border-slate-200 bg-white shadow-[0_12px_30px_rgba(31,43,37,0.06)] transition duration-300 hover:-translate-y-1 hover:border-primary hover:shadow-[0_20px_42px_rgba(31,43,37,0.12)]">
                        @if ($project->image_url)
                            <a class="service-project-card__media glightbox" href="{{ $project->image_url }}" data-type="image" data-gallery="service-projects-{{ $service->id }}" data-title="{{ $project->title }}" aria-label="Mở ảnh dự án {{ $project->title }}">
                                <img src="{{ $project->image_url }}" alt="{{ $project->title }}" loading="lazy">
                                <span class="service-project-card__zoom" aria-hidden="true">↗</span>
                            </a>
                        @else
                            <a class="service-project-card__media" href="{{ LocalizedUrl::project($project) }}" aria-label="Xem dự án {{ $project->title }}">
                                <span class="image-placeholder">THT</span>
                            </a>
                        @endif
                        <div class="p-6">
                            @if ($project->category)
                                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-primary">{{ $project->category->name }}</p>
                            @endif
                            <h3 class="mt-3 text-xl font-bold leading-tight text-ink"><a class="transition hover:text-accent" href="{{ LocalizedUrl::project($project) }}">{{ $project->title }}</a></h3>
                            <div class="mt-3 flex min-h-5 flex-wrap gap-x-3 gap-y-1 text-xs leading-5 text-slate-400">
                                @if ($project->client_name)<span>{{ $project->client_name }}</span>@endif
                                @if ($project->completed_at)<span>{{ $project->completed_at->translatedFormat('m/Y') }}</span>@endif
                            </div>
                            <a class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-accent" href="{{ LocalizedUrl::project($project) }}">Xem dự án <span aria-hidden="true">→</span></a>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="mt-8 text-center">
                <a class="button-dark" href="{{ LocalizedUrl::route('projects.index', ['service' => $service->id]) }}">Xem thêm dự án <span aria-hidden="true">→</span></a>
            </div>
        </div>
    </section>
@endif
