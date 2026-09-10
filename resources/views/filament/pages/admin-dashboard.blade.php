<x-filament-panels::page>
    @php
        $data = $this->getWebsiteOverviewData();
    @endphp

    <div class="space-y-6">
        <section class="overflow-hidden rounded-2xl bg-gradient-to-br from-primary-700 via-primary-600 to-primary-500 p-6 text-white shadow-sm sm:p-8">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-white/70">Trung tâm quản trị</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight sm:text-3xl">Tổng quan website</h2>
                    <p class="mt-3 text-sm leading-6 text-white/80 sm:text-base">Quản lý nội dung, thư viện media và các yêu cầu tư vấn của DVTEC trong một màn hình chung.</p>
                </div>
                <div class="rounded-xl bg-white/10 px-4 py-3 text-sm text-white/90 ring-1 ring-inset ring-white/15">
                    <span class="block text-xs uppercase tracking-wide text-white/60">Cập nhật lúc</span>
                    <span class="mt-1 block font-semibold">{{ now()->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </section>

        <section>
            <div class="mb-3">
                <h2 class="text-base font-semibold text-gray-950 dark:text-white">Vận hành cần chú ý</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Các mục quản trị thường cần kiểm tra trong ngày.</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                @foreach ($data['operations'] as $operation)
                    <a href="{{ $operation['url'] }}" class="flex items-center justify-between gap-4 rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 transition hover:-translate-y-0.5 hover:ring-primary-300 dark:bg-gray-900 dark:ring-white/10 dark:hover:ring-primary-500/40">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-gray-700 dark:text-gray-200">{{ $operation['label'] }}</p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $operation['hint'] }}</p>
                        </div>
                        <span class="shrink-0 text-2xl font-semibold tabular-nums text-primary-600 dark:text-primary-400">{{ number_format($operation['value']) }}</span>
                    </a>
                @endforeach
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-5">
            <x-filament::section
                heading="Nội dung cập nhật gần đây"
                description="Mở nhanh bản ghi vừa được chỉnh sửa."
                class="xl:col-span-3"
            >
                <x-slot name="afterHeader">
                    <a href="{{ \App\Filament\Resources\Services\ServiceResource::getUrl('index') }}" class="text-sm font-semibold text-primary-600 hover:text-primary-500">Quản lý nội dung</a>
                </x-slot>

                @if ($data['recent_content']->isEmpty())
                    <p class="mt-6 rounded-lg bg-gray-50 px-4 py-5 text-sm text-gray-500 dark:bg-white/5 dark:text-gray-400">Chưa có nội dung trong hệ thống.</p>
                @else
                    <div class="mt-5 divide-y divide-gray-100 dark:divide-white/5">
                        @foreach ($data['recent_content'] as $content)
                            <a href="{{ $content['url'] }}" class="flex items-center justify-between gap-4 py-3 first:pt-0 last:pb-0">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="shrink-0 text-xs font-medium text-primary-600 dark:text-primary-400">{{ $content['label'] }}</span>
                                        <span class="truncate text-sm font-medium text-gray-900 hover:text-primary-600 dark:text-white dark:hover:text-primary-400">{{ $content['title'] }}</span>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Cập nhật {{ $content['updated_at']?->format('d/m/Y H:i') ?: 'chưa rõ thời gian' }}</p>
                                </div>
                                <span @class([
                                    'shrink-0 rounded-full px-2 py-1 text-xs font-medium',
                                    'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-300' => $content['status'] === 'published',
                                    'bg-gray-100 text-gray-600 dark:bg-white/10 dark:text-gray-300' => $content['status'] !== 'published',
                                ])>{{ $content['status_label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </x-filament::section>

            <x-filament::section
                heading="Yêu cầu tư vấn gần đây"
                description="Lead mới nhất từ website."
                class="xl:col-span-2"
            >
                <x-slot name="afterHeader">
                    <a href="{{ \App\Filament\Resources\ContactRequests\ContactRequestResource::getUrl('index') }}" class="text-sm font-semibold text-primary-600 hover:text-primary-500">Xem tất cả</a>
                </x-slot>

                @if ($data['recent_leads']->isEmpty())
                    <p class="mt-6 rounded-lg bg-gray-50 px-4 py-5 text-sm text-gray-500 dark:bg-white/5 dark:text-gray-400">Chưa có yêu cầu tư vấn.</p>
                @else
                    <div class="mt-5 space-y-3">
                        @foreach ($data['recent_leads'] as $lead)
                            <div class="rounded-lg border border-gray-200 px-3 py-3 dark:border-white/10">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate font-medium text-gray-900 dark:text-white">{{ $lead->name ?: 'Khách chưa nhập tên' }}</p>
                                        <p class="mt-1 truncate text-xs text-gray-500 dark:text-gray-400">{{ $lead->phone ?: 'Chưa có số điện thoại' }}</p>
                                    </div>
                                    <span @class([
                                        'shrink-0 rounded-full px-2 py-1 text-xs font-medium',
                                        'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-300' => $lead->status === 'new',
                                        'bg-gray-100 text-gray-600 dark:bg-white/10 dark:text-gray-300' => $lead->status !== 'new',
                                    ])>{{ match ($lead->status) {
                                        'new' => 'Mới',
                                        'contacted' => 'Đã liên hệ',
                                        'qualified' => 'Tiềm năng',
                                        'closed' => 'Đã xử lý',
                                        default => $lead->status ?: 'Chưa rõ',
                                    } }}</span>
                                </div>
                                <p class="mt-2 truncate text-xs text-gray-500 dark:text-gray-400">{{ $lead->service?->title ?: ($lead->landingPage?->title ?: 'Nguồn website') }} · {{ $lead->created_at?->format('d/m H:i') }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-filament::section>
        </div>

    </div>
</x-filament-panels::page>
