<?php

namespace App\Filament\Resources\Partners\Schemas;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class PartnerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Thông tin đối tác')
                ->schema([
                    CuratorPicker::make('curator_media_id')
                        ->label('Logo')
                        ->relationship('curatorMedia', 'id')
                        ->disk('public')
                        ->constrained()
                        ->acceptedFileTypes(['image/*']),
                    TextInput::make('name')->label('Tên đối tác')->required()->maxLength(255),
                    TextInput::make('website_url')->label('Website')->url()->maxLength(255),
                    Toggle::make('is_active')->label('Hiển thị ở marquee trang chủ')->default(true),
                ])
                ->columns(2),
        ]);
    }
}
