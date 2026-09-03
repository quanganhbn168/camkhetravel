<?php

namespace App\Filament\Bni\Resources\BniRegistrations;

use App\Filament\Bni\Resources\BniRegistrations\Pages\CreateBniRegistration;
use App\Filament\Bni\Resources\BniRegistrations\Pages\EditBniRegistration;
use App\Filament\Bni\Resources\BniRegistrations\Pages\ListBniRegistrations;
use App\Models\BniRegistration;
use App\Support\Bni\BniPanelAccess;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BniRegistrationResource extends Resource
{
    protected static ?string $model = BniRegistration::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationLabel = 'Đăng ký tham dự';

    protected static ?string $modelLabel = 'đăng ký tham dự';

    protected static ?string $pluralModelLabel = 'Đăng ký tham dự';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return 'Thư mời & vận hành';
    }

    public static function canViewAny(): bool
    {
        return BniPanelAccess::canManageChapterContent();
    }

    public static function getEloquentQuery(): Builder
    {
        return BniPanelAccess::scopeChapter(parent::getEloquentQuery());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Đăng ký tham dự')->icon('heroicon-o-ticket')->schema([
                Select::make('bni_event_id')->label('Sự kiện')->relationship('event', 'title', fn (Builder $query): Builder => BniPanelAccess::scopePublishedEvents($query))->required()->searchable()->preload()->columnSpanFull(),
                Select::make('bni_chapter_id')->label('Chapter')->relationship('chapter', 'name')->searchable()->preload()->visible(fn (): bool => BniPanelAccess::canManageEverything())->columnSpanFull(),
                Select::make('bni_invitation_id')->label('Khách mời')->relationship('invitation', 'guest_name', fn (Builder $query): Builder => BniPanelAccess::scopeChapter($query))->searchable()->preload()->columnSpanFull(),
                Select::make('status')->label('Trạng thái')->options(BniRegistration::statusOptions())->required()->default(BniRegistration::STATUS_PENDING)->columnSpanFull(),
                TextInput::make('full_name')->label('Họ và tên')->required()->maxLength(255)->columnSpanFull(),
                TextInput::make('phone')->label('Số điện thoại')->required()->tel()->columnSpanFull(),
                TextInput::make('email')->label('Email')->email()->columnSpanFull(),
                TextInput::make('team_name')->label('Tên đội')->columnSpanFull(),
                TextInput::make('skill_level')->label('Trình độ')->columnSpanFull(),
                DateTimePicker::make('checked_in_at')->label('Check-in lúc')->columnSpanFull(),
                Textarea::make('note')->label('Ghi chú')->rows(3)->columnSpanFull(),
            ])->columns(2)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('full_name')->label('Vận động viên')->searchable()->sortable(),
            TextColumn::make('chapter.short_name')->label('Chapter')->badge()->toggleable(),
            TextColumn::make('team_name')->label('Đội')->toggleable(),
            TextColumn::make('phone')->label('Số điện thoại')->toggleable(),
            SelectColumn::make('status')->label('Trạng thái')->options(BniRegistration::statusOptions()),
            TextColumn::make('checked_in_at')->label('Check-in')->dateTime('d/m/Y H:i')->toggleable(),
        ])->filters([
            SelectFilter::make('status')->label('Trạng thái')->options(BniRegistration::statusOptions()),
        ])->defaultSort('created_at', 'desc')->recordActions([
            EditAction::make(),
            DeleteAction::make()->slideOver(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBniRegistrations::route('/'),
            'create' => CreateBniRegistration::route('/create'),
            'edit' => EditBniRegistration::route('/{record}/edit'),
        ];
    }
}
