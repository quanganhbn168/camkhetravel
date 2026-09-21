@php
    $defaultCommitmentItems = [
        ['title' => 'Rõ ràng ngay từ đầu', 'description' => 'Phạm vi, tiến độ và đầu ra được thống nhất trước khi triển khai.'],
        ['title' => 'Đồng hành xuyên suốt', 'description' => 'Đội ngũ phối hợp cùng khách hàng từ định hướng đến bàn giao.'],
        ['title' => 'Chỉn chu từng chi tiết', 'description' => 'Mỗi hạng mục được kiểm tra trước khi hoàn thiện và bàn giao.'],
    ];
    $visibleCommitmentItems = $commitmentItems->isNotEmpty() ? $commitmentItems->all() : $defaultCommitmentItems;
@endphp

<section id="cam-ket" class="section-space site-services-partials-commitments__section-1">
    <div class="site-container w-100 mx-auto site-services-partials-commitments__div-2">
        <div class="overflow-hidden site-services-partials-commitments__div-3">
            <div class="site-services-partials-commitments__div-4">
                @if ($commitmentImageUrl)
                    <img class="h-100 w-100 object-fit-cover site-services-partials-commitments__media-5" src="{{ $commitmentImageUrl }}" alt="{{ $service->commitment_title ?: 'Cam kết của '.$website->site_name }}" loading="lazy">
                @else
                    <div class="image-placeholder site-services-partials-commitments__div-6">{{ $website->site_name }}</div>
                @endif
            </div>
            <div class="site-services-partials-commitments__div-7">
                <h2 class="display-title site-services-partials-commitments__heading-8">{{ $service->commitment_title ?: 'Cam kết của '.$website->site_name }}</h2>
                <p class="site-services-partials-commitments__copy-9">{{ $service->commitment_description ?: 'Một quy trình rõ ràng và một đầu mối phối hợp xuyên suốt để dịch vụ được triển khai hiệu quả.' }}</p>
                <ul class="d-grid site-services-partials-commitments__ul-10">
                    @foreach ($visibleCommitmentItems as $item)
                        <li class="d-flex site-services-partials-commitments__li-11">
                            <span class="d-grid flex-shrink-0 fw-bold site-services-partials-commitments__copy-12" aria-hidden="true">✓</span>
                            <div>
                                <h3 class="fw-bold site-services-partials-commitments__heading-13">{{ $item['title'] }}</h3>
                                @if (filled($item['description'] ?? null))
                                    <p class="site-services-partials-commitments__copy-14">{{ $item['description'] }}</p>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>
