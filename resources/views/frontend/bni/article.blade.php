@extends('layouts.master')

@use(App\Support\Localization\LocalizedUrl)
@use(Illuminate\Support\Str)

@section('body_class', 'bni-experience-page')
@section('main_id', 'bni-article-main')
@section('main_class', 'bni-experience-main overflow-x-clip')

@section('content')
    @include('frontend.bni.partials.navigation')

    <article class="bni-article">
        <header class="bni-article__header">@if ($imageUrl)<img src="{{ $imageUrl }}" alt="" aria-hidden="true">@endif<div class="site-container w-full mx-auto max-w-4xl px-4 lg:px-8"><a class="bni-back-link" href="{{ LocalizedUrl::route('bni.articles.index') }}">← Tin tức Lễ chuyển giao</a><p class="bni-experience-kicker">{{ $article->categories->pluck('name')->implode(' · ') ?: ($article->chapter?->short_name ?: 'Tin tức BNI') }}</p><h1>{{ $article->title }}</h1>@if ($article->excerpt)<p>{{ $article->excerpt }}</p>@endif</div></header>
        <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8 bni-article__layout"><div><div class="bni-rich-copy bni-rich-copy--article">{!! $article->body !!}</div><section class="bni-reactions" aria-label="Cảm xúc bài viết"><h2>Gửi cảm xúc</h2>@auth<form method="POST" action="{{ LocalizedUrl::route('bni.articles.reactions.store', ['article' => $article]) }}">@csrf @foreach (['like' => 'Hữu ích', 'love' => 'Yêu thích', 'celebrate' => 'Chúc mừng'] as $reaction => $label)<button type="submit" name="reaction" value="{{ $reaction }}">{{ $label }} <span>{{ $reactionCounts[$reaction] ?? 0 }}</span></button>@endforeach</form>@else<p><a href="{{ LocalizedUrl::route('bni.member.login') }}">Đăng nhập hội viên</a> để gửi cảm xúc và bình luận.</p>@endauth</section><section class="bni-member-comments" id="binh-luan"><h2>Bình luận ({{ $article->approvedComments->count() }})</h2><div class="bni-member-comments__list">@forelse ($article->approvedComments as $comment)<article><span>{{ Str::upper(Str::substr($comment->author_name, 0, 1)) }}</span><div><h3>{{ $comment->author_name }}</h3><p>{{ $comment->body }}</p></div></article>@empty<p class="bni-empty-copy">Chưa có bình luận được duyệt.</p>@endforelse</div>@auth<form class="bni-form" method="POST" action="{{ LocalizedUrl::route('bni.articles.comments.store', ['article' => $article]) }}">@csrf<label for="bni-comment">Bình luận của anh/chị</label><textarea id="bni-comment" name="body" rows="4" required>{{ old('body') }}</textarea><button class="bni-button bni-button--red" type="submit">Gửi bình luận</button></form>@endauth</section></div><aside class="bni-article__aside"><p>Đã đăng</p><strong>{{ $article->published_at?->translatedFormat('d/m/Y') ?: 'Đang cập nhật' }}</strong>@if ($article->chapter)<p>Chapter</p><strong>{{ $article->chapter->name }}</strong>@endif</aside></div>
    </article>
@endsection
