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
