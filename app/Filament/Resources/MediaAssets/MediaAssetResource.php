<?php

namespace App\Filament\Resources\MediaAssets;

use App\Filament\Resources\MediaAssets\Pages\CreateMediaAsset;
use App\Filament\Resources\MediaAssets\Pages\EditMediaAsset;
use App\Filament\Resources\MediaAssets\Pages\ListMediaAssets;
use App\Filament\Resources\MediaAssets\Pages\ViewMediaAsset;
use App\Filament\Resources\MediaAssets\Schemas\MediaAssetForm;
use App\Filament\Resources\MediaAssets\Schemas\MediaAssetInfolist;
use App\Filament\Resources\MediaAssets\Tables\MediaAssetsTable;
use App\Models\MediaAsset;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MediaAssetResource extends Resource
{
    protected static ?string $model = MediaAsset::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $navigationLabel = 'Media nguồn';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationGroup(): ?string
    {
        return 'SEO & media';
    }

    public static function getModelLabel(): string
    {
        return 'media';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Media nguồn';
    }

    public static function form(Schema $schema): Schema
    {
        return MediaAssetForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MediaAssetInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MediaAssetsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMediaAssets::route('/'),
            'create' => CreateMediaAsset::route('/create'),
            'view' => ViewMediaAsset::route('/{record}'),
            'edit' => EditMediaAsset::route('/{record}/edit'),
        ];
    }
}
