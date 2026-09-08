<?php

namespace App\Filament\Resources\Concerns;

use App\Filament\Forms\SeoImageField;
use App\Support\Seo\ContentSeoFallbacks;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

abstract class CategoryResource extends Resource
{
    use UsesPrimaryKeyForRecordRoutes;

    protected static ?string $recordRouteKeyName = 'id';

    public static function getNavigationGroup(): ?string
    {
        return 'Nội dung website';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Danh mục')
                ->icon(Heroicon::OutlinedTag)
                ->schema([
                    TextInput::make('name')
                        ->label('Tên danh mục')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (?string $state, $get, $set): void {
                            if (blank($get('slug')) && ($slug = ContentSeoFallbacks::slug($state))) {
                                $set('slug', $slug);
                            }
                        })
                        ->columnSpanFull(),
                    TextInput::make('slug')
                        ->label('Đường dẫn (slug)')
                        ->maxLength(255)
                        ->formatStateUsing(fn (?string $state, $record): ?string => $state ?: $record?->slug)
                        ->columnSpanFull(),
                    TextInput::make('sort_order')->label('Thứ tự')->numeric()->default(function (): int {
                        $model = static::getModel();

                        return ((int) $model::query()->max('sort_order')) + 1;
                    }),
                    SeoImageField::make(),
                    Textarea::make('description')->label('Mô tả')->rows(3)->columnSpanFull(),
                    Toggle::make('is_active')->label('Hiển thị')->default(true)->columnSpanFull(),
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
                TextColumn::make('sort_order')->label('Thứ tự')->sortable(),
                IconColumn::make('is_active')->label('Hiển thị')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
