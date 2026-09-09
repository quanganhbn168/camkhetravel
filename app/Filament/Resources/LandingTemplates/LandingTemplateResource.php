<?php

namespace App\Filament\Resources\LandingTemplates;

use App\Filament\Resources\LandingTemplates\Pages\EditLandingTemplate;
use App\Filament\Resources\LandingTemplates\Pages\ListLandingTemplates;
use App\Filament\Resources\LandingTemplates\Schemas\LandingTemplateForm;
use App\Filament\Resources\LandingTemplates\Tables\LandingTemplatesTable;
use App\Models\LandingTemplate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LandingTemplateResource extends Resource
{
    protected static ?string $model = LandingTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

    protected static ?string $navigationLabel = 'Kho template landing';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Landingpage';
    }

    public static function getModelLabel(): string
    {
        return 'template landing';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Kho template landing';
    }

    public static function form(Schema $schema): Schema
    {
        return LandingTemplateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LandingTemplatesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLandingTemplates::route('/'),
            'edit' => EditLandingTemplate::route('/{record}/edit'),
        ];
    }
}
