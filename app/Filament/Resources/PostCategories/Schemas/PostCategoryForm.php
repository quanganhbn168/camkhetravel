<?php

namespace App\Filament\Resources\PostCategories\Schemas;

use App\Filament\Forms\CategoryForm;
use App\Models\PostCategory;
use Filament\Schemas\Schema;

final class PostCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return CategoryForm::configure($schema, PostCategory::class, 'Chuyên mục bài viết');
    }
}
