<?php

namespace App\Filament\Pages;

use App\Settings\ServicePageSettings;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/** Mẫu chung cho trang chi tiết dịch vụ: dịch vụ nào để trống phần nào thì dùng phần đó ở đây. */
class ManageServicePage extends Page
{
    protected static ?string $slug = 'service-page-template';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;

    protected static ?string $navigationLabel = 'Mẫu trang dịch vụ';

    protected static ?string $title = 'Mẫu trang dịch vụ';

    protected static string|UnitEnum|null $navigationGroup = 'Dịch vụ & giải pháp';

    protected string $view = 'filament.pages.manage-website-settings';

    public ?array $data = [];

    public function mount(ServicePageSettings $settings): void
    {
        $this->form->fill($settings->toArray());
    }

    public function form(Schema $schema): Schema
    {
        $hint = 'Dùng {dich_vu} để chèn tên dịch vụ, ví dụ: "Vì sao chọn {dich_vu}?".';

        return $schema
            ->components([
                Section::make('Áp dụng thế nào?')
                    ->description('Mỗi dịch vụ để trống phần nào ("Vì sao chọn", "Quy trình đặt xe", "Các dòng xe phục vụ") thì trang dịch vụ đó dùng mẫu dưới đây. Dịch vụ đã điền riêng thì giữ nội dung riêng.')
                    ->icon(Heroicon::OutlinedInformationCircle)
                    ->compact(),
                Section::make('Vì sao chọn dịch vụ')
                    ->icon(Heroicon::OutlinedCheckBadge)
                    ->schema([
                        TextInput::make('benefit_title')->label('Tiêu đề')->helperText($hint)->maxLength(255)->columnSpanFull(),
                        Textarea::make('benefit_description')->label('Mô tả')->rows(2)->columnSpanFull(),
                        Repeater::make('benefit_items')
                            ->label('Các lý do')
                            ->schema([
                                TextInput::make('title')->label('Tiêu đề')->required()->maxLength(255),
                                Textarea::make('description')->label('Mô tả')->rows(2),
                            ])
                            ->addActionLabel('Thêm lý do')
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Lý do mới')
                            ->columnSpanFull(),
                    ]),
                Section::make('Quy trình đặt xe')
                    ->icon(Heroicon::OutlinedListBullet)
                    ->schema([
                        TextInput::make('process_title')->label('Tiêu đề')->helperText($hint)->maxLength(255)->columnSpanFull(),
                        Textarea::make('process_description')->label('Mô tả')->rows(2)->columnSpanFull(),
                        Repeater::make('process_items')
                            ->label('Các bước (đánh số tự động theo thứ tự)')
                            ->schema([
                                TextInput::make('title')->label('Tiêu đề')->required()->maxLength(255),
                                Textarea::make('description')->label('Mô tả')->rows(2),
                            ])
                            ->addActionLabel('Thêm bước')
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Bước mới')
                            ->columnSpanFull(),
                    ]),
                Section::make('Các dòng xe phục vụ')
                    ->description('Chỉ liệt kê loại xe thực sự cung cấp.')
                    ->icon(Heroicon::OutlinedTruck)
                    ->schema([
                        TextInput::make('vehicle_title')->label('Tiêu đề')->helperText($hint)->maxLength(255)->columnSpanFull(),
                        Textarea::make('vehicle_description')->label('Mô tả')->rows(2)->columnSpanFull(),
                        Repeater::make('vehicle_items')
                            ->label('Dòng xe')
                            ->schema([
                                TextInput::make('title')->label('Tên xe')->required()->maxLength(255),
                                TextInput::make('seats')->label('Số chỗ')->placeholder('4 chỗ')->maxLength(64),
                                CuratorPicker::make('media_id')->label('Ảnh xe')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->columnSpanFull(),
                                Textarea::make('description')->label('Mô tả')->rows(2)->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->addActionLabel('Thêm dòng xe')
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Dòng xe mới')
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(ServicePageSettings $settings): void
    {
        $state = $this->form->getState();

        foreach (['benefit_title', 'benefit_description', 'process_title', 'process_description', 'vehicle_title', 'vehicle_description'] as $key) {
            $settings->{$key} = trim((string) ($state[$key] ?? ''));
        }
        foreach (['benefit_items', 'process_items', 'vehicle_items'] as $key) {
            $settings->{$key} = array_values(array_filter((array) ($state[$key] ?? []), fn (mixed $item): bool => is_array($item) && filled($item['title'] ?? null)));
        }
        $settings->save();

        Notification::make()->title('Đã lưu mẫu trang dịch vụ')->success()->send();
    }
}
