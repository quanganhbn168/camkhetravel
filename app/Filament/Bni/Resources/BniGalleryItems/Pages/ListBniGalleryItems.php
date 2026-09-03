<?php

namespace App\Filament\Bni\Resources\BniGalleryItems\Pages;

use App\Filament\Bni\Resources\BniGalleryItems\BniGalleryItemResource;
use App\Filament\Bni\Resources\Pages\ListBniRecords;
use App\Models\BniActivity;
use App\Models\BniGalleryItem;
use App\Support\Bni\BniMediaService;
use App\Support\Bni\BniPanelAccess;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Throwable;

class ListBniGalleryItems extends ListBniRecords
{
    protected static string $resource = BniGalleryItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('uploadGallery')
                ->label('Tải nhiều ảnh')
                ->icon('heroicon-o-cloud-arrow-up')
                ->color('primary')
                ->slideOver()
                ->schema([
                    Section::make('Bộ ảnh sự kiện')
                        ->icon('heroicon-o-photo')
                        ->description('Chọn tối đa 40 ảnh. Ảnh gốc được giữ nguyên và hệ thống tự tạo WebP chất lượng cao để hiển thị.')
                        ->schema([
                            TextInput::make('title')->label('Tiêu đề chung')->maxLength(255)->columnSpanFull(),
                            Select::make('bni_event_id')
                                ->label('Sự kiện tổ chức')
                                ->relationship('event', 'title', fn ($query) => BniPanelAccess::scopePublishedEvents($query))
                                ->required()
                                ->searchable()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(fn ($set) => $set('bni_activity_id', null))
                                ->columnSpanFull(),
                            Select::make('bni_activity_id')
                                ->label('Hoạt động / album ảnh')
                                ->options(fn ($get): array => BniActivity::query()
                                    ->where('bni_event_id', $get('bni_event_id'))
                                    ->where('is_active', true)
                                    ->orderBy('sort_order')
                                    ->orderBy('title')
                                    ->pluck('title', 'id')
                                    ->all())
                                ->required()
                                ->searchable()
                                ->columnSpanFull(),
                            FileUpload::make('images')
                                ->label('Hình ảnh')
                                ->multiple()
                                ->maxFiles(40)
                                ->image()
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->storeFiles(false)
                                ->required()
                                ->columnSpanFull(),
                            Toggle::make('is_active')->label('Hiển thị ngay')->default(true)->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),
                ])
                ->action(function (array $data, BniMediaService $mediaService): void {
                    $base = BniGalleryItemResource::prepareCreateData([
                        'bni_event_id' => $data['bni_event_id'],
                        'bni_activity_id' => $data['bni_activity_id'],
                        'title' => $data['title'] ?? null,
                        'status' => BniGalleryItem::STATUS_APPROVED,
                        'is_active' => $data['is_active'] ?? true,
                    ]);

                    $createdItems = collect();

                    try {
                        collect($data['images'] ?? [])->values()->each(function (TemporaryUploadedFile $upload, int $position) use ($base, $mediaService, $createdItems): void {
                            $item = BniGalleryItem::query()->create($base + ['sort_order' => $position + 1]);
                            $createdItems->push($item);
                            $mediaService->attachUpload($item, $upload, 'image', $base['title'] ?? null);
                        });
                    } catch (Throwable $exception) {
                        $createdItems->each->delete();

                        throw $exception;
                    }

                    Notification::make()
                        ->title('Đã thêm '.count($data['images'] ?? []).' ảnh vào thư viện BNI')
                        ->success()
                        ->send();
                }),
            CreateAction::make()->label('Thêm một ảnh'),
        ];
    }
}
