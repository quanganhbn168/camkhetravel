@use(App\Support\Localization\LocalizedUrl)

<article class="resource-card site-hover-group">
    <a class="resource-card__media" href="{{ LocalizedUrl::project($project) }}" aria-label="Xem dự án {{ $project->title }}">
        @if ($project->image_url ?: ($defaultBannerUrl ?? null))
            <img src="{{ $project->image_url ?: $defaultBannerUrl }}" alt="{{ $project->title }}" loading="lazy">
        @else
            <span class="image-placeholder">DV</span>
        @endif
        @if ($project->category)
            <span class="resource-card__badge">{{ $project->category->name }}</span>
        @endif
        @if ($showVideoCue ?? false)
            <span class="resource-card__video-cue" aria-hidden="true">▶</span>
        @endif
    </a>
    <div class="resource-card__body">
        <h3 class="fw-bold site-partials-project-card__heading-1"><a class="site-partials-project-card__action-2" href="{{ LocalizedUrl::project($project) }}">{{ $project->title }}</a></h3>
        <div class="d-flex flex-wrap site-partials-project-card__div-3">
            @if ($project->client_name)<span>{{ $project->client_name }}</span>@endif
            @if ($project->completed_at)<span>{{ $project->completed_at->translatedFormat('m/Y') }}</span>@endif
        </div>
        <a class="resource-card__link" href="{{ LocalizedUrl::project($project) }}">Xem dự án <span aria-hidden="true">→</span></a>
    </div>
</article>
