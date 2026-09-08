@php($data = $block['data'])

@if ($block['posts']->isNotEmpty())
    <section class="landing-section landing-posts" id="{{ $block['id'] }}" data-landing-block="posts">
        <div class="landing-shell">
            <header class="landing-section-heading">
                @if (filled($data['eyebrow'] ?? null))<p class="landing-eyebrow">{{ $data['eyebrow'] }}</p>@endif
                <h2>{{ $data['title'] ?? 'Bài viết liên quan' }}</h2>
                @if (filled($data['description'] ?? null))<p>{{ $data['description'] }}</p>@endif
            </header>
            <div class="landing-projects__grid">
                @foreach ($block['posts'] as $post)
                    @include('frontend.partials.post-card', ['post' => $post])
                @endforeach
            </div>
        </div>
    </section>
@endif
