<x-filament-widgets::widget class="fi-wi-landing-tracking-comparison">
    <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sm:p-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 class="text-base font-semibold text-gray-950 dark:text-white">Tracking landing mới</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Có dữ liệu hôm nay. Mỗi dòng dưới đây là một loại event riêng, không cộng dồn chung.</p>
            </div>
            <span class="inline-flex w-fit rounded-full bg-primary-50 px-2.5 py-1 text-xs font-medium text-primary-700 dark:bg-primary-500/10 dark:text-primary-300">Tổng quan nhanh</span>
        </div>

        <div class="mt-5 grid gap-4 md:grid-cols-3">
            @foreach ([$summary['today'], $summary['week'], $summary['month']] as $period)
                @php
                    $delta = $period['delta'];
                    $percent = $period['percent'];
                    $deltaText = ($delta > 0 ? '+' : '').number_format($delta);
                    $percentText = $percent === null
                        ? ($period['current'] > 0 ? 'Mới phát sinh' : 'Chưa phát sinh')
                        : (($percent > 0 ? '+' : '').number_format($percent, 1, ',', '.').'%');
                @endphp
                <div class="rounded-xl border border-gray-200 bg-gray-50/70 p-4 dark:border-white/10 dark:bg-white/[0.03]">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $period['label'] }}</p>
                    <p class="mt-2 text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">{{ number_format($period['current']) }}</p>
                    <p class="mt-2 text-xs {{ $delta > 0 ? 'text-success-600 dark:text-success-400' : ($delta < 0 ? 'text-danger-600 dark:text-danger-400' : 'text-gray-500 dark:text-gray-400') }}">
                        {{ $deltaText }}{{ $period['percent'] !== null ? ' ('.$percentText.')' : ' · '.$percentText }} so với {{ $period['comparison_label'] }}
                    </p>
                </div>
            @endforeach
        </div>

        @if ($summary['event_breakdown'] !== [])
            <div class="mt-6">
                <div class="mb-3">
                    <h3 class="text-sm font-semibold text-gray-950 dark:text-white">Chi tiết theo loại event</h3>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Số bên trái là kỳ hiện tại; số bên phải là kỳ đối chiếu.</p>
                </div>
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-white/10">
                    <table class="w-full min-w-[56rem] text-left text-sm">
                        <thead class="border-b border-gray-200 bg-gray-50 text-xs text-gray-500 dark:border-white/10 dark:bg-white/[0.03] dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3 font-medium">Loại event</th>
                                <th class="px-4 py-3 text-right font-medium">Hôm nay / hôm qua</th>
                                <th class="px-4 py-3 text-right font-medium">Tuần này / tuần trước</th>
                                <th class="px-4 py-3 text-right font-medium">Tháng này / tháng trước</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                            @foreach ($summary['event_breakdown'] as $event)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="border-l-2 pl-3" style="border-color: {{ $event['color'] }}">
                                            <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $event['label'] }}</p>
                                            <code class="text-[11px] text-gray-500 dark:text-gray-400">{{ $event['event_name'] }}</code>
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $event['description'] }}</p>
                                        </div>
                                    </td>
                                    @foreach ([$event['today'], $event['week'], $event['month']] as $comparison)
                                        @php
                                            $comparisonDelta = $comparison['delta'];
                                            $comparisonPercent = $comparison['percent'];
                                            $comparisonDeltaText = ($comparisonDelta > 0 ? '+' : '').number_format($comparisonDelta);
                                            $comparisonPercentText = $comparisonPercent === null
                                                ? ($comparison['current'] > 0 ? 'Mới phát sinh' : 'Chưa phát sinh')
                                                : (($comparisonPercent > 0 ? '+' : '').number_format($comparisonPercent, 1, ',', '.').'%');
                                        @endphp
                                        <td class="px-4 py-3 text-right align-top">
                                            <p class="font-semibold tabular-nums text-gray-900 dark:text-white">
                                                {{ number_format($comparison['current']) }}
                                                <span class="font-normal text-gray-400">/ {{ number_format($comparison['previous']) }}</span>
                                            </p>
                                            <p class="mt-1 text-xs {{ $comparisonDelta > 0 ? 'text-success-600 dark:text-success-400' : ($comparisonDelta < 0 ? 'text-danger-600 dark:text-danger-400' : 'text-gray-500 dark:text-gray-400') }}">
                                                {{ $comparisonDeltaText }}{{ $comparison['percent'] !== null ? ' ('.$comparisonPercentText.')' : ' · '.$comparisonPercentText }}
                                            </p>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-filament-widgets::widget>
