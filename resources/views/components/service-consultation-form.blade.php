@props([
    'content',
    'heading' => 'Đăng ký tư vấn',
    'description' => null,
    'buttonLabel' => 'Gửi thông tin đăng ký',
    'blockId' => 'lead-form',
    'successClass' => null,
])

<section class="resource-related-section position-relative overflow-hidden consultation" id="tu-van">
    @if ($content->image_url)
        <img class="position-absolute h-100 w-100 object-fit-cover consultation__background" src="{{ $content->image_url }}" alt="" aria-hidden="true">
    @endif
    <div class="position-absolute consultation__overlay"></div>

    <div class="container position-relative row-gap-4 consultation__layout">
        <aside class="d-flex flex-column gap-3">
                <h2 class="display-title text-uppercase h2 text-white">{{ $heading }}</h2>
                <p class="lead">{{ $description ?: 'Trao đổi lịch trình cho dịch vụ '.mb_strtolower($content->title).' cùng đội ngũ '.$website->site_name.'.' }}</p>

                <div class="mt-auto pt-4">
                    <p class="text-primary fw-bold text-uppercase small">{{ $website->company_name ?: $website->site_name }}</p>
                    <dl class="d-grid gap-3">
                        @if ($website->hotline)<div><dt class="small text-white-50">Điện thoại</dt><dd><a class="link-light fw-semibold" href="tel:{{ preg_replace('/\s+/', '', $website->hotline) }}">{{ $website->hotline }}</a></dd></div>@endif
                        @if ($website->contact_email)<div><dt class="small text-white-50">Email</dt><dd><a class="link-light" href="mailto:{{ $website->contact_email }}">{{ $website->contact_email }}</a></dd></div>@endif
                        @if ($website->address)<div><dt class="small text-white-50">Địa chỉ</dt><dd class="mb-0">{{ $website->address }}</dd></div>@endif
                    </dl>
                </div>
        </aside>

        <form class="consultation__form" method="POST" action="{{ route('contact.store') }}">
            <p class="contact-form-success {{ trim((string) $successClass) }} fw-semibold alert alert-success" data-form-success role="status" aria-live="polite" @if (blank(session('success'))) hidden @endif>{{ session('success') }}</p>
            @csrf
            @if ($content instanceof \App\Models\Service)
                <input type="hidden" name="service_id" value="{{ $content->id }}">
            @endif
            <input type="hidden" name="return_to" value="{{ request()->getRequestUri() }}#tu-van">

            <div class="row g-3">
                <label class="fw-semibold col-md-6">Họ và tên<input class="form-control" name="name" value="{{ old('name') }}" autocomplete="name" maxlength="255">@error('name')<span class="d-block fw-medium small text-danger mt-1">{{ $message }}</span>@enderror</label>
                <label class="fw-semibold col-md-6">Số điện thoại <span class="text-danger">*</span><input class="form-control" name="phone" value="{{ old('phone') }}" autocomplete="tel" required maxlength="32">@error('phone')<span class="d-block fw-medium small text-danger mt-1">{{ $message }}</span>@enderror</label>
                <label class="fw-semibold col-12">Email<input class="form-control" name="email" type="email" value="{{ old('email') }}" autocomplete="email" maxlength="255">@error('email')<span class="d-block fw-medium small text-danger mt-1">{{ $message }}</span>@enderror</label>
                <label class="fw-semibold col-12">Nội dung<textarea class="form-control" name="message" rows="6" maxlength="5000">{{ old('message') }}</textarea>@error('message')<span class="d-block fw-medium small text-danger mt-1">{{ $message }}</span>@enderror</label>
            </div>
            <p class="small mt-3 text-body-secondary"><span class="text-danger">*</span> Thông tin bắt buộc</p>
            <button class="btn btn-primary" type="submit">{{ $buttonLabel }} <span aria-hidden="true">↗</span></button>
        </form>
    </div>
</section>
