@props(['service'])

<section class="resource-related-section relative isolate overflow-hidden bg-ink" id="tu-van">
    @if ($service->image_url)
        <img class="absolute inset-0 -z-20 h-full w-full object-cover" src="{{ $service->image_url }}" alt="" aria-hidden="true">
    @endif
    <div class="absolute inset-0 -z-10 bg-[linear-gradient(115deg,color-mix(in_srgb,var(--site-color-ink)_93%,transparent),color-mix(in_srgb,var(--site-color-green-dark)_78%,transparent))]"></div>

    <div class="site-shell relative grid gap-8 lg:grid-cols-[minmax(0,.85fr)_minmax(0,1.15fr)] lg:items-stretch">
        <aside class="flex min-h-[29rem] max-w-sm flex-col px-1 py-9 text-white md:py-12">
                <h2 class="display-title text-3xl leading-tight text-white uppercase md:text-4xl">Đăng ký tư vấn</h2>
                <p class="mt-4 text-sm leading-7 text-slate-200">Trao đổi nhanh về hạng mục {{ mb_strtolower($service->title) }} cùng đội ngũ THT Media.</p>

                <div class="mt-auto border-t border-white/20 pt-6">
                    <p class="text-xs font-bold tracking-[0.14em] text-primary-soft uppercase">{{ $website->company_name ?: $website->site_name }}</p>
                    <dl class="mt-4 grid gap-3 text-sm">
                        @if ($website->hotline)<div><dt class="sr-only">Điện thoại</dt><dd><a class="font-semibold hover:text-primary-soft" href="tel:{{ preg_replace('/\s+/', '', $website->hotline) }}">{{ $website->hotline }}</a></dd></div>@endif
                        @if ($website->contact_email)<div><dt class="sr-only">Email</dt><dd><a class="text-slate-200 hover:text-white" href="mailto:{{ $website->contact_email }}">{{ $website->contact_email }}</a></dd></div>@endif
                        @if ($website->address)<div><dt class="sr-only">Địa chỉ</dt><dd class="leading-6 text-slate-300">{{ $website->address }}</dd></div>@endif
                    </dl>
                </div>
        </aside>

        <form class="rounded-[2rem] bg-white p-7 md:p-10" method="POST" action="{{ \App\Support\Localization\LocalizedUrl::route('contact.store') }}">
            @csrf
            <input type="hidden" name="landing_id" value="{{ $service->id }}">
            <input type="hidden" name="from_landing" value="1">
            <input type="hidden" name="return_to" value="{{ request()->getRequestUri() }}#tu-van">

            <div class="grid gap-5 md:grid-cols-2">
                <label class="text-sm font-semibold text-ink">Họ và tên<input class="form-field" name="name" value="{{ old('name') }}" autocomplete="name" maxlength="255">@error('name')<span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span>@enderror</label>
                <label class="text-sm font-semibold text-ink">Số điện thoại <span class="text-primary">*</span><input class="form-field" name="phone" value="{{ old('phone') }}" autocomplete="tel" required maxlength="32">@error('phone')<span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span>@enderror</label>
                <label class="text-sm font-semibold text-ink md:col-span-2">Email<input class="form-field" name="email" type="email" value="{{ old('email') }}" autocomplete="email" maxlength="255">@error('email')<span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span>@enderror</label>
                <label class="text-sm font-semibold text-ink md:col-span-2">Nội dung<textarea class="form-field" name="message" rows="6" maxlength="5000">{{ old('message') }}</textarea>@error('message')<span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span>@enderror</label>
            </div>
            <p class="mt-5 text-xs leading-5 text-slate-500"><span class="text-primary">*</span> Thông tin bắt buộc</p>
            <button class="button-primary mt-5" type="submit">Gửi thông tin đăng ký <span aria-hidden="true">↗</span></button>
        </form>
    </div>
</section>
