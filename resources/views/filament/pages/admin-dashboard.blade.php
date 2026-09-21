<x-filament-panels::page>
    <x-filament::section heading="Truy cập nhanh" description="Chọn khu vực cần quản lý.">
        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['Dịch vụ', \App\Filament\Resources\Services\ServiceResource::getUrl('index')],
                ['Dự án', \App\Filament\Resources\Projects\ProjectResource::getUrl('index')],
                ['Sản phẩm', \App\Filament\Resources\Products\ProductResource::getUrl('index')],
                ['Bài viết', \App\Filament\Resources\Posts\PostResource::getUrl('index')],
                ['Yêu cầu tư vấn', \App\Filament\Resources\ContactRequests\ContactRequestResource::getUrl('index')],
                ['Bình luận', \App\Filament\Resources\Comments\CommentResource::getUrl('index')],
                ['Thư viện media', url('/admin/media')],
            ] as [$label, $url])
                <a href="{{ $url }}" class="rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 hover:border-primary-400 hover:text-primary-600 dark:border-white/10 dark:text-gray-200">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-panels::page>
