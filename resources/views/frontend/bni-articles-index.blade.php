@extends('layouts.master')

@use(App\Support\Localization\LocalizedUrl)

@section('body_class', 'bni-experience-page bni-articles-page')
@section('main_id', 'bni-articles-main')
@section('main_class', 'bni-experience-main overflow-x-clip')

@section('content')
    @include('frontend.partials.bni-navigation')

    <section class="bni-articles-hero" aria-labelledby="bni-articles-title">
        <div class="site-shell">
            <p class="bni-experience-kicker">CẬP NHẬT TỪ SỰ KIỆN</p>
            <h1 id="bni-articles-title">Tin tức Lễ chuyển giao BNI</h1>
            <p>Tin tức, câu chuyện và hoạt động mới nhất thuộc riêng hệ thống Lễ chuyển giao BNI.</p>
        </div>
    </section>

    <section class="bni-section bni-articles-browser" aria-label="Danh sách tin tức BNI">
        <div class="site-shell">
            <nav class="bni-articles-filter" aria-label="Lọc theo danh mục tin tức">
                <a @class(['is-active' => $selectedCategory === '']) href="{{ LocalizedUrl::route('bni.articles.index') }}">Tất cả</a>
                @foreach ($categories as $category)
                    <a @class(['is-active' => $selectedCategory === $category->slug]) href="{{ LocalizedUrl::route('bni.articles.index', ['danh-muc' => $category->slug]) }}">
                        {{ $category->name }} <span>{{ $category->published_articles_count }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="bni-articles-grid">
                @forelse ($articles as $article)
                    <article class="bni-articles-card">
                        <a class="bni-articles-card__image" href="{{ LocalizedUrl::route('bni.articles.show', ['article' => $article]) }}">
                            @if ($imageUrl = $article->bniMediaUrl('cover'))
                                <img src="{{ $imageUrl }}" alt="{{ $article->title }}" loading="lazy">
                            @else
                                <span><img src="{{ asset('bni-logo-red.svg') }}" alt="BNI"></span>
                            @endif
                        </a>
                        <div class="bni-articles-card__body">
                            <p>{{ $article->categories->pluck('name')->implode(' · ') ?: 'Tin tức BNI' }}</p>
                            <h2><a href="{{ LocalizedUrl::route('bni.articles.show', ['article' => $article]) }}">{{ $article->title }}</a></h2>
                            @if ($article->excerpt)<span>{{ str($article->excerpt)->stripTags()->limit(180) }}</span>@endif
                            <div>
                                @if ($article->published_at)<time datetime="{{ $article->published_at->toDateString() }}">{{ $article->published_at->translatedFormat('d/m/Y') }}</time>@endif
                                <a href="{{ LocalizedUrl::route('bni.articles.show', ['article' => $article]) }}">Đọc bài viết →</a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="bni-gallery-empty">
                        <img src="{{ asset('bni-logo-red.svg') }}" alt="BNI">
                        <h2>Chưa có bài viết trong danh mục này</h2>
                        <p>Bài viết sẽ xuất hiện tại đây sau khi được xuất bản trong quản trị BNI.</p>
                    </div>
                @endforelse
            </div>

            @if ($articles->hasPages())
                <div class="bni-gallery-pagination">{{ $articles->links() }}</div>
            @endif
        </div>
    </section>
@endsection
