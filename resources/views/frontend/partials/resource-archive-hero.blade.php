@use(App\Support\Localization\LocalizedUrl)

<section class="resource-archive-hero">
    @if ($heroImageUrl)
        <img class="resource-archive-hero__image" src="{{ $heroImageUrl }}" alt="" aria-hidden="true">
    @endif
    <div class="resource-archive-hero__overlay"></div>
    <div class="site-shell resource-archive-hero__content">
        <nav aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-white/65">
                <li><a class="hover:text-white" href="{{ LocalizedUrl::route('home') }}">Trang chủ</a></li>
                <li aria-hidden="true">/</li>
                @if ($activeCategory)
                    <li><a class="hover:text-white" href="{{ LocalizedUrl::route($resourceIndexRoute) }}">{{ $resourceName }}</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-white" aria-current="page">{{ $activeCategory->name }}</li>
                @else
                    <li class="text-white" aria-current="page">{{ $resourceName }}</li>
                @endif
            </ol>
        </nav>
        <h1 class="mt-7 max-w-4xl font-display text-4xl leading-[1.1] tracking-[-0.045em] text-white md:text-6xl">{{ $pageTitle }}</h1>
        <p class="mt-5 max-w-2xl text-sm leading-7 text-white/75 md:text-base md:leading-8">{{ $pageDescription }}</p>
    </div>
</section>
