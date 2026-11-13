@php
    $words = preg_split('/\s+/u', trim((string) $comment->author_name)) ?: [];
    $initials = mb_strtoupper(collect([reset($words), count($words) > 1 ? end($words) : null])->filter()->map(fn ($word) => mb_substr($word, 0, 1))->implode(''));
@endphp
<article class="review-card">
    <header class="review-card__head">
        <span class="review-card__avatar" aria-hidden="true">{{ $initials ?: '?' }}</span>
        <div class="review-card__meta">
            <h3 class="review-card__name">{{ $comment->author_name }}</h3>
            @if ($comment->approved_at)
                <time class="review-card__date" datetime="{{ $comment->approved_at->toDateString() }}">{{ $comment->approved_at->translatedFormat('d/m/Y') }}</time>
            @endif
        </div>
        @if ($comment->rating)
            <p class="review-stars mb-0 ms-auto" role="img" aria-label="{{ $comment->rating }} trên 5 sao">
                @for ($star = 1; $star <= 5; $star++)
                    <span @class(['is-on' => $star <= $comment->rating]) aria-hidden="true">★</span>
                @endfor
            </p>
        @endif
    </header>
    <p class="review-card__body">{{ $comment->body }}</p>
</article>
