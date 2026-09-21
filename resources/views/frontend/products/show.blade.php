@extends('layouts.master')

@push('styles')
    @vite('resources/css/frontend/pages/products-show.css')
@endpush

@use(App\Support\Localization\LocalizedUrl)

@section('content')
    <article>
        <header class="position-relative overflow-hidden site-products-show__element-1">
            @if ($product->image_url)<img class="position-absolute h-100 w-100 object-fit-cover site-products-show__media-2" src="{{ $product->image_url }}" alt="" aria-hidden="true">@endif
            <div class="position-absolute site-products-show__div-3"></div>
            <div class="site-container mx-auto w-100 site-products-show__div-4">
                <p class="d-flex flex-wrap align-items-center site-products-show__copy-5"><a class="site-products-show__action-6" href="{{ LocalizedUrl::route('home') }}">Trang chủ</a><span class="site-products-show__copy-7">›</span><a class="site-products-show__action-6" href="{{ LocalizedUrl::route('products.index') }}">Sản phẩm</a>@if ($product->category)<span class="site-products-show__copy-7">›</span><span>{{ $product->category->name }}</span>@endif</p>
                <h1 class="site-products-show__heading-8">{{ $product->title }}</h1>
                @if ($product->excerpt)<p class="site-products-show__copy-9">{{ $product->excerpt }}</p>@endif
                @if ($product->sku)<p class="fw-semibold text-uppercase site-products-show__copy-10">Mã sản phẩm: {{ $product->sku }}</p>@endif
            </div>
        </header>

        <section class="section-space">
            <div class="site-container mx-auto w-100 align-items-start site-products-show__div-11">
                <div>
                    @if ($product->image_url)<div class="overflow-hidden site-products-show__div-12"><img class="h-auto w-100 object-fit-cover" src="{{ $product->image_url }}" alt="{{ $product->title }}"></div>@endif
                    @if ($galleryImages)<div class="site-products-show__div-14">@foreach ($galleryImages as $image)<img class="w-100 object-fit-cover site-products-show__media-15" src="{{ $image }}" alt="" loading="lazy">@endforeach</div>@endif
                    @if ($product->body)<div class="article-prose site-products-show__div-16">{!! $product->body !!}</div>@endif
                </div>
                <aside class="site-products-show__aside-17">
                    <p class="fw-bold text-uppercase site-products-show__copy-18">Thông tin sản phẩm</p>
                    <dl class="site-products-show__dl-19">
                        @if ($product->category)<div class="d-flex justify-content-between site-products-show__div-20"><dt class="site-products-show__copy-7">Danh mục</dt><dd class="text-end fw-semibold site-products-show__dd-21">{{ $product->category->name }}</dd></div>@endif
                        @if ($product->sku)<div class="d-flex justify-content-between site-products-show__div-20"><dt class="site-products-show__copy-7">Mã sản phẩm</dt><dd class="text-end fw-semibold site-products-show__dd-21">{{ $product->sku }}</dd></div>@endif
                        <div class="d-flex justify-content-between site-products-show__div-20"><dt class="site-products-show__copy-7">Tư vấn</dt><dd class="text-end fw-semibold site-products-show__dd-22">Liên hệ {{ $website->site_name }}</dd></div>
                    </dl>
                    @if ($product->tags->isNotEmpty())<div class="d-flex flex-wrap site-products-show__div-23">@foreach ($product->tags as $tag)<span class="fw-semibold site-products-show__copy-24">#{{ $tag->name }}</span>@endforeach</div>@endif
                    <a class="btn btn-primary button-primary w-100 site-products-show__action-25" href="{{ LocalizedUrl::route('contact') }}">Nhận tư vấn sản phẩm <span aria-hidden="true">→</span></a>
                </aside>
            </div>
        </section>

        @if ($faqItems->isNotEmpty())
            <section class="home-faq section-space site-products-show__section-26">
                <div class="site-container mx-auto w-100 site-products-show__div-27">
                    <h2 class="display-title text-uppercase site-products-show__heading-28">Câu hỏi thường gặp</h2>
                    <div class="home-faq__list">@foreach ($faqItems as $faq)<details class="home-faq__item" @if ($loop->first) open @endif><summary class="home-faq__question"><span>{{ $faq->question }}</span><span class="home-faq__indicator" aria-hidden="true">+</span></summary><div class="home-faq__answer"><p>{{ $faq->answer }}</p></div></details>@endforeach</div>
                </div>
            </section>
        @endif
    </article>
@endsection
