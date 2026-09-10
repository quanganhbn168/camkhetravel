<?php

namespace App\Filament\Resources\ProductCategories\Schemas;

use App\Filament\Forms\SeoFields;
use App\Support\Seo\ContentSeoFallbacks;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

final class ProductCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Danh mục sản phẩm')
                ->icon(Heroicon::OutlinedFolder)
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
                    TextInput::make('slug')->label('Đường dẫn (slug)')->maxLength(255)->columnSpanFull(),
                    Textarea::make('description')->label('Mô tả')->rows(3)->columnSpanFull(),
                    TextInput::make('sort_order')->label('Thứ tự')->numeric()->default(0),
                    Toggle::make('is_active')->label('Hiển thị')->default(true),
                ])
                ->columns(2),
            Section::make('SEO')->icon(Heroicon::OutlinedMagnifyingGlass)->schema(SeoFields::make('name', 'description'))->columns(2),
        ]);
    }
}
