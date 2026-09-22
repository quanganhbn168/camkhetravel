@extends('layouts.master')



@section('content')
    @include('frontend.partials.resource-archive-hero', [
        'resourceName' => 'Sản phẩm',
        'resourceIndexRoute' => 'products.index',
    ])

    <section class="section-space">
        <div class="container">
            @include('frontend.partials.resource-filter-bar', [
                'resourceName' => 'sản phẩm',
                'resourceIndexRoute' => 'products.index',
                'categoryCountAttribute' => 'products_count',
            ])
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mt-4">
                @forelse ($products as $product)
                    <div class="col">@include('frontend.partials.product-card')</div>
                @empty
                    <p class="empty-state w-100">Chưa có sản phẩm được xuất bản.</p>
                @endforelse
            </div>
            @if ($products->hasPages())<div class="mt-4">{{ $products->onEachSide(1)->links('frontend.partials.pagination') }}</div>@endif
        </div>
    </section>

@endsection
