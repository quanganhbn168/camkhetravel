<?php

namespace App\Filament\Bni\Resources\BniMembers;

use App\Filament\Bni\Resources\BniMembers\Pages\CreateBniMember;
use App\Filament\Bni\Resources\BniMembers\Pages\EditBniMember;
use App\Filament\Bni\Resources\BniMembers\Pages\ListBniMembers;
use App\Models\User;
use App\Support\Bni\BniPanelAccess;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BniMemberResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Tài khoản hội viên';

    protected static ?int $navigationSort = 9;

    public static function getNavigationGroup(): ?string
    {
        return 'Cộng đồng & vận hành';
    }

    public static function canViewAny(): bool
    {
        return BniPanelAccess::canManageEverything();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('roles', fn (Builder $query) => $query->whereIn('name', ['bni_admin', 'bni_chapter_manager', 'bni_member']));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Tài khoản hội viên')->icon('heroicon-o-user')->schema([
                TextInput::make('name')->label('Họ và tên')->required()->maxLength(255)->columnSpanFull(),
                TextInput::make('email')->label('Email đăng nhập')->email()->required()->unique(ignoreRecord: true)->maxLength(255)->columnSpanFull(),
                TextInput::make('password')->label('Mật khẩu')->password()->required(fn (?User $record): bool => $record === null)->dehydrated(fn (?string $state): bool => filled($state))->columnSpanFull(),
                Select::make('bni_chapter_id')->label('Chapter')->relationship('bniChapter', 'name')->searchable()->preload()->columnSpanFull(),
                Select::make('roles')->label('Vai trò')->relationship('roles', 'name', fn (Builder $query) => $query->whereIn('name', ['bni_admin', 'bni_chapter_manager', 'bni_member']))->multiple()->preload()->required()->default(['bni_member'])->columnSpanFull(),
            ])->columns(2)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Hội viên')->searchable()->sortable(),
            TextColumn::make('email')->label('Email')->searchable(),
            TextColumn::make('bniChapter.short_name')->label('Chapter')->badge(),
            TextColumn::make('roles.name')->label('Vai trò')->badge()->separator(', '),
        ])->defaultSort('name')->recordActions([EditAction::make(), DeleteAction::make()->slideOver()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBniMembers::route('/'),
            'create' => CreateBniMember::route('/create'),
            'edit' => EditBniMember::route('/{record}/edit'),
        ];
    }
}
