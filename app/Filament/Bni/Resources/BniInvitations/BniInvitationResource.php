<?php

namespace App\Filament\Bni\Resources\BniInvitations;

use App\Filament\Bni\Resources\BniInvitations\Pages\ManageBniInvitations;
use App\Models\BniInvitation;
use App\Support\Bni\BniPanelAccess;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class BniInvitationResource extends Resource
{
    protected static ?string $model = BniInvitation::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Khách mời & RSVP';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return 'Cộng đồng & vận hành';
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
            Section::make('Thông tin khách mời')->icon('heroicon-o-user')->schema([
                Select::make('bni_event_id')->label('Sự kiện')->relationship('event', 'title')->required()->searchable()->preload(),
                Select::make('bni_chapter_id')->label('Chapter phụ trách')->relationship('chapter', 'name')->searchable()->preload()->visible(fn (): bool => BniPanelAccess::canManageEverything()),
                TextInput::make('guest_name')->label('Họ và tên')->required()->maxLength(255)->live(onBlur: true)->afterStateUpdated(function (?string $state, $get, $set): void {
                    if (blank($get('slug')) && filled($state)) {
                        $set('slug', Str::slug($state));
                    }
                })->columnSpanFull(),
                TextInput::make('slug')->label('Slug thư mời')->required()->maxLength(255)->columnSpanFull(),
                TextInput::make('company_name')->label('Doanh nghiệp'),
                TextInput::make('position')->label('Chức danh'),
                TextInput::make('email')->label('Email')->email(),
                TextInput::make('phone')->label('Số điện thoại')->tel(),
                TextInput::make('invitation_code')->label('Mã thư mời')->maxLength(32),
                TextInput::make('guest_count')->label('Số người tham dự')->numeric()->minValue(1)->default(1),
                Select::make('rsvp_status')->label('Phản hồi')->options(BniInvitation::rsvpOptions())->required()->default(BniInvitation::RSVP_PENDING),
                Textarea::make('rsvp_note')->label('Ghi chú RSVP')->rows(3)->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('guest_name')->label('Khách mời')->searchable()->sortable(),
            TextColumn::make('chapter.short_name')->label('Chapter')->badge()->toggleable(),
            TextColumn::make('company_name')->label('Doanh nghiệp')->searchable()->toggleable(),
            TextColumn::make('phone')->label('Số điện thoại')->toggleable(),
            TextColumn::make('rsvp_status')->label('RSVP')->badge()->formatStateUsing(fn (string $state): string => BniInvitation::rsvpOptions()[$state] ?? $state),
            TextColumn::make('responded_at')->label('Phản hồi lúc')->dateTime('d/m/Y H:i')->toggleable(),
        ])->filters([
            SelectFilter::make('rsvp_status')->label('Phản hồi')->options(BniInvitation::rsvpOptions()),
        ])->defaultSort('created_at', 'desc')->recordActions([
            EditAction::make()->mutateDataUsing(fn (array $data): array => BniPanelAccess::forceChapter($data)),
            DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageBniInvitations::route('/')];
    }
}
