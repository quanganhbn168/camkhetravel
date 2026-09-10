@extends('layouts.master')

@use(App\Support\Localization\LocalizedUrl)
@use(Illuminate\Support\Str)

@section('body_class', 'bni-experience-page')
@section('main_id', 'bni-article-main')
@section('main_class', 'bni-experience-main overflow-x-clip')

@section('content')
    @include('frontend.bni.partials.navigation')

    <article class="bni-article">
        <header class="bni-article__header">
            @if ($imageUrl)
                <img src="{{ $imageUrl }}" alt="" aria-hidden="true" loading="eager">
            @endif

            <div class="site-container w-full mx-auto max-w-4xl px-4 lg:px-8">
                <a class="bni-back-link" href="{{ LocalizedUrl::route('bni.articles.index') }}">← Tin tức Lễ chuyển giao</a>
                <div class="bni-article__header-meta">
                    <p class="bni-experience-kicker">{{ $article->categories->pluck('name')->implode(' · ') ?: ($article->chapter?->short_name ?: 'Tin tức BNI') }}</p>
                    @if ($article->published_at)
                        <time datetime="{{ $article->published_at->toDateString() }}">{{ $article->published_at->translatedFormat('d/m/Y') }}</time>
                    @endif
                </div>
                <h1>{{ $article->title }}</h1>
                @if ($article->excerpt)
                    <p>{{ $article->excerpt }}</p>
                @endif
            </div>
        </header>

        <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8 bni-article__layout">
            <div class="bni-article__content">
                <div class="bni-rich-copy bni-rich-copy--article">{!! $article->body !!}</div>

                <section class="bni-reactions" aria-labelledby="bni-reactions-title">
                    <div class="bni-reactions__heading">
                        <div>
                            <p class="bni-article__section-kicker">Tương tác bài viết</p>
                            <h2 id="bni-reactions-title">Gửi cảm xúc</h2>
                        </div>
                    </div>

                    @auth
                        <form method="POST" action="{{ LocalizedUrl::route('bni.articles.reactions.store', ['article' => $article]) }}">
                            @csrf
                            @foreach (['like' => 'Hữu ích', 'love' => 'Yêu thích', 'celebrate' => 'Chúc mừng'] as $reaction => $label)
                                <button type="submit" name="reaction" value="{{ $reaction }}">
                                    <span class="bni-reactions__label">{{ $label }}</span>
                                    <span class="bni-reactions__count">{{ $reactionCounts[$reaction] ?? 0 }}</span>
                                </button>
                            @endforeach
                        </form>
                    @else
                        <p class="bni-reactions__login-copy"><a href="{{ LocalizedUrl::route('bni.member.login') }}">Đăng nhập hội viên</a> để gửi cảm xúc và bình luận.</p>
                    @endauth
                </section>

                <section class="bni-member-comments bni-article__comments" id="binh-luan" aria-labelledby="bni-comments-title">
                    <div class="bni-member-comments__heading">
                        <div>
                            <p class="bni-article__section-kicker">Thảo luận bài viết</p>
                            <h2 id="bni-comments-title">Bình luận <span>({{ $article->approvedComments->count() }})</span></h2>
                        </div>
                        <p class="bni-member-comments__description">Chia sẻ góc nhìn của anh/chị cùng cộng đồng BNI.</p>
                    </div>

                    <div class="bni-member-comments__list">
                        @forelse ($article->approvedComments as $comment)
                            <article class="bni-member-comments__item">
                                <span class="bni-member-comments__avatar" aria-hidden="true">{{ Str::upper(Str::substr($comment->author_name, 0, 1)) }}</span>
                                <div class="bni-member-comments__bubble">
                                    <div class="bni-member-comments__meta">
                                        <h3>{{ $comment->author_name }}</h3>
                                        @if ($comment->created_at)
                                            <time datetime="{{ $comment->created_at->toDateString() }}">{{ $comment->created_at->translatedFormat('d/m/Y') }}</time>
                                        @endif
                                    </div>
                                    <p>{{ $comment->body }}</p>
                                </div>
                            </article>
                        @empty
                            <p class="bni-empty-copy bni-member-comments__empty">Chưa có bình luận được duyệt.</p>
                        @endforelse
                    </div>

                    @auth
                        @if (session('success'))
                            <p class="bni-member-comments__notice" role="status">{{ session('success') }}</p>
                        @endif

                        <form class="bni-form bni-comment-form" method="POST" action="{{ LocalizedUrl::route('bni.articles.comments.store', ['article' => $article]) }}">
                            @csrf
                            <div class="bni-comment-form__heading">
                                <h3>Để lại bình luận</h3>
                                <p>Bình luận sẽ hiển thị sau khi được Ban quản trị duyệt.</p>
                            </div>
                            <div class="bni-comment-form__field">
                                <label for="bni-comment">Bình luận của anh/chị</label>
                                <textarea id="bni-comment" name="body" rows="5" maxlength="3000" placeholder="Chia sẻ cảm nhận của anh/chị..." required>{{ old('body') }}</textarea>
                                @error('body')
                                    <p class="bni-form-error" role="alert">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="bni-comment-form__footer">
                                <p class="bni-comment-form__identity">Đang đăng nhập với <strong>{{ auth()->user()->name }}</strong></p>
                                <button class="bni-button bni-button--red" type="submit">Gửi bình luận</button>
                            </div>
                        </form>
                    @else
                        <div class="bni-member-comments__login">
                            <p>Đăng nhập hội viên để để lại bình luận.</p>
                            <a class="bni-button bni-button--dark" href="{{ LocalizedUrl::route('bni.member.login') }}">Đăng nhập hội viên</a>
                        </div>
                    @endauth
                </section>
            </div>

            <aside class="bni-article__aside">
                <div class="bni-article__aside-card">
                    <p class="bni-article__aside-label">Thông tin bài viết</p>
                    <dl>
                        <div>
                            <dt>Đã đăng</dt>
                            <dd>{{ $article->published_at?->translatedFormat('d/m/Y') ?: 'Đang cập nhật' }}</dd>
                        </div>
                        @if ($article->chapter)
                            <div>
                                <dt>Chapter</dt>
                                <dd>{{ $article->chapter->name }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
                <a class="bni-article__aside-link" href="#binh-luan">Xem bình luận <span aria-hidden="true">↘</span></a>
            </aside>
        </div>
    </article>
@endsection
