<div class="branding-gold-card">
    @if ($offer['discount_label'])
        <small class="branding-discount">ƯU ĐÃI GIẢM {{ $offer['discount_label'] }}</small>
    @endif
    @if ($isHeading ?? false)
        <h2 class="branding-offer-label">CHỈ CÒN</h2>
    @else
        <span class="branding-offer-label">CHỈ CÒN</span>
    @endif
    <strong>{{ number_format($offer['price'], 0, ',', '.') }} <small>VNĐ</small></strong>
</div>
