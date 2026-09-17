@if ($pricingMediaUrl || $pricingSourceUrl)
    <section class="service-pricing-media resource-detail-content" id="tai-lieu-bang-gia">
        <div class="site-container w-100 mx-auto dv-services-partials-pricing-media__div-1">
            <div class="service-pricing-media__card">
                <div class="service-pricing-media__header">
                    <div>
                        <h2 class="display-title">{{ $pricingCatalog?->title ?: 'Bảng giá dịch vụ' }}</h2>
                    </div>
                    @if ($pricingSourceUrl)
                        <a class="btn btn-dark button-dark" href="{{ $pricingSourceUrl }}" target="_blank" rel="noopener noreferrer">Mở bảng giá online <span aria-hidden="true">↗</span></a>
                    @endif
                </div>

                @if ($pricingMediaUrl)
                    @if ($pricingMediaIsImage)
                        <a class="service-pricing-media__image glightbox" href="{{ $pricingMediaUrl }}" data-type="image" data-gallery="service-pricing-media-{{ $service->id }}" data-title="Bảng giá {{ $service->title }}" aria-label="Mở bảng giá {{ $service->title }}">
                            <img src="{{ $pricingMediaUrl }}" alt="Bảng giá {{ $service->title }}" loading="eager">
                            <span class="service-pricing-media__open" aria-hidden="true">↗</span>
                        </a>
                    @else
                        <a class="service-pricing-media__file" href="{{ $pricingMediaUrl }}" target="_blank" rel="noopener" aria-label="Mở hoặc tải bảng giá {{ $service->title }}">
                            <span class="service-pricing-media__file-icon" aria-hidden="true">↗</span>
                            <span>
                                <strong>Mở file bảng giá</strong>
                                <small>Xem hoặc tải tài liệu bảng giá của {{ $service->title }}.</small>
                            </span>
                        </a>
                    @endif
                @endif

                @if ($pricingCatalog?->description)
                    <p class="service-pricing-media__description">{{ $pricingCatalog->description }}</p>
                @endif
            </div>
        </div>
    </section>
@endif
