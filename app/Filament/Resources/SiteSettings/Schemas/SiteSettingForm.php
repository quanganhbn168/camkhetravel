<?php

namespace App\Filament\Resources\SiteSettings\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SiteSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('group')
                    ->label('Nhóm')
                    ->required()
                    ->default('general'),
                TextInput::make('key')
                    ->label('Khóa')
                    ->required(),
                TextInput::make('type')
                    ->label('Kiểu dữ liệu')
                    ->required()
                    ->default('string'),
                Textarea::make('value')
                    ->label('Giá trị')
                    ->rows(16)
                    ->columnSpanFull(),
                Toggle::make('is_public')
                    ->label('Cho phép dùng ngoài frontend'),
            ]);
    }
}
