<?php

namespace App\Filament\Resources\ContactRequests\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

final class ContactRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Thông tin khách hàng')
                ->icon(Heroicon::OutlinedUser)
                ->schema([
                    TextInput::make('name')->label('Họ tên'),
                    TextInput::make('phone')->label('Điện thoại'),
                    TextInput::make('email')->label('Email')->email(),
                    TextInput::make('company')->label('Công ty'),
                    Select::make('service_id')->label('Dịch vụ quan tâm')->relationship('service', 'title')->searchable()->preload(),
                    Select::make('landing_page_id')->label('Landing page nguồn')->relationship('landingPage', 'title')->searchable()->preload(),
                    TextInput::make('budget')->label('Ngân sách'),
                    TextInput::make('timeline')->label('Thời gian dự kiến'),
                    Textarea::make('message')->label('Nội dung')->rows(5)->columnSpanFull(),
                    Select::make('status')->label('Trạng thái')->options(['new' => 'Mới', 'contacted' => 'Đã liên hệ', 'qualified' => 'Tiềm năng', 'closed' => 'Đã xử lý'])->required(),
                    DateTimePicker::make('contacted_at')->label('Đã liên hệ lúc'),
                ])
                ->columns(2),
        ]);
    }
}
