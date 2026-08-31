@use(App\Support\Landing\LandingTemplateRegistry)

@php($data = $block['data'])

@if ($block['projects']->isNotEmpty())
    <section class="landing-section landing-projects" id="{{ $block['id'] }}" data-landing-block="projects">
        <div class="landing-shell">
            <header class="landing-section-heading landing-section-heading--split">
                <div>
                    @if (filled($data['eyebrow'] ?? null))<p class="landing-eyebrow">{{ $data['eyebrow'] }}</p>@endif
                    <h2>{{ $data['title'] ?? 'Dự án liên quan' }}</h2>
                    @if (filled($data['description'] ?? null))<p>{{ $data['description'] }}</p>@endif
                </div>
                <a href="{{ \App\Support\Localization\LocalizedUrl::route('projects.index') }}">Xem tất cả <span aria-hidden="true">→</span></a>
            </header>
            <div class="landing-projects__grid">
                @foreach ($block['projects'] as $project)
                    <div data-landing-event="project_click" data-block-id="{{ $block['id'] }}" data-project-id="{{ $project->id }}">
                        @include('frontend.partials.project-card', [
                            'project' => $project,
                            'showVideoCue' => $landingPage->template_key === LandingTemplateRegistry::CORPORATE_FILM && filled($project->video_url),
                        ])
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
