<?php

namespace App\Filament\Resources\Languages\Schemas;

use App\Models\Language;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

final class LanguageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Thông tin ngôn ngữ')
                ->icon(Heroicon::OutlinedLanguage)
                ->description('Thêm ngôn ngữ mới tại đây. Route locale sẽ tự nhận mã mới; route cache sẽ tự được xóa khi lưu.')
                ->schema([
                    TextInput::make('name')
                        ->label('Tên hiển thị')
                        ->required()
                        ->maxLength(100)
                        ->columnSpanFull(),
                    TextInput::make('code')
                        ->label('Mã locale')
                        ->helperText('Ví dụ: en, ko, zh hoặc pt-BR.')
                        ->required()
                        ->regex('/^[a-z]{2,8}(?:-[A-Za-z0-9]{2,8})?$/')
                        ->unique(ignoreRecord: true)
                        ->maxLength(10),
                    TextInput::make('native_name')
                        ->label('Nhãn ngắn/native')
                        ->required()
                        ->maxLength(100),
                    TextInput::make('og_locale')
                        ->label('Open Graph locale')
                        ->helperText('Ví dụ: vi_VN, en_US, ko_KR.')
                        ->required()
                        ->maxLength(20),
                    TextInput::make('sort_order')
                        ->label('Thứ tự')
                        ->numeric()
                        ->default(fn (): int => ((int) Language::query()->max('sort_order')) + 1),
                ])
                ->columns(2),
            Section::make('Xuất bản và SEO')
                ->icon(Heroicon::OutlinedGlobeAlt)
                ->description('Chỉ bật Google index khi nội dung của ngôn ngữ này đã được dịch và xuất bản đầy đủ.')
                ->schema([
                    Toggle::make('is_active')
                        ->label('Hiển thị trong bộ chuyển ngôn ngữ')
                        ->default(true),
                    Toggle::make('is_indexable')
                        ->label('Cho phép Google lập chỉ mục')
                        ->default(false),
                    Toggle::make('is_default')
                        ->label('Ngôn ngữ mặc định')
                        ->helperText('Chọn ngôn ngữ mới làm mặc định sẽ tự bỏ mặc định ở ngôn ngữ cũ.')
                        ->disabled(fn (?Language $record): bool => (bool) $record?->is_default)
                        ->dehydrated(),
                ])
                ->columns(3),
        ]);
    }
}
