<?php

namespace App\Filament\Bni\Resources\BniGeneralContacts;

use App\Filament\Bni\Resources\BniGeneralContacts\Pages\CreateBniGeneralContact;
use App\Filament\Bni\Resources\BniGeneralContacts\Pages\EditBniGeneralContact;
use App\Filament\Bni\Resources\BniGeneralContacts\Pages\ListBniGeneralContacts;
use App\Models\BniContact;
use App\Support\Bni\BniPanelAccess;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BniGeneralContactResource extends Resource
{
    protected static ?string $model = BniContact::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-phone';

    protected static ?string $navigationLabel = 'Liên hệ chung';

    protected static ?string $modelLabel = 'liên hệ chung';

    protected static ?string $pluralModelLabel = 'Liên hệ chung';

    protected static ?int $navigationSort = 7;

    public static function getNavigationGroup(): ?string
    {
        return 'Sự kiện BNI';
    }

    public static function canViewAny(): bool
    {
        return BniPanelAccess::canManageEverything();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->general();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Đầu mối liên hệ chung')
                ->icon('heroicon-o-phone')
                ->description('Có thể tạo nhiều đầu mối. Để trống sự kiện nếu đây là liên hệ chung cho toàn bộ khu BNI.')
                ->schema([
                    Select::make('bni_event_id')->label('Áp dụng cho sự kiện')->relationship('event', 'title')->searchable()->preload()->columnSpanFull(),
                    TextInput::make('name')->label('Họ và tên')->required()->maxLength(255)->columnSpanFull(),
                    TextInput::make('position')->label('Chức vụ / vai trò')->maxLength(255)->columnSpanFull(),
                    TextInput::make('phone')->label('Số điện thoại')->tel()->maxLength(32)->columnSpanFull(),
                    TextInput::make('email')->label('Email')->email()->maxLength(255)->columnSpanFull(),
                    TextInput::make('zalo_url')->label('Link Zalo')->url()->maxLength(2048)->columnSpanFull(),
                    Textarea::make('note')->label('Ghi chú')->rows(3)->columnSpanFull(),
                    Toggle::make('is_primary')->label('Đầu mối chính')->default(false)->columnSpanFull(),
                    Toggle::make('is_active')->label('Hiển thị')->default(true)->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Đầu mối')->searchable()->sortable()->weight('semibold'),
                TextColumn::make('position')->label('Chức vụ')->placeholder('Chưa nhập')->toggleable(),
                TextColumn::make('event.title')->label('Sự kiện')->placeholder('Toàn bộ BNI')->toggleable(),
                TextColumn::make('phone')->label('Điện thoại')->copyable()->searchable(),
                TextColumn::make('email')->label('Email')->copyable()->toggleable(),
                ToggleColumn::make('is_primary')->label('Chính'),
                ToggleColumn::make('is_active')->label('Hiển thị'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->slideOver(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBniGeneralContacts::route('/'),
            'create' => CreateBniGeneralContact::route('/create'),
            'edit' => EditBniGeneralContact::route('/{record}/edit'),
        ];
    }
}
