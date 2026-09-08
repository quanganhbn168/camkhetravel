<?php

namespace App\Filament\Resources\ServiceCategories;

use App\Filament\Resources\Concerns\CategoryResource;
use App\Filament\Resources\ServiceCategories\Pages\CreateServiceCategory;
use App\Filament\Resources\ServiceCategories\Pages\EditServiceCategory;
use App\Filament\Resources\ServiceCategories\Pages\ListServiceCategories;
use App\Models\ServiceCategory;
use BackedEnum;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class ServiceCategoryResource extends CategoryResource
{
    protected static ?string $model = ServiceCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $navigationLabel = 'Nhóm dịch vụ';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        $schema = parent::form($schema);

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

    public static function table(Table $table): Table
    {
        $table = parent::table($table);

        return $table->columns([
            ...$table->getColumns(),
            ToggleColumn::make('is_featured')->label('Nổi bật'),
            ToggleColumn::make('is_home')->label('Trang chủ'),
        ]);
    }

    public static function getModelLabel(): string
    {
        return 'nhóm dịch vụ';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Nhóm dịch vụ';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServiceCategories::route('/'),
            'create' => CreateServiceCategory::route('/create'),
            'edit' => EditServiceCategory::route('/{record}/edit'),
        ];
    }
}
