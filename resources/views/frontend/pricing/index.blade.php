@extends('layouts.master')

@use(App\Support\Localization\LocalizedUrl)

@section('content')
    <section class="relative isolate overflow-hidden bg-ink py-18 text-white md:py-26">
        <div class="absolute top-[-10rem] right-[8%] -z-10 size-96 rounded-full bg-primary/15 blur-3xl"></div>
        <div class="site-shell text-center">
            <h1 class="font-display mx-auto max-w-3xl text-4xl leading-tight tracking-[-0.045em] md:text-5xl">{{ $selectedService ? 'Bảng giá '.$selectedService->title : 'Mức đầu tư rõ ràng cho từng mục tiêu truyền thông.' }}</h1>
            <p class="mx-auto mt-6 max-w-2xl text-base leading-8 text-slate-300 md:text-lg">{{ $selectedService ? 'Các gói và mức đầu tư tham khảo cho dịch vụ này.' : 'Các gói dưới đây là mức tham khảo. THT Media sẽ điều chỉnh phạm vi và báo giá khi đã hiểu đúng nhu cầu thực tế của anh/chị.' }}</p>
            @if ($selectedService)
                <a class="mt-6 inline-flex text-sm font-semibold text-primary-soft hover:text-white" href="{{ LocalizedUrl::route('pricing.index') }}">Xem toàn bộ bảng giá <span class="ml-2" aria-hidden="true">→</span></a>
            @endif
        </div>
    </section>

    <section class="section-space">
        <div class="site-shell">
            @forelse ($plans as $plan)
                @if ($loop->first)<div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">@endif
                <article class="relative flex h-full flex-col overflow-hidden rounded-[1.75rem] border p-7 {{ $plan->is_featured ? 'border-ink bg-ink text-white shadow-[0_24px_60px_rgba(31,43,37,0.18)]' : 'border-slate-200 bg-white text-ink' }}" id="bang-gia-{{ $plan->id }}">
                    @if ($plan->badge)<span class="mb-6 w-fit rounded-full px-3 py-1 text-[0.68rem] font-bold tracking-[0.12em] uppercase {{ $plan->is_featured ? 'bg-primary text-white' : 'bg-sand text-accent' }}">{{ $plan->badge }}</span>@endif
                    @if ($plan->landing)<a class="text-sm font-semibold {{ $plan->is_featured ? 'text-primary-soft hover:text-white' : 'text-accent hover:text-ink' }}" href="{{ LocalizedUrl::slug($plan->landing->slug) }}">{{ $plan->landing->title }}</a>@endif
                    <h2 class="font-display mt-3 text-3xl leading-tight">{{ $plan->name }}</h2>
                    @if ($plan->description)<p class="mt-4 text-sm leading-7 {{ $plan->is_featured ? 'text-slate-300' : 'text-slate-500' }}">{{ $plan->description }}</p>@endif
                    <div class="mt-8 border-y py-6 {{ $plan->is_featured ? 'border-white/15' : 'border-slate-200' }}">
                        @if ($plan->price)<p class="font-display text-4xl tracking-[-0.04em]">{{ number_format($plan->price) }}<span class="ml-1 text-lg">đ</span></p>@else<p class="font-display text-3xl leading-tight">{{ $plan->price_label ?: 'Liên hệ' }}</p>@endif
                        @if ($plan->price_unit)<p class="mt-2 text-xs font-medium {{ $plan->is_featured ? 'text-slate-400' : 'text-slate-400' }}">{{ $plan->price_unit }}</p>@endif
                    </div>
                    <ul class="mt-6 grid gap-3 text-sm leading-6 {{ $plan->is_featured ? 'text-slate-200' : 'text-slate-600' }}">
                        @forelse ($plan->features ?? [] as $feature)
                            <li class="flex gap-3"><svg class="mt-1 size-4 shrink-0 text-primary" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 0 1 0 1.4l-7.5 7.5a1 1 0 0 1-1.4 0L3.3 9.7a1 1 0 1 1 1.4-1.4l3.8 3.8 6.8-6.8a1 1 0 0 1 1.4 0Z" clip-rule="evenodd"/></svg><span>{{ $feature }}</span></li>
                        @empty
                            <li class="flex gap-3"><svg class="mt-1 size-4 shrink-0 text-primary" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 0 1 0 1.4l-7.5 7.5a1 1 0 0 1-1.4 0L3.3 9.7a1 1 0 1 1 1.4-1.4l3.8 3.8 6.8-6.8a1 1 0 0 1 1.4 0Z" clip-rule="evenodd"/></svg><span>Phạm vi được tư vấn theo yêu cầu thực tế.</span></li>
                        @endforelse
                    </ul>
                    <a class="mt-8 {{ $plan->is_featured ? 'button-primary' : 'button-dark' }} w-full" href="{{ LocalizedUrl::route('contact', array_filter(['landing' => $plan->landing_id])) }}">{{ __('site.consult') }}</a>
                </article>
                @if ($loop->last)</div>@endif
            @empty
                <div class="mx-auto max-w-2xl rounded-[1.75rem] border border-dashed border-slate-300 bg-mist/55 p-9 text-center"><h2 class="display-title text-3xl">Đang hoàn thiện các gói dịch vụ</h2><p class="mt-4 text-sm leading-7 text-slate-600">Anh/chị có thể để lại mục tiêu và ngân sách dự kiến để THT Media tư vấn phạm vi phù hợp.</p><a class="button-dark mt-6" href="{{ LocalizedUrl::route('contact') }}">{{ __('site.consult') }}</a></div>
            @endforelse
        </div>
    </section>
@endsection
