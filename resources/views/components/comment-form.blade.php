@props(['post' => null, 'service' => null, 'project' => null, 'compact' => false, 'ratingEnabled' => false])

@php
    $routeName = $project ? 'projects.comments.store' : ($service ? 'services.comments.store' : 'comments.store');
    $routeParameters = $project ? ['project' => $project] : ($service ? ['service' => $service] : ['post' => $post]);
@endphp

<form class="{{ $compact ? '' : 'dv-comment-form__element-1 ' }} dv-comment-form__element-2" action="{{ \App\Support\Localization\LocalizedUrl::route($routeName, $routeParameters) }}" method="post">
    @csrf
    <div class="dv-comment-form__div-3">
        <label class="fw-semibold dv-comment-form__element-4" for="comment-author-name">
            Họ tên <span class="dv-comment-form__copy-5">*</span>
            <input class="form-control form-field" id="comment-author-name" name="author_name" type="text" value="{{ old('author_name') }}" autocomplete="name" required maxlength="120">
            @error('author_name')<span class="d-block fw-medium dv-comment-form__copy-6">{{ $message }}</span>@enderror
        </label>
        <label class="fw-semibold dv-comment-form__element-4" for="comment-author-email">
            Email <span class="fw-normal dv-comment-form__copy-7">(không bắt buộc)</span>
            <input class="form-control form-field" id="comment-author-email" name="author_email" type="email" value="{{ old('author_email') }}" autocomplete="email" maxlength="255">
            @error('author_email')<span class="d-block fw-medium dv-comment-form__copy-6">{{ $message }}</span>@enderror
        </label>
        @if ($ratingEnabled)
            <fieldset class="fw-semibold dv-comment-form__element-8">
                <legend>Đánh giá của anh/chị <span class="dv-comment-form__copy-5">*</span></legend>
                <div class="d-flex flex-wrap dv-comment-form__div-9">
                    @for ($rating = 1; $rating <= 5; $rating++)
                        <label class="dv-comment-form__element-10">
                            <input class="dv-choice-control dv-comment-form__element-11" type="radio" name="rating" value="{{ $rating }}" @checked((int) old('rating') === $rating) required>
                            <span class="d-inline-flex align-items-center fw-semibold dv-comment-form__copy-12">
                                {{ $rating }} <span aria-hidden="true">★</span>
                            </span>
                        </label>
                    @endfor
                </div>
                @error('rating')<span class="d-block fw-medium dv-comment-form__copy-6">{{ $message }}</span>@enderror
            </fieldset>
        @endif
        <label class="fw-semibold dv-comment-form__element-8" for="comment-body">
            Bình luận <span class="dv-comment-form__copy-5">*</span>
            <textarea class="form-control form-field dv-comment-form__element-13" id="comment-body" name="body" required maxlength="3000">{{ old('body') }}</textarea>
            @error('body')<span class="d-block fw-medium dv-comment-form__copy-6">{{ $message }}</span>@enderror
        </label>
    </div>
    <div class="d-flex flex-wrap align-items-center justify-content-between dv-comment-form__div-14">
        <p class="dv-comment-form__copy-15">Bình luận sẽ được kiểm duyệt trước khi hiển thị công khai.</p>
        <button class="btn btn-primary button-primary dv-comment-form__action-16" type="submit">Gửi bình luận</button>
    </div>
</form>
