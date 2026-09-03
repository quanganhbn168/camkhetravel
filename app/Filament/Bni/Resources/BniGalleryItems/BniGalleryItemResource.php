<?php

namespace App\Filament\Bni\Resources\BniGalleryItems;

use App\Filament\Bni\Resources\BniGalleryItems\Pages\CreateBniGalleryItem;
use App\Filament\Bni\Resources\BniGalleryItems\Pages\EditBniGalleryItem;
use App\Filament\Bni\Resources\BniGalleryItems\Pages\ListBniGalleryItems;
use App\Models\BniActivity;
use App\Models\BniGalleryItem;
use App\Support\Bni\BniMediaService;
use App\Support\Bni\BniPanelAccess;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BniGalleryItemResource extends Resource
{
    protected static ?string $model = BniGalleryItem::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Thư viện ảnh';

    protected static ?string $modelLabel = 'ảnh sự kiện';

    protected static ?string $pluralModelLabel = 'Thư viện ảnh';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Hình ảnh';
    }

    public static function canViewAny(): bool
    {
        return BniPanelAccess::canManageChapterContent();
    }

    public static function getEloquentQuery(): Builder
    {
        return BniPanelAccess::scopeChapter(parent::getEloquentQuery())
            ->with(['activity', 'event'])
            ->withCount('comments');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Ảnh sự kiện')->icon('heroicon-o-photo')->schema([
                TextInput::make('title')->label('Tiêu đề')->maxLength(255)->columnSpanFull(),
                Select::make('bni_event_id')
                    ->label('Sự kiện tổ chức')
                    ->relationship('event', 'title', fn (Builder $query): Builder => BniPanelAccess::scopePublishedEvents($query))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(fn ($set) => $set('bni_activity_id', null))
                    ->columnSpanFull(),
                Select::make('bni_activity_id')
                    ->label('Album ảnh')
                    ->options(fn ($get): array => BniActivity::query()
                        ->where('bni_event_id', $get('bni_event_id'))
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->orderBy('title')
                        ->pluck('title', 'id')
                        ->all())
                    ->helperText('Tạo và quản lý nhóm ảnh tại mục Album ảnh, ví dụ “Trước lễ chuyển giao” hoặc “Trong Gala Dinner”.')
                    ->required()
                    ->searchable()
                    ->columnSpanFull(),
                TextInput::make('sort_order')->label('Thứ tự')->numeric()->default(0)->columnSpanFull(),
                TextInput::make('caption')->label('Chú thích')->maxLength(255)->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('image')->label('Hình ảnh')->collection('image')->conversion(BniMediaService::WEBP_CONVERSION)->disk('public')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->required()->columnSpanFull(),
                Select::make('source')->label('Nguồn ảnh')->options(BniGalleryItem::sourceOptions())->default(BniGalleryItem::SOURCE_ADMIN)->disabled(fn (): bool => ! BniPanelAccess::canManageEverything())->dehydrated()->columnSpanFull(),
                Select::make('status')->label('Kiểm duyệt')->options(BniGalleryItem::statusOptions())->required()->default(BniGalleryItem::STATUS_APPROVED)->columnSpanFull(),
                Toggle::make('is_active')->label('Hiển thị')->default(true)->columnSpanFull(),
            ])->columns(2)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Stack::make([
                SpatieMediaLibraryImageColumn::make('image')
                    ->label('Ảnh')
                    ->collection('image')
                    ->conversion(BniMediaService::WEBP_CONVERSION)
                    ->imageWidth('100%')
                    ->imageHeight(220)
                    ->extraImgAttributes(['class' => 'w-full rounded-xl object-cover']),
                TextColumn::make('title')->label('Tiêu đề')->searchable()->wrap()->placeholder('Ảnh chưa có tiêu đề'),
                TextColumn::make('activity.title')->label('Album')->badge()->placeholder('Chưa chọn album'),
                TextColumn::make('event.title')->label('Sự kiện')->toggleable(),
                SelectColumn::make('status')->label('Kiểm duyệt')->options(BniGalleryItem::statusOptions()),
                ToggleColumn::make('is_active')->label('Hiển thị'),
                TextColumn::make('comments_count')->label('Bình luận')->badge(),
            ])->space(3),
        ])->filters([
            SelectFilter::make('bni_activity_id')
                ->label('Album ảnh')
                ->relationship('activity', 'title')
                ->searchable()
                ->preload(),
            SelectFilter::make('status')->label('Kiểm duyệt')->options(BniGalleryItem::statusOptions()),
            SelectFilter::make('source')->label('Nguồn ảnh')->options(BniGalleryItem::sourceOptions()),
        ])->contentGrid([
            'md' => 2,
            'xl' => 3,
            '2xl' => 4,
        ])->defaultSort('sort_order')->recordActions([
            EditAction::make()->mutateDataUsing(fn (array $data): array => self::prepareUpdateData($data)),
            Action::make('rejectAndDeleteGuestUpload')
                ->label('Từ chối & xóa file')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->visible(fn (BniGalleryItem $record): bool => $record->source === BniGalleryItem::SOURCE_GUEST)
                ->requiresConfirmation()
                ->slideOver()
                ->modalHeading('Từ chối ảnh và xóa file?')
                ->modalDescription('Ảnh, file gốc và các bình luận liên quan sẽ bị xóa khỏi hệ thống.')
                ->action(fn (BniGalleryItem $record) => $record->delete()),
            DeleteAction::make()->slideOver(),
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
        return [
            'index' => ListBniGalleryItems::route('/'),
            'create' => CreateBniGalleryItem::route('/create'),
            'edit' => EditBniGalleryItem::route('/{record}/edit'),
        ];
    }
}
