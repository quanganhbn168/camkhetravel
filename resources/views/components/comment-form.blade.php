@props(['post', 'compact' => false])

<form class="{{ $compact ? '' : 'mt-8 ' }}rounded-[1.5rem] border border-slate-200 bg-white p-6 shadow-sm md:p-8" action="{{ \App\Support\Localization\LocalizedUrl::route('comments.store', ['post' => $post]) }}" method="post">
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
