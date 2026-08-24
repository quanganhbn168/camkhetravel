<?php

namespace App\Filament\Resources\Redirects\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RedirectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('from_path')
                    ->label('Đường dẫn cũ')
                    ->placeholder('/duong-dan-cu/')
                    ->helperText('Chỉ nhập path, bắt đầu và kết thúc bằng dấu /.')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('to_url')
                    ->label('URL đích')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('status_code')
                    ->label('Mã chuyển hướng')
                    ->required()
                    ->numeric()
                    ->default(301),
                Toggle::make('is_active')
                    ->label('Đang hoạt động')
                    ->default(true),
                TextInput::make('source')
                    ->label('Nguồn')
                    ->required()
                    ->default('manual'),
                TextInput::make('hits')
                    ->label('Lượt truy cập')
                    ->numeric()
                    ->default(0)
                    ->disabled()
                    ->dehydrated(),
                DateTimePicker::make('last_hit_at')
                    ->label('Truy cập gần nhất')
                    ->disabled()
                    ->dehydrated(),
            ]);
    }
}
