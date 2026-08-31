<?php

namespace App\Filament\Resources\PricingPlans\Schemas;

use App\Models\PricingPlan;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class PricingPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Nội dung gói giá')
                ->icon(Heroicon::OutlinedReceiptPercent)
                ->schema([
                    TextInput::make('name')->label('Tên gói')->required()->maxLength(255)->columnSpanFull(),
                    Select::make('landing_page_id')
                        ->label('Landing page liên quan (tuỳ chọn)')
                        ->relationship('landingPage', 'title')
                        ->searchable()
                        ->preload()
                        ->columnSpanFull(),
                    TextInput::make('badge')->label('Nhãn nổi bật')->maxLength(100)->placeholder('Ví dụ: Phổ biến'),
                    Textarea::make('description')->label('Mô tả ngắn')->rows(3)->columnSpanFull(),
                    TextInput::make('price')->label('Giá từ')->numeric()->minValue(0)->prefix('₫')->helperText('Để trống nếu cần báo giá theo yêu cầu.'),
                    TextInput::make('price_label')->label('Nhãn giá thay thế')->maxLength(255)->placeholder('Ví dụ: Liên hệ để nhận báo giá'),
                    TextInput::make('price_unit')->label('Đơn vị giá')->maxLength(100)->placeholder('Ví dụ: / dự án'),
                    TagsInput::make('features')->label('Hạng mục bao gồm')->placeholder('Nhập hạng mục rồi nhấn Enter')->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Hiển thị')
                ->icon(Heroicon::OutlinedEye)
                ->schema([
                    TextInput::make('sort_order')->label('Thứ tự')->numeric()->default(fn (): int => ((int) PricingPlan::query()->max('sort_order')) + 1),
                    Toggle::make('is_featured')->label('Gói nổi bật')->default(false),
                    Toggle::make('is_active')->label('Hiển thị trên website')->default(true)->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }
}
