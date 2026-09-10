<?php

namespace App\Filament\Resources\ServiceCategories\Schemas;

use App\Filament\Resources\ServiceCategories\ServiceCategoryResource;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

final class ServiceCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        $schema = ServiceCategoryResource::baseForm($schema);

        return $schema->components([
            ...$schema->getComponents(),
            Section::make('Dịch vụ media nổi bật trên trang chủ')
                ->icon(Heroicon::OutlinedHome)
                ->description('Bật cả hai tùy chọn để hiển thị danh mục. Chỉ các dịch vụ đã xuất bản và được bật Trang chủ trong danh mục này xuất hiện.')
                ->schema([
                    Toggle::make('is_featured')->label('Danh mục nổi bật')->default(false)->columnSpanFull(),
                    Toggle::make('is_home')->label('Hiển thị trên trang chủ')->default(false)->columnSpanFull(),
                ])->columns(1)->columnSpanFull(),
        ]);
    }
}
