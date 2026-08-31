<?php

namespace App\Filament\Bni\Resources\BniInvitations\Pages;

use App\Filament\Bni\Resources\BniInvitations\BniInvitationResource;
use App\Models\BniChapter;
use App\Support\Bni\BniInvitationBulkCreator;
use App\Support\Bni\BniPanelAccess;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManageBniInvitations extends ManageRecords
{
    protected static string $resource = BniInvitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('bulkCreateGuests')
                ->label('Dán danh sách khách mời')
                ->icon('heroicon-o-clipboard-document-list')
                ->modalHeading('Nhập hàng loạt khách mời')
                ->modalSubmitActionLabel('Tạo danh sách')
                ->modalWidth('2xl')
                ->schema([
                    Select::make('bni_chapter_id')
                        ->label('Chapter nhận danh sách')
                        ->options(fn (): array => self::chapterOptions())
                        ->default(fn (): ?int => BniPanelAccess::chapterId())
                        ->disabled(fn (): bool => ! BniPanelAccess::canManageEverything())
                        ->dehydrated()
                        ->searchable()
                        ->required()
                        ->helperText(fn (): string => BniPanelAccess::canManageEverything()
                            ? 'Chọn một lần cho toàn bộ danh sách bên dưới.'
                            : 'Danh sách được tự động nhập vào Chapter của tài khoản.')
                        ->columnSpanFull(),
                    Textarea::make('guest_names')
                        ->label('Tên khách mời')
                        ->placeholder("Nguyễn Văn An\nTrần Thu Hà\nLê Minh Đức")
                        ->helperText('Mỗi dòng một người. Có thể dán thẳng 300 tên; dòng trống sẽ được bỏ qua.')
                        ->rows(18)
                        ->required()
                        ->columnSpanFull(),
                ])
                ->action(function (array $data): void {
                    $count = app(BniInvitationBulkCreator::class)->create(
                        $data['guest_names'],
                        $data['bni_chapter_id'] ?? null,
                    );

                    Notification::make()
                        ->title("Đã tạo {$count} khách mời")
                        ->body('Mỗi khách đã có mã thư mời riêng và đang chờ RSVP.')
                        ->success()
                        ->send();
                })
                ->successNotification(null),
            CreateAction::make()
                ->label('Thêm khách mời')
                ->mutateDataUsing(fn (array $data): array => BniPanelAccess::prepareInvitationData($data)),
        ];
    }

    /** @return array<int, string> */
    private static function chapterOptions(): array
    {
        return BniChapter::query()
            ->with('event:id,title')
            ->when(
                ! BniPanelAccess::canManageEverything(),
                fn ($query) => $query->whereKey(BniPanelAccess::chapterId() ?? 0),
            )
            ->where('is_active', true)
            ->whereNotNull('bni_event_id')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (BniChapter $chapter): array => [
                $chapter->getKey() => $chapter->name.' — '.$chapter->event?->title,
            ])
            ->all();
    }
}
