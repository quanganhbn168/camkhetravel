@use(App\Support\Localization\LocalizedUrl)

@if ($backstageProjects->isNotEmpty())
    <section id="du-an-noi-bat" class="resource-related-section">
        <div class="site-container w-100 mx-auto site-services-partials-projects__div-1">
            <header class="mx-auto text-center site-services-partials-projects__element-2">
                <h2 class="display-title site-services-partials-projects__heading-3">{{ $service->projects_title ?: 'Các dự án nổi bật' }}</h2>
                <p class="mx-auto site-services-partials-projects__copy-4">Những dự án đã được triển khai và gắn với dịch vụ này.</p>
            </header>
            <div class="site-services-partials-projects__div-5">
                @foreach ($backstageProjects->take(3) as $project)
                    <article class="site-hover-group overflow-hidden site-services-partials-projects__article-6">
                        @if ($project->image_url)
                            <a class="service-project-card__media" href="{{ $project->image_url }}" target="_blank" rel="noopener" aria-label="Mở ảnh dự án {{ $project->title }}">
                                <img src="{{ $project->image_url }}" alt="{{ $project->title }}" loading="lazy">
                                <span class="service-project-card__zoom" aria-hidden="true">↗</span>
                            </a>
                        @else
                            <a class="service-project-card__media" href="{{ LocalizedUrl::project($project) }}" aria-label="Xem dự án {{ $project->title }}">
                                <span class="image-placeholder">DV</span>
                            </a>
                        @endif
                        <div class="site-services-partials-projects__div-7">
                            @if ($project->category)
                                <p class="fw-semibold text-uppercase site-services-partials-projects__copy-8">{{ $project->category->name }}</p>
                            @endif
                            <h3 class="fw-bold site-services-partials-projects__heading-9"><a class="site-services-partials-projects__action-10" href="{{ LocalizedUrl::project($project) }}">{{ $project->title }}</a></h3>
                            <div class="d-flex flex-wrap site-services-partials-projects__div-11">
                                @if ($project->client_name)<span>{{ $project->client_name }}</span>@endif
                                @if ($project->completed_at)<span>{{ $project->completed_at->translatedFormat('m/Y') }}</span>@endif
                            </div>
                            <a class="d-inline-flex align-items-center fw-bold site-services-partials-projects__action-12" href="{{ LocalizedUrl::project($project) }}">Xem dự án <span aria-hidden="true">→</span></a>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="text-center site-services-partials-projects__div-13">
                <a class="btn btn-dark button-dark" href="{{ LocalizedUrl::route('projects.index', ['service' => $service->id]) }}">Xem thêm dự án <span aria-hidden="true">→</span></a>
            </div>
        </div>
    </section>
@endif
