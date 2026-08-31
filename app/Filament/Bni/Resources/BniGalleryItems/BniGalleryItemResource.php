<?php

namespace App\Filament\Bni\Resources\BniGalleryItems;

use App\Filament\Bni\Resources\BniGalleryItems\Pages\ManageBniGalleryItems;
use App\Models\BniGalleryItem;
use App\Support\Bni\BniPanelAccess;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BniGalleryItemResource extends Resource
{
    protected static ?string $model = BniGalleryItem::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Thư viện ảnh';

    protected static ?int $navigationSort = 6;

    public static function getNavigationGroup(): ?string
    {
        return 'Lễ chuyển giao';
    }

    public static function canViewAny(): bool
    {
        return BniPanelAccess::canManageChapterContent();
    }

    public static function getEloquentQuery(): Builder
    {
        return BniPanelAccess::scopeChapter(parent::getEloquentQuery())->withCount('comments');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Ảnh sự kiện')->icon('heroicon-o-photo')->schema([
                Select::make('bni_event_id')->label('Sự kiện')->relationship('event', 'title', fn (Builder $query): Builder => BniPanelAccess::scopePublishedEvents($query))->searchable()->preload(),
                Select::make('bni_chapter_id')->label('Chapter')->relationship('chapter', 'name')->searchable()->preload()->visible(fn (): bool => BniPanelAccess::canManageEverything()),
                Select::make('group')->label('Nhóm hiển thị')->options(['event' => 'Theo sự kiện', 'chapter' => 'Theo chapter'])->required()->default('event'),
                TextInput::make('sort_order')->label('Thứ tự')->numeric()->default(0),
                TextInput::make('title')->label('Tiêu đề')->maxLength(255)->columnSpanFull(),
                TextInput::make('caption')->label('Chú thích')->maxLength(255)->columnSpanFull(),
                CuratorPicker::make('media_id')->label('Hình ảnh')->relationship('media', 'id')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->required()->columnSpanFull(),
                Select::make('source')->label('Nguồn ảnh')->options(BniGalleryItem::sourceOptions())->default(BniGalleryItem::SOURCE_ADMIN)->disabled(fn (): bool => ! BniPanelAccess::canManageEverything())->dehydrated(),
                Select::make('status')->label('Kiểm duyệt')->options(BniGalleryItem::statusOptions())->required()->default(BniGalleryItem::STATUS_APPROVED),
                Toggle::make('is_active')->label('Hiển thị')->default(true)->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            CuratorColumn::make('media')->label('Ảnh')->square(),
            TextColumn::make('title')->label('Tiêu đề')->searchable()->wrap(),
            TextColumn::make('group')->label('Nhóm')->badge(),
            TextColumn::make('event.title')->label('Sự kiện')->toggleable(),
            TextColumn::make('chapter.short_name')->label('Chapter')->badge()->toggleable(),
            TextColumn::make('source')->label('Nguồn')->badge()->formatStateUsing(fn (string $state): string => BniGalleryItem::sourceOptions()[$state] ?? $state),
            TextColumn::make('status')->label('Kiểm duyệt')->badge()->formatStateUsing(fn (string $state): string => BniGalleryItem::statusOptions()[$state] ?? $state),
            TextColumn::make('comments_count')->label('Bình luận')->badge(),
        ])->filters([
            SelectFilter::make('status')->label('Kiểm duyệt')->options(BniGalleryItem::statusOptions()),
            SelectFilter::make('source')->label('Nguồn ảnh')->options(BniGalleryItem::sourceOptions()),
        ])->defaultSort('sort_order')->recordActions([
            EditAction::make()->mutateDataUsing(fn (array $data): array => self::prepareUpdateData($data)),
            Action::make('rejectAndDeleteGuestUpload')
                ->label('Từ chối & xóa file')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->visible(fn (BniGalleryItem $record): bool => $record->source === BniGalleryItem::SOURCE_GUEST)
                ->requiresConfirmation()
                ->modalHeading('Từ chối ảnh và xóa file?')
                ->modalDescription('Ảnh, file gốc và các bình luận liên quan sẽ bị xóa khỏi hệ thống.')
                ->action(fn (BniGalleryItem $record) => $record->delete()),
            DeleteAction::make(),
        ]);
    }

    public static function prepareCreateData(array $data): array
    {
        $data = BniPanelAccess::prepareGalleryData($data);
        $data['uploaded_by_user_id'] = auth()->id();
        $data['approved_by_user_id'] = auth()->id();
        $data['source'] = BniPanelAccess::isChapterManager() && ! BniPanelAccess::canManageEverything()
            ? BniGalleryItem::SOURCE_CHAPTER
            : BniGalleryItem::SOURCE_ADMIN;
        $data['status'] = $data['status'] ?? BniGalleryItem::STATUS_APPROVED;

        return $data;
    }

    public static function prepareUpdateData(array $data): array
    {
        $data = BniPanelAccess::prepareGalleryData($data);

        if (($data['status'] ?? null) === BniGalleryItem::STATUS_APPROVED) {
            $data['approved_by_user_id'] = auth()->id();
        }

        return $data;
    }

    public static function getPages(): array
    {
        return ['index' => ManageBniGalleryItems::route('/')];
    }
}
