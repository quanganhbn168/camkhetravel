<?php

namespace App\Filament\Resources\ProjectCategories\Schemas;

use App\Filament\Forms\CategoryForm;
use App\Models\ProjectCategory;
use Filament\Schemas\Schema;

final class ProjectCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return CategoryForm::configure($schema, ProjectCategory::class, 'Danh mục dự án');
    }
}
