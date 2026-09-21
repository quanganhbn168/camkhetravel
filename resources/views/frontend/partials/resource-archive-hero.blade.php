@use(App\Support\Localization\LocalizedUrl)

<section class="resource-archive-hero">
    @if (($heroImageUrl ?: $defaultBannerUrl))
        <img class="resource-archive-hero__image" src="{{ ($heroImageUrl ?: $defaultBannerUrl) }}" alt="" aria-hidden="true">
    @endif
    <div class="resource-archive-hero__overlay"></div>
    <div class="site-container resource-archive-hero__content w-100 mx-auto site-partials-resource-archive-hero__div-1">
        <nav aria-label="Breadcrumb">
            <ol class="d-flex flex-wrap align-items-center site-partials-resource-archive-hero__element-2">
                <li><a class="site-partials-resource-archive-hero__action-3" href="{{ LocalizedUrl::route('home') }}">Trang chủ</a></li>
                <li aria-hidden="true">/</li>
                @if ($activeCategory)
                    <li><a class="site-partials-resource-archive-hero__action-3" href="{{ LocalizedUrl::route($resourceIndexRoute) }}">{{ $resourceName }}</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="site-partials-resource-archive-hero__li-4" aria-current="page">{{ $activeCategory->name }}</li>
                @else
                    <li class="site-partials-resource-archive-hero__li-4" aria-current="page">{{ $resourceName }}</li>
                @endif
            </ol>
        </nav>
        <h1 class="site-partials-resource-archive-hero__heading-5">{{ $pageTitle }}</h1>
        <p class="site-partials-resource-archive-hero__copy-6">{{ $pageDescription }}</p>
    </div>
</section>
