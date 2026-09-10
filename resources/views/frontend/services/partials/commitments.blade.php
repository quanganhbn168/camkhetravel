@php
    $defaultCommitmentItems = [
        ['title' => 'Rõ ràng ngay từ đầu', 'description' => 'Phạm vi, tiến độ và đầu ra được thống nhất trước khi triển khai.'],
        ['title' => 'Đồng hành xuyên suốt', 'description' => 'Đội ngũ phối hợp cùng khách hàng từ định hướng đến bàn giao.'],
        ['title' => 'Chỉn chu từng chi tiết', 'description' => 'Mỗi hạng mục được kiểm tra trước khi hoàn thiện và bàn giao.'],
    ];
    $visibleCommitmentItems = $commitmentItems->isNotEmpty() ? $commitmentItems->all() : $defaultCommitmentItems;
@endphp

<section id="cam-ket" class="section-space bg-mist/55">
    <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8">
        <div class="grid overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white shadow-[0_16px_38px_rgba(31,43,37,0.07)] lg:grid-cols-2">
            <div class="min-h-72 bg-ink lg:min-h-full">
                @if ($commitmentImageUrl)
                    <img class="h-full min-h-72 w-full object-cover lg:min-h-[34rem]" src="{{ $commitmentImageUrl }}" alt="{{ $service->commitment_title ?: 'Cam kết của DVTEC' }}" loading="lazy">
                @else
                    <div class="image-placeholder min-h-72 lg:min-h-[34rem]">DVTEC</div>
                @endif
            </div>
            <div class="p-7 md:p-10 lg:p-14">
                <h2 class="display-title text-3xl leading-tight md:text-4xl">{{ $service->commitment_title ?: 'Cam kết của DVTEC' }}</h2>
                <p class="mt-5 text-base leading-8 text-slate-600">{{ $service->commitment_description ?: 'Một quy trình rõ ràng và một đầu mối phối hợp xuyên suốt để dịch vụ được triển khai hiệu quả.' }}</p>
                <ul class="mt-8 grid gap-5 border-t border-slate-200 pt-7">
                    @foreach ($visibleCommitmentItems as $item)
                        <li class="flex gap-4">
                            <span class="mt-1 grid size-7 shrink-0 place-items-center rounded-full bg-primary/10 text-sm font-bold text-primary" aria-hidden="true">✓</span>
                            <div>
                                <h3 class="text-lg font-bold leading-tight text-ink">{{ $item['title'] }}</h3>
                                @if (filled($item['description'] ?? null))
                                    <p class="mt-2 text-sm leading-7 text-slate-600">{{ $item['description'] }}</p>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>
