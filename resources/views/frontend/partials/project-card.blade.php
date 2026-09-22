<article class="resource-card">
    <a class="resource-card__media" href="{{ route('projects.show', ['slug' => $project->slug]) }}" aria-label="Xem dự án {{ $project->title }}">
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
        <h3 class="fw-bold h4"><a class="link-body-emphasis text-decoration-none" href="{{ route('projects.show', ['slug' => $project->slug]) }}">{{ $project->title }}</a></h3>
        <div class="d-flex flex-wrap gap-2 small text-body-secondary">
            @if ($project->client_name)<span>{{ $project->client_name }}</span>@endif
            @if ($project->completed_at)<span>{{ $project->completed_at->translatedFormat('m/Y') }}</span>@endif
        </div>
        <a class="resource-card__link" href="{{ route('projects.show', ['slug' => $project->slug]) }}">Xem dự án <span aria-hidden="true">→</span></a>
    </div>
</article>
