@php
    $defaultCommitmentItems = [
        ['title' => 'Rõ ràng ngay từ đầu', 'description' => 'Phạm vi, tiến độ và đầu ra được thống nhất trước khi triển khai.'],
        ['title' => 'Đồng hành xuyên suốt', 'description' => 'Đội ngũ phối hợp cùng khách hàng từ định hướng đến bàn giao.'],
        ['title' => 'Chỉn chu từng chi tiết', 'description' => 'Mỗi hạng mục được kiểm tra trước khi hoàn thiện và bàn giao.'],
    ];
    $visibleCommitmentItems = $commitmentItems->isNotEmpty() ? $commitmentItems->all() : $defaultCommitmentItems;
@endphp

<section id="cam-ket" class="section-space dv-services-partials-commitments__section-1">
    <div class="site-container w-100 mx-auto dv-services-partials-commitments__div-2">
        <div class="overflow-hidden dv-services-partials-commitments__div-3">
            <div class="dv-services-partials-commitments__div-4">
                @if ($commitmentImageUrl)
                    <img class="h-100 w-100 object-fit-cover dv-services-partials-commitments__media-5" src="{{ $commitmentImageUrl }}" alt="{{ $service->commitment_title ?: 'Cam kết của DVTEC' }}" loading="lazy">
                @else
                    <div class="image-placeholder dv-services-partials-commitments__div-6">DVTEC</div>
                @endif
            </div>
            <div class="dv-services-partials-commitments__div-7">
                <h2 class="display-title dv-services-partials-commitments__heading-8">{{ $service->commitment_title ?: 'Cam kết của DVTEC' }}</h2>
                <p class="dv-services-partials-commitments__copy-9">{{ $service->commitment_description ?: 'Một quy trình rõ ràng và một đầu mối phối hợp xuyên suốt để dịch vụ được triển khai hiệu quả.' }}</p>
                <ul class="d-grid dv-services-partials-commitments__ul-10">
                    @foreach ($visibleCommitmentItems as $item)
                        <li class="d-flex dv-services-partials-commitments__li-11">
                            <span class="d-grid flex-shrink-0 fw-bold dv-services-partials-commitments__copy-12" aria-hidden="true">✓</span>
                            <div>
                                <h3 class="fw-bold dv-services-partials-commitments__heading-13">{{ $item['title'] }}</h3>
                                @if (filled($item['description'] ?? null))
                                    <p class="dv-services-partials-commitments__copy-14">{{ $item['description'] }}</p>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>
