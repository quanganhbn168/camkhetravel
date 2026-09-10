<?php

namespace App\Filament\Resources\Tags\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

final class TagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Thẻ nội dung')
                ->icon(Heroicon::OutlinedHashtag)
                ->schema([
                    TextInput::make('name')->label('Tên thẻ')->required()->maxLength(100)->live(onBlur: true),
                    TextInput::make('slug')->label('Slug')->maxLength(120)->helperText('Để trống để tự tạo từ tên thẻ.'),
                    Toggle::make('is_active')->label('Đang sử dụng')->default(true),
                ])
                ->columns(2),
        ]);
    }
}
