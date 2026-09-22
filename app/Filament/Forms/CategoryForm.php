<?php

namespace App\Filament\Forms;

use App\Filament\RichEditor\ScopedAttachCuratorMediaPlugin;
use App\Models\ServiceCategory;
use App\Support\Categories\CategoryTree;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

final class CategoryForm
{
    public static function configure(Schema $schema, string $model, string $label): Schema
    {
        return $schema->columns(['default' => 1, 'lg' => 3])->components([
            Group::make([
                Section::make($label)->schema([
                    TextInput::make('name')->label('Tên danh mục')->required()->maxLength(255)
                        ->live(onBlur: true)->afterStateUpdated(function (?string $state, $get, $set): void {
                            if (blank($get('slug'))) {
                                $set('slug', Str::slug($state ?? ''));
                            }
                        }),
                    TextInput::make('slug')->label('Đường dẫn (slug)')->maxLength(255)
                        ->formatStateUsing(fn (?string $state, ?Model $record) => $state ?: $record?->slug),
                    Textarea::make('description')->label('Mô tả')->rows(3),
                    RichEditor::make('body')->label('Nội dung')
                        ->plugins([ScopedAttachCuratorMediaPlugin::make()])
                        ->toolbarButtons([
                            ['bold', 'italic', 'underline', 'strike', 'link'],
                            ['attachCuratorMedia', 'h2', 'h3'],
                            ['alignStart', 'alignCenter', 'alignEnd'],
                            ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                            ['table'], ['undo', 'redo'],
                        ])->resizableImages(),
                ])->columns(1),
                Section::make('SEO')->schema(collect(SeoFields::make('name', 'description'))
                    ->map(fn ($field) => $field->helperText(null))->all())->columns(1),
            ])->columnSpan(['default' => 1, 'lg' => 2]),
            Group::make([
                Section::make('Thiết lập')->schema([
                    Select::make('parent_id')->label('Danh mục cha')->placeholder('Danh mục gốc')
                        ->options(fn (?Model $record) => CategoryTree::parentOptions($model, $record))
                        ->searchable()->preload()->nullable()
                        ->rules([fn (?Model $record) => function (string $attribute, $value, $fail) use ($model, $record): void {
                            if ($value !== null && ! array_key_exists($value, CategoryTree::parentOptions($model, $record))) {
                                $fail('Danh mục cha không hợp lệ.');
                            }
                        }]),
                    Toggle::make('is_active')->label('Hiển thị')->default(true),
                    ...($model === ServiceCategory::class ? [
                        Toggle::make('is_featured')->label('Danh mục nổi bật')->default(false),
                        Toggle::make('is_home')->label('Hiển thị trên trang chủ')->default(false),
                    ] : []),
                    TextInput::make('sort_order')->label('Thứ tự')->numeric()->minValue(0)->default(0),
                ])->columns(1),
                Section::make('Hình ảnh')->schema([
                    CuratorPicker::make('curator_media_id')->label('Ảnh đại diện')
                        ->relationship('curatorMedia', 'id')->disk('public')->constrained()->acceptedFileTypes(['image/*']),
                    CuratorPicker::make('banner_media_id')->label('Banner')
                        ->relationship('bannerMedia', 'id')->disk('public')->constrained()->acceptedFileTypes(['image/*']),
                ])->columns(1),
            ])->columnSpan(1),
        ]);
    }
}
