<?php

namespace App\Filament\Resources\ContactRequests;

use App\Filament\Resources\ContactRequests\Pages\EditContactRequest;
use App\Filament\Resources\ContactRequests\Pages\ListContactRequests;
use App\Models\ContactRequest;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContactRequestResource extends Resource
{
    protected static ?string $model = ContactRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $navigationLabel = 'Yêu cầu tư vấn';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return 'Khách hàng & liên hệ';
    }

    public static function getModelLabel(): string
    {
        return 'yêu cầu tư vấn';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Yêu cầu tư vấn';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
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
            Section::make('Nguồn landing & chiến dịch')
                ->icon(Heroicon::OutlinedChartBarSquare)
                ->schema([
                    TextInput::make('landing_block_id')->label('Khối chuyển đổi')->disabled()->dehydrated(false),
                    TextInput::make('utm_source')->label('UTM source')->disabled()->dehydrated(false),
                    TextInput::make('utm_medium')->label('UTM medium')->disabled()->dehydrated(false),
                    TextInput::make('utm_campaign')->label('UTM campaign')->disabled()->dehydrated(false),
                    TextInput::make('utm_content')->label('UTM content')->disabled()->dehydrated(false),
                    TextInput::make('utm_term')->label('UTM term')->disabled()->dehydrated(false),
                    TextInput::make('session_id')->label('Session ID')->disabled()->dehydrated(false),
                    TextInput::make('visitor_id')->label('Visitor ID')->disabled()->dehydrated(false),
                    Textarea::make('first_url')->label('Trang đầu tiên')->rows(2)->disabled()->dehydrated(false)->columnSpanFull(),
                    Textarea::make('referrer')->label('Referrer')->rows(2)->disabled()->dehydrated(false)->columnSpanFull(),
                ])
                ->columns(2)
                ->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Khách hàng')->searchable()->sortable(),
                TextColumn::make('phone')->label('Điện thoại')->copyable(),
                TextColumn::make('service.title')->label('Dịch vụ')->toggleable(),
                TextColumn::make('landingPage.title')->label('Landing page')->toggleable(),
                TextColumn::make('utm_source')->label('Nguồn')->badge()->toggleable(),
                TextColumn::make('utm_campaign')->label('Chiến dịch')->toggleable(),
                TextColumn::make('status')->label('Trạng thái')->badge(),
                TextColumn::make('created_at')->label('Gửi lúc')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Trạng thái')->options(['new' => 'Mới', 'contacted' => 'Đã liên hệ', 'qualified' => 'Tiềm năng', 'closed' => 'Đã xử lý']),
                SelectFilter::make('service_id')->label('Dịch vụ')->relationship('service', 'title')->searchable()->preload(),
                SelectFilter::make('landing_page_id')->label('Landing page')->relationship('landingPage', 'title')->searchable()->preload(),
                SelectFilter::make('utm_source')->label('Nguồn UTM')->options(fn (): array => ContactRequest::query()->whereNotNull('utm_source')->distinct()->orderBy('utm_source')->pluck('utm_source', 'utm_source')->all()),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactRequests::route('/'),
            'edit' => EditContactRequest::route('/{record}/edit'),
        ];
    }
}
