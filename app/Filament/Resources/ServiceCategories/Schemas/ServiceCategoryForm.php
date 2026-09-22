<?php

namespace App\Filament\Resources\ServiceCategories\Schemas;

use App\Filament\Forms\CategoryForm;
use App\Models\ServiceCategory;
use Filament\Schemas\Schema;

final class ServiceCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return CategoryForm::configure($schema, ServiceCategory::class, 'Danh mục dịch vụ');
    }
}
