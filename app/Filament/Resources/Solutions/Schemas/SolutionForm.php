<?php

namespace App\Filament\Resources\Solutions\Schemas;

use App\Filament\RichEditor\ScopedAttachCuratorMediaPlugin;
use App\Models\Solution;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class SolutionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(['lg' => 3])->components([
            Group::make([
                Section::make('Nội dung giải pháp')->schema([
                    TextInput::make('title')->label('Tên giải pháp')->required()->maxLength(255),
                    TextInput::make('short_title')->label('Loại công trình')->maxLength(255),
                    TextInput::make('slug')->label('Đường dẫn')->maxLength(255)->formatStateUsing(fn (?string $state, ?Solution $record) => $state ?: $record?->slug),
                    Textarea::make('excerpt')->label('Mô tả ngắn')->rows(3),
                    RichEditor::make('body')->label('Nội dung')->plugins([ScopedAttachCuratorMediaPlugin::make()])
                        ->toolbarButtons([['bold', 'italic', 'underline', 'link'], ['attachCuratorMedia', 'h2', 'h3'], ['bulletList', 'orderedList', 'blockquote'], ['undo', 'redo']])->resizableImages(),
                    Repeater::make('highlights')->label('Hạng mục nổi bật')->simple(TextInput::make('item')->required()->maxLength(255))->defaultItems(0)->addActionLabel('Thêm hạng mục'),
                ]),
                Section::make('SEO')->schema([
                    TextInput::make('seo_title')->label('Tiêu đề SEO')->maxLength(255),
                    Textarea::make('seo_description')->label('Mô tả SEO')->maxLength(160)->rows(3),
                    CuratorPicker::make('seo_image_media_id')->label('Ảnh chia sẻ')->relationship('seoImageMedia', 'id')->disk('public')->constrained()->acceptedFileTypes(['image/*']),
                ]),
            ])->columnSpan(['lg' => 2]),
            Group::make([
                Section::make('Hiển thị')->schema([
                    Toggle::make('is_active')->label('Công khai')->default(false),
                    Toggle::make('is_home')->label('Hiện trang chủ')->default(false),
                    TextInput::make('sort_order')->label('Thứ tự')->numeric()->integer()->minValue(0)->default(0),
                ]),
                Section::make('Hình ảnh')->schema([
                    CuratorPicker::make('curator_media_id')->label('Ảnh đại diện / nền trang chủ')->relationship('curatorMedia', 'id')->disk('public')->constrained()->acceptedFileTypes(['image/*']),
                    CuratorPicker::make('banner_media_id')->label('Banner chi tiết')->relationship('bannerMedia', 'id')->disk('public')->constrained()->acceptedFileTypes(['image/*']),
                ]),
            ])->columnSpan(['lg' => 1]),
        ]);
    }
}
