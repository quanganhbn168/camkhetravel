<?php

namespace App\Filament\Resources\Page\Schemas;

use App\Filament\Forms\SeoFields;
use App\Filament\RichEditor\ScopedAttachCuratorMediaPlugin;
use App\Models\Page;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;

final class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        $custom = fn (?Page $record): bool => ! ($record?->isSystem() ?? false);
        $system = fn (?Page $record): bool => $record?->isSystem() ?? false;

        return $schema->columns(['default' => 1, 'lg' => 3])->components([
            Group::make([
                Section::make('Thông tin trang')
                    ->schema([
                        Text::make(fn (?Page $record): string => 'Trang hệ thống, địa chỉ cố định: '.($record?->system_key ? parse_url($record->url, PHP_URL_PATH) : ''))
                            ->visible($system)
                            ->columnSpanFull(),
                        TextInput::make('title')->label('Tiêu đề')->required()->maxLength(255)->columnSpanFull(),
                        TextInput::make('slug')
                            ->label('Đường dẫn (slug)')
                            ->helperText('Trang hiển thị tại /đường-dẫn. Chữ thường, số và dấu gạch ngang; để trống để tự tạo từ tiêu đề.')
                            ->maxLength(180)
                            ->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')
                            ->formatStateUsing(fn (?string $state, ?Page $record): ?string => $state ?: $record?->slug)
                            ->visible($custom)
                            ->columnSpanFull(),
                        Textarea::make('excerpt')
                            ->label('Mô tả ngắn')
                            ->helperText(fn (?Page $record): ?string => $record?->isSystem() ? 'Hiển thị dưới tiêu đề trên banner của trang. Để trống thì dùng câu mặc định.' : null)
                            ->rows(3)
                            ->columnSpanFull(),
                        CuratorPicker::make('curator_media_id')
                            ->label('Ảnh đại diện')
                            ->relationship('curatorMedia', 'id')
                            ->disk('public')
                            ->constrained()
                            ->acceptedFileTypes(['image/*'])
                            ->visible($custom)
                            ->columnSpanFull(),
                        CuratorPicker::make('banner_media_id')
                            ->label('Banner trang')
                            ->relationship('bannerMedia', 'id')
                            ->disk('public')
                            ->constrained()
                            ->acceptedFileTypes(['image/*'])
                            ->helperText('Không bắt buộc. Để trống thì trang không hiển thị ảnh banner.')
                            ->columnSpanFull(),
                        RichEditor::make('content')
                            ->label('Nội dung')
                            ->plugins([ScopedAttachCuratorMediaPlugin::make()])
                            ->enableToolbarButtons(['attachCuratorMedia'])
                            ->disableToolbarButtons(['attachFiles'])
                            ->visible($custom)
                            ->columnSpanFull(),
                    ])->columns(2),
                Section::make('SEO')
                    ->collapsible()
                    ->description(fn (?Page $record): ?string => $record?->isSystem() ? 'Trang hệ thống bắt buộc có SEO title, meta description và ảnh chia sẻ.' : null)
                    ->schema(collect(SeoFields::make(titleSource: 'title', descriptionSource: 'excerpt'))
                        ->map(fn ($component) => $component->required($system))
                        ->all()),
            ])->columnSpan(['default' => 1, 'lg' => 2]),
            Group::make([
                Section::make('Xuất bản')
                    ->visible($custom)
                    ->schema([
                        Select::make('status')
                            ->label('Trạng thái')
                            ->options(['draft' => 'Bản nháp', 'published' => 'Đã xuất bản'])
                            ->default('draft')
                            ->required(),
                        DateTimePicker::make('published_at')
                            ->label('Ngày xuất bản')
                            ->helperText('Để trống để hiển thị ngay khi xuất bản; đặt ngày tương lai để hẹn giờ.'),
                        TextInput::make('sort_order')->label('Thứ tự')->numeric()->integer()->minValue(0)->default(0),
                    ]),
            ])->columnSpan(['default' => 1, 'lg' => 1]),
        ]);
    }
}
