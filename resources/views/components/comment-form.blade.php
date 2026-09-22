@props(['post' => null, 'service' => null, 'project' => null, 'compact' => false, 'ratingEnabled' => false])

<form class="{{ $compact ? '' : 'mt-5 ' }} comment-form" action="{{ route($project ? 'projects.comments.store' : ($service ? 'services.comments.store' : 'comments.store'), $project ? ['project' => $project->id] : ($service ? ['service' => $service->id] : ['post' => $post->id])) }}" method="post">
    @csrf
    <div class="row g-3">
        <label class="fw-semibold col-md-6" for="comment-author-name">
            Họ tên <span class="text-danger">*</span>
            <input class="form-control" id="comment-author-name" name="author_name" type="text" value="{{ old('author_name') }}" autocomplete="name" required maxlength="120">
            @error('author_name')<span class="d-block fw-medium text-danger small mt-1">{{ $message }}</span>@enderror
        </label>
        <label class="fw-semibold col-md-6" for="comment-author-email">
            Email <span class="fw-normal text-body-secondary small">(không bắt buộc)</span>
            <input class="form-control" id="comment-author-email" name="author_email" type="email" value="{{ old('author_email') }}" autocomplete="email" maxlength="255">
            @error('author_email')<span class="d-block fw-medium text-danger small mt-1">{{ $message }}</span>@enderror
        </label>
        @if ($ratingEnabled)
            <fieldset class="fw-semibold col-12">
                <legend>Đánh giá của anh/chị <span class="text-danger">*</span></legend>
                <div class="d-flex flex-wrap gap-3">
                    @for ($rating = 1; $rating <= 5; $rating++)
                        <label class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="rating" value="{{ $rating }}" @checked((int) old('rating') === $rating) required>
                            <span class="d-inline-flex align-items-center fw-semibold gap-1">
                                {{ $rating }} <span aria-hidden="true">★</span>
                            </span>
                        </label>
                    @endfor
                </div>
                @error('rating')<span class="d-block fw-medium text-danger small mt-1">{{ $message }}</span>@enderror
            </fieldset>
        @endif
        <label class="fw-semibold col-12" for="comment-body">
            Bình luận <span class="text-danger">*</span>
            <textarea class="form-control comment-form__message" id="comment-body" name="body" required maxlength="3000">{{ old('body') }}</textarea>
            @error('body')<span class="d-block fw-medium text-danger small mt-1">{{ $message }}</span>@enderror
        </label>
    </div>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mt-4">
        <p class="small text-body-secondary mb-0">Bình luận sẽ được kiểm duyệt trước khi hiển thị công khai.</p>
        <button class="btn btn-primary" type="submit">Gửi bình luận</button>
    </div>
</form>
