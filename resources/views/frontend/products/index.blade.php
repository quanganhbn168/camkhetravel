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

@endsection
