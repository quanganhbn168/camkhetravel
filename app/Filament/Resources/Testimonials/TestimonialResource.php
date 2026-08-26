<?php

namespace App\Filament\Resources\Testimonials;

use App\Filament\Resources\Testimonials\Pages\CreateTestimonial;
use App\Filament\Resources\Testimonials\Pages\EditTestimonial;
use App\Filament\Resources\Testimonials\Pages\ListTestimonials;
use App\Models\Testimonial;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $navigationLabel = 'Phản hồi khách hàng';

    protected static ?string $recordTitleAttribute = 'client_name';

    protected static ?int $navigationSort = 6;

    public static function getNavigationGroup(): ?string
    {
        return 'Nội dung trang chủ';
    }

    public static function getModelLabel(): string
    {
        return 'phản hồi';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Phản hồi khách hàng';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Thông tin khách hàng')
                ->schema([
                    CuratorPicker::make('curator_media_id')
                        ->label('Ảnh đại diện / logo')
                        ->relationship('curatorMedia', 'id')
                        ->disk('public')
                        ->constrained()
                        ->acceptedFileTypes(['image/*']),
                    TextInput::make('client_name')->label('Họ tên khách hàng')->required()->maxLength(255),
                    TextInput::make('client_role')->label('Chức danh')->maxLength(255),
                    TextInput::make('company_name')->label('Doanh nghiệp')->maxLength(255),
                    TextInput::make('rating')->label('Số sao')->numeric()->minValue(1)->maxValue(5)->default(5),
                    Textarea::make('quote')->label('Nội dung phản hồi')->required()->rows(5)->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Hiển thị')
                ->schema([
                    Toggle::make('is_active')->label('Hiển thị trên trang chủ')->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                CuratorColumn::make('curatorMedia')->label('Ảnh')->square(),
                TextColumn::make('client_name')->label('Khách hàng')->searchable()->sortable(),
                TextColumn::make('company_name')->label('Doanh nghiệp')->toggleable(),
                TextColumn::make('rating')->label('Sao')->suffix('/5')->sortable(),
                IconColumn::make('is_active')->label('Hiển thị')->boolean(),
                TextColumn::make('updated_at')->label('Cập nhật')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTestimonials::route('/'),
            'create' => CreateTestimonial::route('/create'),
            'edit' => EditTestimonial::route('/{record}/edit'),
        ];
    }
}
