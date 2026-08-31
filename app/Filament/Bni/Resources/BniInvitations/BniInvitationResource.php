<?php

namespace App\Filament\Bni\Resources\BniInvitations;

use App\Filament\Bni\Resources\BniInvitations\Pages\ManageBniInvitations;
use App\Models\BniInvitation;
use App\Support\Bni\BniPanelAccess;
use App\Support\Localization\LocalizedUrl;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BniInvitationResource extends Resource
{
    protected static ?string $model = BniInvitation::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Khách mời & RSVP';

    protected static ?string $modelLabel = 'khách mời';

    protected static ?string $pluralModelLabel = 'khách mời';

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
                Select::make('bni_event_id')->label('Sự kiện')->relationship('event', 'title', fn (Builder $query): Builder => BniPanelAccess::scopeInvitationEvents($query))->required()->searchable()->preload(),
                Select::make('bni_chapter_id')->label('Chapter phụ trách')->relationship('chapter', 'name')->required(fn (): bool => BniPanelAccess::canManageEverything())->searchable()->preload()->visible(fn (): bool => BniPanelAccess::canManageEverything()),
                TextInput::make('guest_name')->label('Tên người nhận (tuỳ chọn)')->helperText('Bỏ trống để hiển thị “Anh/Chị chủ doanh nghiệp”.')->maxLength(255)->columnSpanFull(),
                TextInput::make('invitation_code')
                    ->label('Mã thư mời & đường dẫn')
                    ->default(fn (): string => BniInvitation::newInvitationCode())
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->regex('/^[A-Za-z0-9-]+$/')
                    ->maxLength(32)
                    ->helperText('Dùng trực tiếp trong liên kết, ví dụ: /thu-moi/tm-k4x9p2q7. Không dùng tên khách trong URL.')
                    ->columnSpanFull(),
                TextInput::make('company_name')->label('Doanh nghiệp'),
                TextInput::make('position')->label('Chức danh'),
                TextInput::make('email')->label('Email')->email(),
                TextInput::make('phone')->label('Số điện thoại')->tel(),
                TextInput::make('guest_count')->label('Số người tham dự')->numeric()->minValue(1)->default(1),
                Select::make('rsvp_status')->label('Phản hồi')->options(BniInvitation::rsvpOptions())->required()->default(BniInvitation::RSVP_PENDING),
                Textarea::make('rsvp_note')->label('Ghi chú RSVP')->rows(3)->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('guest_name')->label('Khách mời')->formatStateUsing(fn (?string $state): string => filled($state) ? $state : 'Anh/Chị chủ doanh nghiệp')->searchable()->sortable(),
            TextColumn::make('invitation_code')->label('Mã thư mời')->copyable()->copyMessage('Đã sao chép mã thư mời')->searchable(),
            TextColumn::make('chapter.short_name')->label('Chapter')->badge()->toggleable(),
            TextColumn::make('company_name')->label('Doanh nghiệp')->searchable()->toggleable(),
            TextColumn::make('phone')->label('Số điện thoại')->toggleable(),
            TextColumn::make('public_url')
                ->label('Liên kết thư mời')
                ->getStateUsing(fn (BniInvitation $record): string => $record->publicUrl())
                ->copyable()
                ->copyMessage('Đã sao chép liên kết thư mời')
                ->url(fn (BniInvitation $record): string => $record->publicUrl(), shouldOpenInNewTab: true)
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('rsvp_status')->label('RSVP')->badge()->formatStateUsing(fn (string $state): string => BniInvitation::rsvpOptions()[$state] ?? $state),
            TextColumn::make('responded_at')->label('Phản hồi lúc')->dateTime('d/m/Y H:i')->toggleable(),
        ])->filters([
            SelectFilter::make('rsvp_status')->label('Phản hồi')->options(BniInvitation::rsvpOptions()),
        ])->defaultSort('created_at', 'desc')->recordActions([
            Action::make('preview')
                ->label('Xem thư mời')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn (BniInvitation $record): string => LocalizedUrl::route('bni.invitations.show', ['invitation' => $record]), true),
            EditAction::make()->mutateDataUsing(fn (array $data): array => BniPanelAccess::prepareInvitationData($data)),
            DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageBniInvitations::route('/')];
    }
}
