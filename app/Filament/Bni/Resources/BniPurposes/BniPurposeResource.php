<?php

namespace App\Filament\Bni\Resources\BniPurposes;

use App\Filament\Bni\Resources\BniPurposes\Pages\CreateBniPurpose;
use App\Filament\Bni\Resources\BniPurposes\Pages\EditBniPurpose;
use App\Filament\Bni\Resources\BniPurposes\Pages\ListBniPurposes;
use App\Models\BniPurpose;
use App\Support\Bni\BniPanelAccess;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BniPurposeResource extends Resource
{
    protected static ?string $model = BniPurpose::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationLabel = 'Mục đích sự kiện';

    protected static ?string $modelLabel = 'mục đích sự kiện';

    protected static ?string $pluralModelLabel = 'Mục đích sự kiện';

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return 'Lễ chuyển giao';
    }

    public static function canViewAny(): bool
    {
        return BniPanelAccess::canManageEverything();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Mục đích sự kiện')
                ->icon('heroicon-o-sparkles')
                ->description('Mỗi bản ghi là một thông điệp mục đích hiển thị trên trang sự kiện.')
                ->schema([
                    Select::make('bni_event_id')->label('Sự kiện')->relationship('event', 'title')->required()->searchable()->preload()->columnSpanFull(),
                    TextInput::make('title')->label('Tiêu đề')->required()->maxLength(255)->columnSpanFull(),
                    Textarea::make('description')->label('Mô tả')->rows(3)->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Mục đích')->searchable()->wrap(),
                TextColumn::make('event.title')->label('Sự kiện')->sortable(),
                TextColumn::make('description')->label('Mô tả')->limit(70)->wrap(),
            ])
            ->filters([
                SelectFilter::make('bni_event_id')->label('Sự kiện')->relationship('event', 'title'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->slideOver(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBniPurposes::route('/'),
            'create' => CreateBniPurpose::route('/create'),
            'edit' => EditBniPurpose::route('/{record}/edit'),
        ];
    }
}
