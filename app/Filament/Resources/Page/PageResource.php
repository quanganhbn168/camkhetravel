<?php

namespace App\Filament\Resources\Page;

use App\Filament\Resources\Page\Pages\CreatePage;
use App\Filament\Resources\Page\Pages\EditPage;
use App\Filament\Resources\Page\Pages\ListPages;
use App\Filament\Resources\Page\Schemas\PageForm;
use App\Filament\Resources\Page\Tables\PagesTable;
use App\Models\Page;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentDuplicate;

    protected static ?string $navigationLabel = 'Trang';

    protected static ?string $slug = 'pages';

    protected static ?string $modelLabel = 'trang';

    protected static ?string $pluralModelLabel = 'Trang';

    protected static ?string $recordTitleAttribute = 'title';

    protected static string|UnitEnum|null $navigationGroup = 'Nội dung website';

    protected static ?int $navigationSort = 0;

    public static function form(Schema $schema): Schema
    {
        return PageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PagesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }
}
