@extends('layouts.master')

@push('styles')
    @vite('resources/scss/pages/product.scss')
@endpush

@section('content')
    <article>
        <header class="position-relative overflow-hidden archive-hero">
            @if ($product->image_url)<img class="position-absolute h-100 w-100 object-fit-cover archive-hero__image" src="{{ $product->image_url }}" alt="" aria-hidden="true">@endif
            <div class="position-absolute archive-hero__overlay"></div>
            <div class="container position-relative">
                <p class="d-flex flex-wrap align-items-center small gap-2"><a class="link-light" href="{{ route('home') }}">Trang chủ</a><span class="mx-1">›</span><a class="link-light" href="{{ route('products.index') }}">Sản phẩm</a>@if ($product->category)<span class="mx-1">›</span><span>{{ $product->category->name }}</span>@endif</p>
                <h1 class="display-title text-white">{{ $product->title }}</h1>
                @if ($product->excerpt)<p class="lead">{{ $product->excerpt }}</p>@endif
                @if ($product->sku)<p class="fw-semibold text-uppercase small">Mã sản phẩm: {{ $product->sku }}</p>@endif
            </div>
        </header>

        <section class="section-space">
            <div class="container align-items-start product-layout">
                <div>
                    @if ($product->image_url)<div class="overflow-hidden rounded"><img class="h-auto w-100 object-fit-cover" src="{{ $product->image_url }}" alt="{{ $product->title }}"></div>@endif
                    @if ($galleryImages)<div class="gallery-grid mt-3">@foreach ($galleryImages as $image)<img class="w-100 object-fit-cover rounded" src="{{ $image }}" alt="" loading="lazy">@endforeach</div>@endif
                    @if ($product->body)<div class="article-prose mt-4">{!! $product->body !!}</div>@endif
                </div>
                <aside class="product-summary">
                    <p class="fw-bold text-uppercase h5">Thông tin sản phẩm</p>
                    <dl class="mb-0">
                        @if ($product->category)<div class="d-flex justify-content-between gap-3 py-3 border-bottom"><dt class="mx-1">Danh mục</dt><dd class="text-end fw-semibold mb-0">{{ $product->category->name }}</dd></div>@endif
                        @if ($product->sku)<div class="d-flex justify-content-between gap-3 py-3 border-bottom"><dt class="mx-1">Mã sản phẩm</dt><dd class="text-end fw-semibold mb-0">{{ $product->sku }}</dd></div>@endif
                        <div class="d-flex justify-content-between gap-3 py-3 border-bottom"><dt class="mx-1">Tư vấn</dt><dd class="text-end fw-semibold mb-0 text-primary">Liên hệ {{ $website->site_name }}</dd></div>
                    </dl>
                    @if ($product->tags->isNotEmpty())<div class="d-flex flex-wrap gap-2 mt-3">@foreach ($product->tags as $tag)<span class="fw-semibold badge text-bg-light">#{{ $tag->name }}</span>@endforeach</div>@endif
                    <a class="btn btn-primary w-100 mt-4" href="{{ route('contact') }}">Nhận tư vấn sản phẩm <span aria-hidden="true">→</span></a>
                </aside>
            </div>
        </section>

        @if ($faqItems->isNotEmpty())
            <section class="faq section-space bg-light">
                <div class="container">
                    <h2 class="display-title text-uppercase h2 mb-4">Câu hỏi thường gặp</h2>
                    <div class="faq__list">@foreach ($faqItems as $faq)<details class="faq__item" @if ($loop->first) open @endif><summary class="faq__question"><span>{{ $faq->question }}</span><span class="faq__indicator" aria-hidden="true">+</span></summary><div class="faq__answer"><p>{{ $faq->answer }}</p></div></details>@endforeach</div>
                </div>
            </section>
        @endif
    </article>
@endsection
