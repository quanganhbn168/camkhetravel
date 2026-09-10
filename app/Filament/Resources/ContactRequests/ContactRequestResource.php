<?php

namespace App\Filament\Resources\ContactRequests;

use App\Filament\Resources\ContactRequests\Pages\EditContactRequest;
use App\Filament\Resources\ContactRequests\Pages\ListContactRequests;
use App\Filament\Resources\ContactRequests\Schemas\ContactRequestForm;
use App\Filament\Resources\ContactRequests\Tables\ContactRequestsTable;
use App\Models\ContactRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ContactRequestResource extends Resource
{
    protected static ?string $model = ContactRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $navigationLabel = 'Yêu cầu tư vấn';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return 'Nội dung website';
    }

    public static function getModelLabel(): string
    {
        return 'yêu cầu tư vấn';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Yêu cầu tư vấn';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return ContactRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContactRequestsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactRequests::route('/'),
            'edit' => EditContactRequest::route('/{record}/edit'),
        ];
    }
}
