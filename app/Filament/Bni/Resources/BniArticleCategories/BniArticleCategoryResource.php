<?php

namespace App\Filament\Bni\Resources\BniArticleCategories;

use App\Filament\Bni\Resources\BniArticleCategories\Pages\CreateBniArticleCategory;
use App\Filament\Bni\Resources\BniArticleCategories\Pages\EditBniArticleCategory;
use App\Filament\Bni\Resources\BniArticleCategories\Pages\ListBniArticleCategories;
use App\Models\BniArticleCategory;
use App\Support\Bni\BniPanelAccess;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BniArticleCategoryResource extends Resource
{
    protected static ?string $model = BniArticleCategory::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Danh mục tin BNI';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Lễ chuyển giao';
    }

    public static function canViewAny(): bool
    {
        return BniPanelAccess::canManageChapterContent();
    }

    public static function canCreate(): bool
    {
        return BniPanelAccess::canManageEverything();
    }

    public static function canEdit(Model $record): bool
    {
        return BniPanelAccess::canManageEverything();
    }

    public static function canDelete(Model $record): bool
    {
        return BniPanelAccess::canManageEverything();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Danh mục tin BNI')
                ->icon('heroicon-o-tag')
                ->schema([
                    TextInput::make('name')
                        ->label('Tên danh mục')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (?string $state, $get, $set): void {
                            if (blank($get('slug')) && filled($state)) {
                                $set('slug', Str::slug($state));
                            }
                        })
                        ->columnSpanFull(),
                    TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255)
                        ->columnSpanFull(),
                    TextInput::make('sort_order')
                        ->label('Thứ tự tab')
                        ->numeric()
                        ->default(fn (): int => ((int) BniArticleCategory::query()->max('sort_order')) + 1)
                        ->columnSpanFull(),
                    Textarea::make('description')
                        ->label('Mô tả')
                        ->rows(3)
                        ->columnSpanFull(),
                    Toggle::make('is_active')
                        ->label('Hiển thị')
                        ->default(true)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Danh mục')->searchable()->sortable(),
                TextColumn::make('slug')->label('Slug')->copyable(),
                TextColumn::make('articles_count')->counts('articles')->label('Số bài')->sortable(),
                TextColumn::make('sort_order')->label('Thứ tự tab')->sortable(),
                IconColumn::make('is_active')->label('Hiển thị')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make()->visible(fn (): bool => BniPanelAccess::canManageEverything()),
                DeleteAction::make()->slideOver()->visible(fn (): bool => BniPanelAccess::canManageEverything()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBniArticleCategories::route('/'),
            'create' => CreateBniArticleCategory::route('/create'),
            'edit' => EditBniArticleCategory::route('/{record}/edit'),
        ];
    }
}
