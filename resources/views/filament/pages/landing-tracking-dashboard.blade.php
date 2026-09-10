<x-filament-panels::page>
    @php
        $data = $this->getDashboardData();
    @endphp

    <div class="space-y-6">
        <div class="fi-section rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sm:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-gray-950 dark:text-white">Bộ lọc báo cáo</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Dữ liệu first-party từ các landing page đang bật tracking.</p>
                </div>
                <button
                    type="button"
                    wire:click="resetFilters"
                    class="inline-flex items-center justify-center rounded-lg px-3 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:text-gray-200 dark:ring-gray-600 dark:hover:bg-white/5"
                >
                    Đặt lại bộ lọc
                </button>
            </div>

            <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <label class="block">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Từ ngày</span>
                    <input
                        type="date"
                        wire:model.live="dateFrom"
                        class="mt-1 block w-full rounded-lg border-0 bg-white px-3 py-2 text-sm text-gray-950 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-primary-600 dark:bg-white/5 dark:text-white dark:ring-white/20"
                    >
                </label>
                <label class="block">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Đến ngày</span>
                    <input
                        type="date"
                        wire:model.live="dateTo"
                        class="mt-1 block w-full rounded-lg border-0 bg-white px-3 py-2 text-sm text-gray-950 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-primary-600 dark:bg-white/5 dark:text-white dark:ring-white/20"
                    >
                </label>
                <label class="block">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Landing page</span>
                    <select
                        wire:model.live="landingPageId"
                        class="mt-1 block w-full rounded-lg border-0 bg-white px-3 py-2 text-sm text-gray-950 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-primary-600 dark:bg-gray-900 dark:text-white dark:ring-white/20"
                    >
                        <option value="">Tất cả landing page</option>
                        @foreach ($this->getLandingPageOptions() as $landingId => $landingTitle)
                            <option value="{{ $landingId }}">{{ $landingTitle }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Nguồn UTM</span>
                    <select
                        wire:model.live="utmSource"
                        class="mt-1 block w-full rounded-lg border-0 bg-white px-3 py-2 text-sm text-gray-950 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-primary-600 dark:bg-gray-900 dark:text-white dark:ring-white/20"
                    >
                        <option value="">Tất cả nguồn</option>
                        @foreach ($this->getUtmSourceOptions() as $sourceValue => $sourceLabel)
                            <option value="{{ $sourceValue }}">{{ $sourceLabel }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => 'Lượt xem ghi nhận', 'value' => $data['views'], 'hint' => 'page_view trong khoảng '.$data['date_label'], 'color' => 'blue'],
                ['label' => 'Phiên có mã', 'value' => $data['sessions'], 'hint' => 'distinct session_id', 'color' => 'violet'],
                ['label' => 'Lead thành công', 'value' => $data['leads'], 'hint' => 'lead_submit sau khi lưu form', 'color' => 'green'],
                ['label' => 'Tỷ lệ chuyển đổi', 'value' => number_format($data['conversion_rate'], 1, ',', '.').'%', 'hint' => 'lead / lượt xem', 'color' => 'orange'],
            ] as $stat)
                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</p>
                    <p class="mt-2 text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">{{ is_numeric($stat['value']) ? number_format((int) $stat['value']) : $stat['value'] }}</p>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ $stat['hint'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <section class="fi-section rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sm:p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-base font-semibold text-gray-950 dark:text-white">Hành trình tương tác</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Các sự kiện được ghi nhận trong khoảng đã chọn.</p>
                    </div>
                    <span class="rounded-full bg-primary-50 px-2.5 py-1 text-xs font-medium text-primary-700 dark:bg-primary-500/10 dark:text-primary-300">{{ $data['date_label'] }}</span>
                </div>

                @if ($data['event_breakdown'] === [])
                    <p class="mt-6 rounded-lg bg-gray-50 px-4 py-5 text-sm text-gray-500 dark:bg-white/5 dark:text-gray-400">Chưa có sự kiện trong bộ lọc này.</p>
                @else
                    <div class="mt-6 space-y-4">
                        @foreach ($data['event_breakdown'] as $event)
                            <div>
                                <div class="mb-1.5 flex items-center justify-between gap-4 text-sm">
                                    <span class="font-medium text-gray-700 dark:text-gray-200">{{ $event['label'] }}</span>
                                    <span class="tabular-nums text-gray-500 dark:text-gray-400">{{ number_format($event['total']) }}</span>
                                </div>
                                <div class="h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-white/10">
                                    <div class="h-full rounded-full" style="width: {{ $event['width'] }}%; background-color: {{ $event['color'] }}"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            <section class="fi-section rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sm:p-6">
                <div>
                    <h2 class="text-base font-semibold text-gray-950 dark:text-white">Nguồn traffic</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Phân theo UTM trên các lượt xem trang.</p>
                </div>

                @if ($data['source_breakdown'] === [])
                    <p class="mt-6 rounded-lg bg-gray-50 px-4 py-5 text-sm text-gray-500 dark:bg-white/5 dark:text-gray-400">Chưa có lượt xem trong bộ lọc này.</p>
                @else
                    <div class="mt-6 space-y-4">
                        @foreach ($data['source_breakdown'] as $source)
                            <div>
                                <div class="mb-1.5 flex items-center justify-between gap-4 text-sm">
                                    <span class="truncate font-medium text-gray-700 dark:text-gray-200">{{ $source['label'] }}</span>
                                    <span class="shrink-0 tabular-nums text-gray-500 dark:text-gray-400">{{ number_format($source['total']) }}</span>
                                </div>
                                <div class="h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-white/10">
                                    <div class="h-full rounded-full bg-primary-500" style="width: {{ $source['width'] }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            <section class="fi-section rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sm:p-6">
                <div>
                    <h2 class="text-base font-semibold text-gray-950 dark:text-white">Hiệu quả theo landing page</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ưu tiên các landing có nhiều lượt xem nhất.</p>
                </div>

                @if ($data['landing_breakdown'] === [])
                    <p class="mt-6 rounded-lg bg-gray-50 px-4 py-5 text-sm text-gray-500 dark:bg-white/5 dark:text-gray-400">Chưa có dữ liệu landing trong bộ lọc này.</p>
                @else
                    <div class="mt-5 overflow-x-auto">
                        <table class="w-full min-w-[34rem] text-left text-sm">
                            <thead class="border-b border-gray-200 text-xs uppercase text-gray-500 dark:border-white/10 dark:text-gray-400">
                                <tr>
                                    <th class="pb-3 pr-4 font-medium">Landing page</th>
                                    <th class="pb-3 pr-4 text-right font-medium">Lượt xem</th>
                                    <th class="pb-3 pr-4 text-right font-medium">Lead</th>
                                    <th class="pb-3 text-right font-medium">CVR</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                                @foreach ($data['landing_breakdown'] as $landing)
                                    <tr>
                                        <td class="max-w-[18rem] truncate py-3 pr-4 font-medium text-gray-800 dark:text-gray-200" title="{{ $landing['title'] }}">{{ $landing['title'] }}</td>
                                        <td class="py-3 pr-4 text-right tabular-nums text-gray-600 dark:text-gray-300">{{ number_format($landing['views']) }}</td>
                                        <td class="py-3 pr-4 text-right tabular-nums text-gray-600 dark:text-gray-300">{{ number_format($landing['leads']) }}</td>
                                        <td class="py-3 text-right font-medium tabular-nums text-primary-700 dark:text-primary-300">{{ number_format($landing['conversion_rate'], 1, ',', '.') }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <section class="fi-section rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sm:p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-base font-semibold text-gray-950 dark:text-white">Lead gần đây</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Lead đã được lưu thành công từ landing page.</p>
                    </div>
                    <a href="{{ $this->getLeadsUrl() }}" class="shrink-0 text-sm font-semibold text-primary-600 hover:text-primary-500">Xem tất cả</a>
                </div>

                @if ($data['recent_leads']->isEmpty())
                    <p class="mt-6 rounded-lg bg-gray-50 px-4 py-5 text-sm text-gray-500 dark:bg-white/5 dark:text-gray-400">Chưa có lead trong bộ lọc này.</p>
                @else
                    <div class="mt-5 space-y-3">
                        @foreach ($data['recent_leads'] as $lead)
                            <div class="flex items-start justify-between gap-4 rounded-lg border border-gray-200 px-3 py-3 dark:border-white/10">
                                <div class="min-w-0">
                                    <p class="truncate font-medium text-gray-900 dark:text-white">{{ $lead->name ?: 'Khách chưa nhập tên' }}</p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $lead->phone ?: 'Chưa có số điện thoại' }} · {{ $lead->landingPage?->title ?: 'Landing page' }}</p>
                                </div>
                                <div class="shrink-0 text-right">
                                    <span class="inline-flex rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700 dark:bg-white/10 dark:text-gray-200">{{ $lead->utm_source ?: 'direct' }}</span>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $lead->created_at?->format('d/m H:i') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>

        <div class="rounded-xl border border-primary-200 bg-primary-50/70 p-4 text-sm text-primary-900 dark:border-primary-500/20 dark:bg-primary-500/10 dark:text-primary-100">
            <p class="font-semibold">Cách đọc số liệu</p>
            <p class="mt-1 leading-6">Lượt xem là các sự kiện <code class="rounded bg-white/60 px-1 py-0.5 text-xs dark:bg-white/10">page_view</code> đã ghi nhận. Trình duyệt hiện không gửi lại lượt xem khi anh chỉ bấm F5 trong cùng tab và session; dashboard vẫn giữ số liệu theo phiên, nguồn UTM và landing page để đối chiếu với GA4/GTM sau này.</p>
        </div>
    </div>
</x-filament-panels::page>
