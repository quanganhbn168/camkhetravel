<?php

namespace App\Filament\Resources\PostCategories;

use App\Filament\Resources\Concerns\CategoryResource;
use App\Filament\Resources\PostCategories\Pages\CreatePostCategory;
use App\Filament\Resources\PostCategories\Pages\EditPostCategory;
use App\Filament\Resources\PostCategories\Pages\ListPostCategories;
use App\Models\PostCategory;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class PostCategoryResource extends CategoryResource
{
    protected static ?string $model = PostCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $navigationLabel = 'Chuyên mục bài viết';

    protected static ?int $navigationSort = 6;

    public static function getModelLabel(): string
    {
        return 'chuyên mục bài viết';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Chuyên mục bài viết';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPostCategories::route('/'),
            'create' => CreatePostCategory::route('/create'),
            'edit' => EditPostCategory::route('/{record}/edit'),
        ];
    }
}
