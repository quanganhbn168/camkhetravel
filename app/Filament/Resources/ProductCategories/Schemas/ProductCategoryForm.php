<?php

namespace App\Filament\Resources\ProductCategories\Schemas;

use App\Filament\Forms\CategoryForm;
use App\Models\ProductCategory;
use Filament\Schemas\Schema;

final class ProductCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return CategoryForm::configure($schema, ProductCategory::class, 'Danh mục sản phẩm');
    }
}
