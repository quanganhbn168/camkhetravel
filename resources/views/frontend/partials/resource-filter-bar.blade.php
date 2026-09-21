@use(App\Support\Localization\LocalizedUrl)

<div class="resource-filter-bar">
    <div class="resource-filter-bar__categories" aria-label="Lọc theo danh mục">
        <a class="resource-filter  {{ ! $activeCategory ? 'is-active' : '' }}" href="{{ LocalizedUrl::route($resourceIndexRoute) }}">Tất cả {{ mb_strtolower($resourceName) }}</a>
        @foreach ($categories as $category)
            <a class="resource-filter  {{ $activeCategory?->is($category) ? 'is-active' : '' }}" href="{{ $category->public_url ?? LocalizedUrl::slug($category->slug) }}">
                {{ $category->name }}
                @if ($category->{$categoryCountAttribute})
                    <span class="resource-filter__count">{{ $category->{$categoryCountAttribute} }}</span>
                @endif
            </a>
        @endforeach
    </div>
    <form class="resource-sort" method="GET" action="{{ url()->current() }}">
        <label class="site-partials-resource-filter-bar__element-1" for="resource-sort">Sắp xếp {{ mb_strtolower($resourceName) }}</label>
        <select id="resource-sort" name="sort" onchange="this.form.submit()">
            @foreach ($sortOptions as $value => $label)
                <option value="{{ $value }}" @selected($sort === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </form>
</div>
