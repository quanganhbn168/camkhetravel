@extends('layouts.master')

@use(App\Support\Localization\LocalizedUrl)

@section('content')
    @include('frontend.partials.resource-archive-hero', [
        'resourceName' => 'Sản phẩm',
        'resourceIndexRoute' => 'products.index',
    ])

    <section class="section-space bg-white">
        <div class="site-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            @include('frontend.partials.resource-filter-bar', [
                'resourceName' => 'sản phẩm',
                'resourceIndexRoute' => 'products.index',
                'categoryCountAttribute' => 'products_count',
            ])
            <div class="mt-9 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                @forelse ($products as $product)
                    @include('frontend.partials.product-card')
                @empty
                    <p class="col-span-full rounded-2xl border border-dashed border-slate-300 p-8 text-sm leading-7 text-slate-500">Chưa có sản phẩm được xuất bản.</p>
                @endforelse
            </div>
            @if ($products->hasPages())<div class="mt-12">{{ $products->onEachSide(1)->links() }}</div>@endif
        </div>
    </section>

    <section class="resource-archive-cta">
        @if (($heroImageUrl ?: $defaultBannerUrl))<img class="resource-archive-cta__image" src="{{ $heroImageUrl ?: $defaultBannerUrl }}" alt="" aria-hidden="true">@endif
        <div class="resource-archive-cta__overlay"></div>
        <div class="site-container mx-auto flex w-full max-w-7xl flex-col gap-5 px-4 py-12 md:flex-row md:items-center md:justify-between lg:px-8">
            <div><h2 class="max-w-2xl font-display text-3xl leading-tight tracking-[-0.045em] text-white md:text-4xl">Cần thiết bị PCCC phù hợp?</h2><p class="mt-3 max-w-xl text-sm leading-7 text-white/75">Gửi thông tin công trình để DVTEC tư vấn cấu hình đồng bộ.</p></div>
            <a class="button-primary shrink-0" href="{{ LocalizedUrl::route('contact') }}">Nhận tư vấn <span aria-hidden="true">→</span></a>
        </div>
    </section>
@endsection
