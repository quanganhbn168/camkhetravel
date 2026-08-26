@props(['post' => null, 'landing' => null, 'project' => null, 'compact' => false, 'ratingEnabled' => false])

@php
    $routeName = $project ? 'projects.comments.store' : ($landing ? 'landings.comments.store' : 'comments.store');
    $routeParameters = $project ? ['project' => $project] : ($landing ? ['landing' => $landing] : ['post' => $post]);
@endphp

<form class="{{ $compact ? '' : 'mt-8 ' }}rounded-[1.5rem] border border-slate-200 bg-white p-6 shadow-sm md:p-8" action="{{ \App\Support\Localization\LocalizedUrl::route($routeName, $routeParameters) }}" method="post">
    @csrf
    <div class="grid gap-5 md:grid-cols-2">
        <label class="text-sm font-semibold text-ink" for="comment-author-name">
            Họ tên <span class="text-primary">*</span>
            <input class="form-field" id="comment-author-name" name="author_name" type="text" value="{{ old('author_name') }}" autocomplete="name" required maxlength="120">
            @error('author_name')<span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span>@enderror
        </label>
        <label class="text-sm font-semibold text-ink" for="comment-author-email">
            Email <span class="text-xs font-normal text-slate-400">(không bắt buộc)</span>
            <input class="form-field" id="comment-author-email" name="author_email" type="email" value="{{ old('author_email') }}" autocomplete="email" maxlength="255">
            @error('author_email')<span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span>@enderror
        </label>
        @if ($ratingEnabled)
            <fieldset class="text-sm font-semibold text-ink md:col-span-2">
                <legend>Đánh giá của anh/chị <span class="text-primary">*</span></legend>
                <div class="mt-3 flex flex-wrap gap-2">
                    @for ($rating = 1; $rating <= 5; $rating++)
                        <label class="cursor-pointer">
                            <input class="peer sr-only" type="radio" name="rating" value="{{ $rating }}" @checked((int) old('rating') === $rating) required>
                            <span class="inline-flex min-h-10 items-center gap-1 rounded-full border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-500 transition peer-checked:border-primary peer-checked:bg-primary peer-checked:text-white hover:border-primary">
                                {{ $rating }} <span aria-hidden="true">★</span>
                            </span>
                        </label>
                    @endfor
                </div>
                @error('rating')<span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span>@enderror
            </fieldset>
        @endif
        <label class="text-sm font-semibold text-ink md:col-span-2" for="comment-body">
            Bình luận <span class="text-primary">*</span>
            <textarea class="form-field min-h-32 resize-y" id="comment-body" name="body" required maxlength="3000">{{ old('body') }}</textarea>
            @error('body')<span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span>@enderror
        </label>
    </div>
    <div class="mt-6 flex flex-wrap items-center justify-between gap-4">
        <p class="text-xs leading-5 text-slate-500">Bình luận sẽ được kiểm duyệt trước khi hiển thị công khai.</p>
        <button class="button-primary min-h-10 px-5 py-2.5 text-sm" type="submit">Gửi bình luận</button>
    </div>
</form>
