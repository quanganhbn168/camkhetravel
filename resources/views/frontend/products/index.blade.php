@extends('layouts.master')

@push('styles')
    @vite('resources/css/frontend/pages/products-index.css')
@endpush

@use(App\Support\Localization\LocalizedUrl)

@section('content')
    @include('frontend.partials.resource-archive-hero', [
        'resourceName' => 'Sản phẩm',
        'resourceIndexRoute' => 'products.index',
    ])

    <section class="section-space site-products-index__section-1">
        <div class="site-container mx-auto w-100 site-products-index__div-2">
            @include('frontend.partials.resource-filter-bar', [
                'resourceName' => 'sản phẩm',
                'resourceIndexRoute' => 'products.index',
                'categoryCountAttribute' => 'products_count',
            ])
            <div class="site-products-index__div-3">
                @forelse ($products as $product)
                    @include('frontend.partials.product-card')
                @empty
                    <p class="site-products-index__copy-4">Chưa có sản phẩm được xuất bản.</p>
                @endforelse
            </div>
            @if ($products->hasPages())<div class="site-products-index__div-5">{{ $products->onEachSide(1)->links('frontend.partials.pagination') }}</div>@endif
        </div>
    </section>

@endsection
