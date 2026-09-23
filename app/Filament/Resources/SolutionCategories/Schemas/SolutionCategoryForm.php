<?php

namespace App\Filament\Resources\SolutionCategories\Schemas;

use App\Filament\RichEditor\ScopedAttachCuratorMediaPlugin;
use App\Models\SolutionCategory;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class SolutionCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(['lg' => 3])->components([
            Group::make([
                Section::make('Danh mục giải pháp')->schema([
                    TextInput::make('name')->label('Tên danh mục')->required()->maxLength(255)
                        ->placeholder('Ví dụ: PCCC, HVAC & Thông gió'),
                    TextInput::make('slug')->label('Đường dẫn')->maxLength(255)
                        ->formatStateUsing(fn (?string $state, ?SolutionCategory $record) => $state ?: $record?->slug),
                    Textarea::make('description')->label('Mô tả ngắn')->rows(3),
                    RichEditor::make('body')->label('Nội dung danh mục')
                        ->plugins([ScopedAttachCuratorMediaPlugin::make()])
                        ->toolbarButtons([['bold', 'italic', 'underline', 'link'], ['attachCuratorMedia', 'h2', 'h3'], ['bulletList', 'orderedList', 'blockquote'], ['undo', 'redo']])
                        ->resizableImages(),
                ]),
                Section::make('SEO')->schema([
                    TextInput::make('seo_title')->label('Tiêu đề SEO')->maxLength(255),
                    Textarea::make('seo_description')->label('Mô tả SEO')->maxLength(160)->rows(3),
                    CuratorPicker::make('seo_image_media_id')->label('Ảnh chia sẻ')
                        ->relationship('seoImageMedia', 'id')->disk('public')->constrained()->acceptedFileTypes(['image/*']),
                ]),
            ])->columnSpan(['lg' => 2]),
            Group::make([
                Section::make('Hiển thị')->schema([
                    Toggle::make('is_active')->label('Công khai')->default(false)
                        ->helperText('Ẩn danh mục sẽ ẩn các giải pháp thuộc danh mục khỏi website, không xóa nội dung.'),
                    TextInput::make('sort_order')->label('Thứ tự')->numeric()->integer()->minValue(0)->maxValue(4294967295)->required()->default(0)
                        ->helperText('Số nhỏ đứng trước. Nhập trực tiếp hoặc kéo thả tại danh sách.'),
                ]),
                Section::make('Hình ảnh')->schema([
                    CuratorPicker::make('curator_media_id')->label('Ảnh đại diện')
                        ->relationship('curatorMedia', 'id')->disk('public')->constrained()->acceptedFileTypes(['image/*']),
                    CuratorPicker::make('banner_media_id')->label('Banner')
                        ->relationship('bannerMedia', 'id')->disk('public')->constrained()->acceptedFileTypes(['image/*']),
                ]),
            ])->columnSpan(['lg' => 1]),
        ]);
    }
}
