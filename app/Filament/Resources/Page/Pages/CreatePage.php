<?php

namespace App\Filament\Resources\Page\Pages;

use App\Filament\Resources\Page\PageResource;
use App\Models\Page;
use Filament\Resources\Pages\CreateRecord;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Trang tạo từ admin luôn là trang tự tạo; trang hệ thống chỉ có sẵn từ seeder.
        $data['type'] = Page::TYPE_CUSTOM;

        return $data;
    }
}
