@use(App\Support\Localization\LocalizedUrl)

@if ($pricingMatrix['packages'] !== [])
    <section id="goi-dich-vu" class="service-pricing-section section-space">
        <div class="site-shell">
            <header class="service-pricing-section__header">
                <h2 class="display-title">{{ $pricingMatrix['title'] }}</h2>
                <p>{{ $pricingMatrix['description'] ?: 'Các gói được xây dựng theo phạm vi công việc và mục tiêu thực tế của từng dịch vụ.' }}</p>
            </header>

            <div class="service-pricing-table-wrap">
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
                                    <span class="service-pricing-table__price">
                                        @if ($package['has_promotion'])
                                            <del>{{ number_format($package['list_price'], 0, ',', '.') }}đ</del>
                                            <strong>{{ number_format($package['promotion_price'], 0, ',', '.') }}đ</strong>
                                            <small>{{ $package['promotion_type'] === 'percent' ? 'Giảm '.$package['promotion_value'].'%' : 'Giá khuyến mại' }}</small>
                                        @elseif ($package['list_price'] !== null)
                                            <strong>{{ number_format($package['list_price'], 0, ',', '.') }}đ</strong>
                                        @else
                                            <strong>{{ $package['price_label'] ?: 'Báo giá theo yêu cầu' }}</strong>
                                        @endif
                                        @if ($package['price_unit'])
                                            <small>{{ $package['price_unit'] }}</small>
                                        @endif
                                    </span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pricingMatrix['items'] as $itemName)
                            <tr>
                                <th scope="row">{{ $itemName }}</th>
                                @foreach ($pricingMatrix['packages'] as $package)
                                    @php $packageItem = collect($package['items'])->firstWhere('name', $itemName); @endphp
                                    <td class="service-pricing-table__included-cell">
                                        @if ($packageItem)
                                            <span class="service-pricing-table__check" aria-label="Có">✓</span>
                                            @if ($packageItem['description'])
                                                <small>{{ $packageItem['description'] }}</small>
                                            @endif
                                        @else
                                            <span class="service-pricing-table__not-included" aria-label="Không áp dụng">—</span>
                                        @endif
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
        </div>
    </section>
@endif
