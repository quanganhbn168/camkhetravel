<?php

namespace App\Filament\Resources\ServicePricings\Pages;

use App\Filament\Resources\ServicePricings\ServicePricingResource;
use App\Support\Pricing\PricingJsonImporter;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\EditRecord;
use InvalidArgumentException;

class EditServicePricing extends EditRecord
{
    protected static string $resource = ServicePricingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('importJson')
                ->label('Nhập JSON')
                ->icon('heroicon-o-code-bracket-square')
                ->schema([
                    Textarea::make('json')
                        ->label('JSON bảng giá')
                        ->rows(18)
                        ->required()
                        ->helperText('JSON cần có packages hoặc plans. Thao tác này sẽ thay thế toàn bộ gói và đầu mục hiện có của bảng giá này.'),
                ])
                ->action(function (array $data): void {
                    try {
                        $counts = app(PricingJsonImporter::class)->import($this->record, (string) $data['json']);
                    } catch (InvalidArgumentException $exception) {
                        Notification::make()
                            ->title('Không thể nhập JSON')
                            ->body($exception->getMessage())
                            ->danger()
                            ->send();

                        return;
                    }

                    $this->record->refresh();
                    $this->fillForm();
                    Notification::make()
                        ->title('Đã nhập bảng giá')
                        ->body("Đã tạo {$counts['packages']} gói và {$counts['items']} đầu mục.")
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->modalHeading('Nhập nhanh bảng giá bằng JSON')
                ->modalSubmitActionLabel('Nhập dữ liệu')
                ->slideOver(),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
