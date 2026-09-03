<?php

namespace App\Filament\Bni\Resources\BniChapterContacts;

use App\Filament\Bni\Resources\BniChapterContacts\Pages\CreateBniChapterContact;
use App\Filament\Bni\Resources\BniChapterContacts\Pages\EditBniChapterContact;
use App\Filament\Bni\Resources\BniChapterContacts\Pages\ListBniChapterContacts;
use App\Models\BniContact;
use App\Support\Bni\BniPanelAccess;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BniChapterContactResource extends Resource
{
    protected static ?string $model = BniContact::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationLabel = 'Liên hệ Chapter';

    protected static ?string $modelLabel = 'liên hệ Chapter';

    protected static ?string $pluralModelLabel = 'Liên hệ Chapter';

    protected static ?int $navigationSort = 6;

    public static function getNavigationGroup(): ?string
    {
        return 'Sự kiện BNI';
    }

    public static function canViewAny(): bool
    {
        return BniPanelAccess::canManageEverything();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->forChapters()->with('chapter');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Đầu mối của Chapter')
                ->icon('heroicon-o-identification')
                ->description('Mỗi Chapter có thể có nhiều người liên hệ; một người được đánh dấu là đầu mối chính cho thư mời.')
                ->schema([
                    Select::make('bni_chapter_id')->label('Chapter')->relationship('chapter', 'name')->required()->searchable()->preload()->columnSpanFull(),
                    TextInput::make('name')->label('Họ và tên')->required()->maxLength(255)->columnSpanFull(),
                    TextInput::make('position')->label('Chức vụ / vai trò')->maxLength(255)->columnSpanFull(),
                    TextInput::make('phone')->label('Số điện thoại')->tel()->maxLength(32)->columnSpanFull(),
                    TextInput::make('email')->label('Email')->email()->maxLength(255)->columnSpanFull(),
                    TextInput::make('zalo_url')->label('Link Zalo')->url()->maxLength(2048)->columnSpanFull(),
                    Textarea::make('note')->label('Ghi chú')->rows(3)->columnSpanFull(),
                    Toggle::make('is_primary')->label('Đầu mối chính trên thư mời')->default(false)->columnSpanFull(),
                    Toggle::make('is_active')->label('Hiển thị')->default(true)->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('chapter.name')->label('Chapter')->searchable()->sortable(),
                TextColumn::make('name')->label('Người liên hệ')->searchable()->sortable()->weight('semibold'),
                TextColumn::make('position')->label('Chức vụ')->placeholder('Chưa nhập')->toggleable(),
                TextColumn::make('phone')->label('Điện thoại')->copyable()->searchable(),
                TextColumn::make('email')->label('Email')->copyable()->toggleable(),
                ToggleColumn::make('is_primary')->label('Đầu mối chính'),
                ToggleColumn::make('is_active')->label('Hiển thị'),
            ])
            ->filters([
                SelectFilter::make('bni_chapter_id')->label('Chapter')->relationship('chapter', 'name'),
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
            'index' => ListBniChapterContacts::route('/'),
            'create' => CreateBniChapterContact::route('/create'),
            'edit' => EditBniChapterContact::route('/{record}/edit'),
        ];
    }
}
