@use(App\Support\Localization\LocalizedUrl)

<article class="resource-card group">
    <a class="resource-card__media" href="{{ LocalizedUrl::project($project) }}" aria-label="Xem dự án {{ $project->title }}">
        @if ($project->image_url)
            <img src="{{ $project->image_url }}" alt="{{ $project->title }}" loading="lazy">
        @else
            <span class="image-placeholder">THT</span>
        @endif
        @if ($project->category)
            <span class="resource-card__badge">{{ $project->category->name }}</span>
        @endif
        @if ($showVideoCue ?? false)
            <span class="resource-card__video-cue" aria-hidden="true">▶</span>
        @endif
    </a>
    <div class="resource-card__body">
        <h3 class="text-lg leading-6 font-bold text-ink md:text-xl"><a class="hover:text-accent" href="{{ LocalizedUrl::project($project) }}">{{ $project->title }}</a></h3>
        <div class="mt-3 flex min-h-5 flex-wrap gap-x-3 gap-y-1 text-xs leading-5 text-slate-400">
            @if ($project->client_name)<span>{{ $project->client_name }}</span>@endif
            @if ($project->completed_at)<span>{{ $project->completed_at->translatedFormat('m/Y') }}</span>@endif
        </div>
        <a class="resource-card__link" href="{{ LocalizedUrl::project($project) }}">Xem dự án <span aria-hidden="true">→</span></a>
    </div>
</article>
