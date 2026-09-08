<?php

namespace App\Filament\Resources\ServicePricings\Pages;

use App\Filament\Resources\ServicePricings\ServicePricingResource;
use App\Support\Localization\LocalizedUrl;
use App\Support\Pricing\PricingJsonImporter;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use InvalidArgumentException;

class EditServicePricing extends EditRecord
{
    protected static string $resource = ServicePricingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('Xem trên website')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn (): string => LocalizedUrl::service($this->record->service).'#goi-dich-vu')
                ->openUrlInNewTab(),
            Action::make('downloadJsonTemplate')
                ->label('Tải mẫu JSON')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(asset('downloads/service-pricing-template.json'))
                ->extraAttributes(['download' => 'service-pricing-template.json']),
            Action::make('downloadComparison')
                ->label('Tải JSON so sánh hiện tại')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => $this->downloadComparison()),
            Action::make('importJson')
                ->label('Nhập JSON')
                ->icon('heroicon-o-code-bracket-square')
                ->schema([
                    Textarea::make('json')
                        ->label('JSON bảng giá')
                        ->rows(18)
                        ->required()
                        ->helperText('Có packages: thay toàn bộ gói và bảng so sánh. Chỉ có comparison: cập nhật riêng bảng so sánh, giữ nguyên gói giá.'),
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
                        ->body($counts['packages'] > 0 ? "Đã tạo {$counts['packages']} gói và {$counts['items']} đầu mục, cập nhật bảng so sánh." : 'Đã cập nhật bảng so sánh, giữ nguyên các gói giá.')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->extraModalFooterActions([
                    Action::make('downloadComparison')
                        ->label('Tải JSON so sánh hiện tại')
                        ->action(fn () => $this->downloadComparison()),
                    Action::make('downloadTemplate')
                        ->label('Tải mẫu JSON')
                        ->url(asset('downloads/service-pricing-template.json'))
                        ->extraAttributes(['download' => 'service-pricing-template.json']),
                ])
                ->modalDescription('Tải mẫu đầy đủ để nhập gói giá và so sánh, hoặc tải mẫu so sánh hiện tại để chỉnh riêng từng ô Có/Không. Không tự suy ra quyền lợi từ giá hoặc thứ tự gói.')
                ->modalHeading('Nhập nhanh bảng giá bằng JSON')
                ->modalSubmitActionLabel('Nhập dữ liệu')
                ->slideOver(),
            DeleteAction::make(),
        ];
    }

    private function downloadComparison()
    {
        $packages = $this->record->packages;
        $rows = collect($this->record->comparison_rows ?? []);
        if ($rows->isEmpty()) {
            $rows = collect([['name' => 'Tiêu chí mẫu - sửa trước khi nhập', 'cells' => $packages->map(fn ($package) => ['package_id' => $package->id, 'included' => false, 'value' => null])->all()]]);
        }
        $payload = [
            '_guide' => 'Chỉ cập nhật bảng so sánh. package là ID gói trong package_reference; included là true hoặc false. Ô chưa thiết lập được bỏ khỏi bản xuất.',
            'package_reference' => $packages->map(fn ($package) => ['id' => $package->id, 'name' => $package->name])->all(),
            'comparison' => $rows->map(fn ($row) => [
                'name' => $row['name'],
                'cells' => collect($row['cells'] ?? [])->filter(fn ($cell) => isset($cell['included']) && $packages->contains('id', $cell['package_id']))->map(fn ($cell) => [
                    'package' => (int) $cell['package_id'], 'included' => (bool) $cell['included'], 'value' => $cell['value'] ?? null,
                ])->values()->all(),
            ])->all(),
        ];

        return response()->streamDownload(fn () => print (json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)), 'service-comparison-'.$this->record->id.'.json', ['Content-Type' => 'application/json']);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
