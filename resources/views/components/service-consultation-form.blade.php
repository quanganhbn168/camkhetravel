@props([
    'content',
    'heading' => 'Đăng ký tư vấn',
    'description' => null,
    'buttonLabel' => 'Gửi thông tin đăng ký',
    'blockId' => 'lead-form',
    'successClass' => null,
])

<section class="resource-related-section position-relative overflow-hidden dv-service-consultation-form__section-1" id="tu-van">
    @if ($content->image_url)
        <img class="position-absolute h-100 w-100 object-fit-cover dv-service-consultation-form__media-2" src="{{ $content->image_url }}" alt="" aria-hidden="true">
    @endif
    <div class="position-absolute dv-service-consultation-form__div-3"></div>

    <div class="site-container w-100 mx-auto position-relative dv-service-consultation-form__div-4">
        <aside class="d-flex flex-column dv-service-consultation-form__aside-5">
                <h2 class="display-title text-uppercase dv-service-consultation-form__heading-6">{{ $heading }}</h2>
                <p class="dv-service-consultation-form__copy-7">{{ $description ?: 'Trao đổi nhanh về hạng mục '.mb_strtolower($content->title).' cùng đội ngũ DVTEC.' }}</p>

                <div class="mt-auto dv-service-consultation-form__div-8">
                    <p class="text-primary-soft fw-bold text-uppercase dv-service-consultation-form__copy-9">{{ $website->company_name ?: $website->site_name }}</p>
                    <dl class="d-grid dv-service-consultation-form__dl-10">
                        @if ($website->hotline)<div><dt class="dv-service-consultation-form__dt-11">Điện thoại</dt><dd><a class="hover:text-primary-soft fw-semibold" href="tel:{{ preg_replace('/\s+/', '', $website->hotline) }}">{{ $website->hotline }}</a></dd></div>@endif
                        @if ($website->contact_email)<div><dt class="dv-service-consultation-form__dt-11">Email</dt><dd><a class="dv-service-consultation-form__action-13" href="mailto:{{ $website->contact_email }}">{{ $website->contact_email }}</a></dd></div>@endif
                        @if ($website->address)<div><dt class="dv-service-consultation-form__dt-11">Địa chỉ</dt><dd class="dv-service-consultation-form__dd-14">{{ $website->address }}</dd></div>@endif
                    </dl>
                </div>
        </aside>

        @php
            $successMessage = session('success');
        $legacySuccessClasses = preg_split('/\s+/', trim((string) $successClass), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $successClassNames = array_merge([
            'landing-form-success',
        ], $legacySuccessClasses);
            $successClasses = implode(' ', array_values(array_unique(array_filter($successClassNames))));
        @endphp
        <form class="dv-service-consultation-form__element-15" method="POST" action="{{ \App\Support\Localization\LocalizedUrl::route('contact.store') }}" data-lead-form>
            <p class="{{ $successClasses }} fw-semibold dv-service-consultation-form__copy-16" data-form-success role="status" aria-live="polite" @if (blank($successMessage)) hidden @endif>{{ $successMessage }}</p>
            @csrf
            @if ($content instanceof \App\Models\Service)
                <input type="hidden" name="service_id" value="{{ $content->id }}">
            @elseif ($content instanceof \App\Models\LandingPage)
                <input type="hidden" name="landing_page_id" value="{{ $content->id }}">
                <input type="hidden" name="from_landing_page" value="1">
            @endif
            <input type="hidden" name="return_to" value="{{ request()->getRequestUri() }}#tu-van">

            <div class="dv-service-consultation-form__div-17">
                <label class="fw-semibold dv-service-consultation-form__element-18">Họ và tên<input class="form-control form-field" name="name" value="{{ old('name') }}" autocomplete="name" maxlength="255">@error('name')<span class="d-block fw-medium dv-service-consultation-form__copy-19">{{ $message }}</span>@enderror</label>
                <label class="fw-semibold dv-service-consultation-form__element-18">Số điện thoại <span class="dv-service-consultation-form__copy-20">*</span><input class="form-control form-field" name="phone" value="{{ old('phone') }}" autocomplete="tel" required maxlength="32">@error('phone')<span class="d-block fw-medium dv-service-consultation-form__copy-19">{{ $message }}</span>@enderror</label>
                <label class="fw-semibold dv-service-consultation-form__element-21">Email<input class="form-control form-field" name="email" type="email" value="{{ old('email') }}" autocomplete="email" maxlength="255">@error('email')<span class="d-block fw-medium dv-service-consultation-form__copy-19">{{ $message }}</span>@enderror</label>
                <label class="fw-semibold dv-service-consultation-form__element-21">Nội dung<textarea class="form-control form-field" name="message" rows="6" maxlength="5000">{{ old('message') }}</textarea>@error('message')<span class="d-block fw-medium dv-service-consultation-form__copy-19">{{ $message }}</span>@enderror</label>
            </div>
            <p class="dv-service-consultation-form__copy-22"><span class="dv-service-consultation-form__copy-20">*</span> Thông tin bắt buộc</p>
            <button class="btn btn-primary button-primary dv-service-consultation-form__action-23" type="submit">{{ $buttonLabel }} <span aria-hidden="true">↗</span></button>
        </form>
    </div>
</section>
