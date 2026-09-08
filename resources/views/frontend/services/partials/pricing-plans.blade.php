@use(App\Support\Localization\LocalizedUrl)

@if ($pricingMatrix['packages'] !== [])
    <section id="goi-dich-vu" class="service-pricing-section section-space">
        <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8">
            <header class="service-pricing-section__header">
                <h2 class="display-title">{{ $pricingMatrix['title'] }}</h2>
                <p>{{ $pricingMatrix['description'] ?: 'Các gói được xây dựng theo phạm vi công việc và mục tiêu thực tế của từng dịch vụ.' }}</p>
            </header>

            <div class="service-package-grid">
                @foreach ($pricingMatrix['packages'] as $package)
                    <article class="service-package-card {{ $package['is_featured'] ? 'is-featured' : '' }}">
                        <div class="service-package-card__intro">
                            @if ($package['badge'])<span class="service-pricing-table__badge">{{ $package['badge'] }}</span>@endif
                            <h3>{{ $package['name'] }}</h3>
                            @if ($package['description'])<p>{{ $package['description'] }}</p>@endif
                        </div>
                        @include('frontend.services.partials.package-price')
                        <a class="{{ $package['is_featured'] ? 'button-primary' : 'button-dark' }}" href="{{ LocalizedUrl::route('contact', ['service' => $service->id, 'plan' => $package['id']]) }}" aria-label="Nhận tư vấn gói {{ $package['name'] }}">Nhận tư vấn gói này <span aria-hidden="true">→</span></a>
                        @if ($package['items'] !== [])
                            <ul class="service-package-card__items">
                                @foreach (array_slice($package['items'], 0, 4) as $item)
                                    <li><span aria-hidden="true">✓</span><div>{{ $item['name'] }}@if ($item['description'])<small>{{ $item['description'] }}</small>@endif</div></li>
                                @endforeach
                            </ul>
                            @if (count($package['items']) > 4)
                                <details class="service-package-card__more">
                                    <summary>Xem thêm {{ count($package['items']) - 4 }} hạng mục</summary>
                                    <ul class="service-package-card__items">
                                        @foreach (array_slice($package['items'], 4) as $item)
                                            <li><span aria-hidden="true">✓</span><div>{{ $item['name'] }}@if ($item['description'])<small>{{ $item['description'] }}</small>@endif</div></li>
                                        @endforeach
                                    </ul>
                                </details>
                            @endif
                        @endif
                    </article>
                @endforeach
            </div>

            @if (count($pricingMatrix['packages']) > 1 && $pricingMatrix['comparison_rows'] !== [])
            <details class="service-pricing-comparison">
                <summary>So sánh chi tiết các gói <span aria-hidden="true">＋</span></summary>
                <p class="service-pricing-comparison__hint">Cuộn ngang để xem đầy đủ các gói.</p>
            <div class="service-pricing-table-wrap" tabindex="0" role="region" aria-label="Bảng so sánh các gói dịch vụ">

                <table class="service-pricing-table">
                    <caption class="sr-only">So sánh các gói trong {{ $pricingMatrix['title'] }} của {{ $service->title }}</caption>
                    <thead>
                        <tr>
                            <th class="service-pricing-table__feature-heading" scope="col">Hạng mục</th>
                            @foreach ($pricingMatrix['packages'] as $package)
                                <th class="service-pricing-table__package-heading {{ $package['is_featured'] ? 'is-featured' : '' }}" scope="col">
                                    @if ($package['badge'])
                                        <span class="service-pricing-table__badge">{{ $package['badge'] }}</span>
                                    @endif
                                    <span class="service-pricing-table__package-name">{{ $package['name'] }}</span>
                                    @if ($package['description'])
                                        <span class="service-pricing-table__package-description">{{ $package['description'] }}</span>
                                    @endif
                                    @include('frontend.services.partials.package-price')
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pricingMatrix['comparison_rows'] as $comparisonRow)
                            <tr>
                                <th scope="row">{{ $comparisonRow['name'] }}</th>
                                @foreach ($pricingMatrix['packages'] as $package)
                                    @php $cell = $comparisonRow['cells'][$package['id']]; @endphp
                                    <td class="service-pricing-table__included-cell">
                                        @if ($cell['included'] === true)
                                            <span class="service-pricing-table__check" aria-label="Có">✓</span>
                                        @elseif ($cell['included'] === false)
                                            <span class="service-pricing-table__not-included" aria-label="Không bao gồm">—</span>
                                        @else
                                            <small>Chưa thiết lập</small>
                                        @endif
                                        @if (filled($cell['value']))<small>{{ $cell['value'] }}</small>@endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th scope="row">Trao đổi theo nhu cầu</th>
                            @foreach ($pricingMatrix['packages'] as $package)
                                <td><a class="button-dark" href="{{ LocalizedUrl::route('contact', ['service' => $service->id, 'plan' => $package['id']]) }}">Nhận tư vấn <span aria-hidden="true">→</span></a></td>
                            @endforeach
                        </tr>
                    </tfoot>
                </table>
            </div>
            </details>
            @endif
        </div>
    </section>
@endif
