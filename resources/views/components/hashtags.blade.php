@props(['tags'])
{{-- Thẻ hiển thị dạng hashtag; bấm vào mở trang tìm kiếm (trang tìm kiếm noindex nên không sinh trang mỏng cho Google). --}}
@php
    $visibleTags = collect($tags)->filter(fn ($tag) => $tag->is_active ?? true)->values();
@endphp
@if ($visibleTags->isNotEmpty())
    <ul {{ $attributes->class('hashtags') }} aria-label="Thẻ">
        @foreach ($visibleTags as $tag)
            <li><a class="hashtag" href="{{ route('search', ['q' => $tag->name]) }}" rel="nofollow">#{{ $tag->name }}</a></li>
        @endforeach
    </ul>
@endif
