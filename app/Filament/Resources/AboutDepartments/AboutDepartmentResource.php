<?php

namespace App\Filament\Resources\AboutDepartments;

use App\Filament\Resources\AboutDepartments\Pages\CreateAboutDepartment;
use App\Filament\Resources\AboutDepartments\Pages\EditAboutDepartment;
use App\Filament\Resources\AboutDepartments\Pages\ListAboutDepartments;
use App\Filament\Resources\AboutDepartments\Schemas\AboutDepartmentForm;
use App\Filament\Resources\AboutDepartments\Tables\AboutDepartmentsTable;
use App\Models\AboutDepartment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AboutDepartmentResource extends Resource
{
    protected static ?string $model = AboutDepartment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Phòng ban & nhân sự';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 7;

    public static function getNavigationGroup(): ?string
    {
        return 'Nội dung website';
    }

    public static function getModelLabel(): string
    {
        return 'phòng ban';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Phòng ban & nhân sự';
    }

    public static function form(Schema $schema): Schema
    {
        return AboutDepartmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AboutDepartmentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAboutDepartments::route('/'),
            'create' => CreateAboutDepartment::route('/create'),
            'edit' => EditAboutDepartment::route('/{record}/edit'),
        ];
    }
}
