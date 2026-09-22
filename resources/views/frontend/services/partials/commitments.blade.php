<section id="cam-ket" class="section-space">
    <div class="container">
        <div class="overflow-hidden commitment-layout">
            <div>
                @if ($commitmentImageUrl)
                    <img class="h-100 w-100 object-fit-cover" src="{{ $commitmentImageUrl }}" alt="{{ $service->commitment_title ?: 'Cam kết của '.$website->site_name }}" loading="lazy">
                @else
                    <div class="image-placeholder h-100">{{ $website->site_name }}</div>
                @endif
            </div>
            <div class="p-4">
                <h2 class="display-title h2">{{ $service->commitment_title ?: 'Cam kết của '.$website->site_name }}</h2>
                <p class="lead">{{ $service->commitment_description ?: 'Một quy trình rõ ràng và một đầu mối phối hợp xuyên suốt để dịch vụ được triển khai hiệu quả.' }}</p>
                <ul class="d-grid gap-4 list-unstyled mt-4">
                    @foreach ($commitmentItems as $item)
                        <li class="d-flex gap-3">
                            <span class="d-grid flex-shrink-0 fw-bold text-primary fs-4" aria-hidden="true">✓</span>
                            <div>
                                <h3 class="fw-bold h5">{{ $item['title'] }}</h3>
                                @if (filled($item['description'] ?? null))
                                    <p class="mb-0">{{ $item['description'] }}</p>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>
