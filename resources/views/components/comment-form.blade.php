@props(['post' => null, 'service' => null, 'compact' => false, 'ratingEnabled' => false, 'buttonLabel' => 'Gửi bình luận'])

<form class="{{ $compact ? '' : 'mt-5 ' }} comment-form" action="{{ $service ? route('services.comments.store', ['service' => $service->id]) : route('comments.store', ['post' => $post->id]) }}" method="post">
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
                {{-- Sao xếp ngược 5→1 để CSS tô sáng ngôi sao được chọn và các sao bên trái nó. --}}
                <div class="star-picker">
                    @for ($rating = 5; $rating >= 1; $rating--)
                        <input class="star-picker__input" type="radio" id="comment-rating-{{ $rating }}" name="rating" value="{{ $rating }}" @checked((int) old('rating') === $rating) required>
                        <label class="star-picker__star" for="comment-rating-{{ $rating }}" title="{{ $rating }} sao"><span class="visually-hidden">{{ $rating }} sao</span><span aria-hidden="true">★</span></label>
                    @endfor
                </div>
                @error('rating')<span class="d-block fw-medium text-danger small mt-1">{{ $message }}</span>@enderror
            </fieldset>
        @endif
        <label class="fw-semibold col-12" for="comment-body">
            {{ $ratingEnabled ? 'Nội dung đánh giá' : 'Bình luận' }} <span class="text-danger">*</span>
            <textarea class="form-control comment-form__message" id="comment-body" name="body" required maxlength="3000">{{ old('body') }}</textarea>
            @error('body')<span class="d-block fw-medium text-danger small mt-1">{{ $message }}</span>@enderror
        </label>
    </div>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mt-4">
        <p class="small text-body-secondary mb-0">{{ $ratingEnabled ? 'Đánh giá' : 'Bình luận' }} sẽ được kiểm duyệt trước khi hiển thị công khai.</p>
        <button class="btn btn-primary" type="submit">{{ $buttonLabel }}</button>
    </div>
</form>
