<?php

namespace App\Filament\Bni\Resources\BniGalleryItems\Pages;

use App\Filament\Bni\Resources\BniGalleryItems\BniGalleryItemResource;
use App\Models\BniGalleryItem;
use App\Support\Bni\BniPanelAccess;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Section;

class ManageBniGalleryItems extends ManageRecords
{
    protected static string $resource = BniGalleryItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('uploadGallery')
                ->label('Tải nhiều ảnh')
                ->icon('heroicon-o-cloud-arrow-up')
                ->color('primary')
                ->schema([
                    Section::make('Bộ ảnh sự kiện')
                        ->icon('heroicon-o-photo')
                        ->description('Chọn nhiều ảnh theo dạng lưới. Có thể dùng nút Xóa tất cả trước khi lưu.')
                        ->schema([
                            Select::make('bni_event_id')->label('Sự kiện')->relationship('event', 'title', fn ($query) => BniPanelAccess::scopePublishedEvents($query))->required()->searchable()->preload(),
                            Select::make('bni_chapter_id')->label('Chapter')->relationship('chapter', 'name')->searchable()->preload()->visible(fn (): bool => BniPanelAccess::canManageEverything()),
                            Select::make('group')->label('Nhóm hiển thị')->options(['event' => 'Theo sự kiện', 'chapter' => 'Theo chapter'])->required()->default(fn (): string => BniPanelAccess::isChapterManager() ? 'chapter' : 'event'),
                            TextInput::make('title')->label('Tiêu đề chung')->maxLength(255),
                            CuratorPicker::make('media_ids')
                                ->label('Hình ảnh')
                                ->multiple()
                                ->maxItems(40)
                                ->listDisplay(false)
                                ->disk('public')
                                ->constrained()
                                ->acceptedFileTypes(['image/*'])
                                ->required()
                                ->columnSpanFull(),
                            Toggle::make('is_active')->label('Hiển thị ngay')->default(true)->columnSpanFull(),
                        ])
                        ->columns(2),
                ])
                ->action(function (array $data): void {
                    $base = BniGalleryItemResource::prepareCreateData([
                        'bni_event_id' => $data['bni_event_id'],
                        'bni_chapter_id' => $data['bni_chapter_id'] ?? null,
                        'group' => $data['group'],
                        'title' => $data['title'] ?? null,
                        'status' => BniGalleryItem::STATUS_APPROVED,
                        'is_active' => $data['is_active'] ?? true,
                    ]);

                    foreach (array_values($data['media_ids'] ?? []) as $position => $mediaId) {
                        BniGalleryItem::query()->create($base + [
                            'media_id' => $mediaId,
                            'sort_order' => $position + 1,
                        ]);
                    }

                    Notification::make()
                        ->title('Đã thêm '.count($data['media_ids'] ?? []).' ảnh vào thư viện BNI')
                        ->success()
                        ->send();
                }),
            CreateAction::make()
                ->label('Thêm một ảnh')
                ->mutateDataUsing(fn (array $data): array => BniGalleryItemResource::prepareCreateData($data)),
        ];
    }
}
