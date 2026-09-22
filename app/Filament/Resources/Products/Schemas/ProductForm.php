<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Filament\Forms\SeoFields;
use App\Filament\RichEditor\ScopedAttachCuratorMediaPlugin;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Support\Categories\CategoryTree;
use App\Support\Seo\ContentSeoFallbacks;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['lg' => 3])
            ->components([
                Group::make([
                    Section::make('Thông tin sản phẩm')

                        ->schema([
                            TextInput::make('title')
                                ->label('Tên sản phẩm')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (?string $state, $get, $set): void {
                                    if (blank($get('slug')) && ($slug = ContentSeoFallbacks::slug($state))) {
                                        $set('slug', $slug);
                                    }
                                    if (blank($get('seo_title')) && ($title = ContentSeoFallbacks::title($state))) {
                                        $set('seo_title', $title);
                                    }
                                })
                                ->columnSpanFull(),
                            TextInput::make('slug')->label('Đường dẫn (slug)')->maxLength(255)->columnSpanFull(),
                            TextInput::make('sku')->label('Mã sản phẩm')->maxLength(100),
                            CuratorPicker::make('curator_media_id')
                                ->label('Ảnh đại diện')
                                ->relationship('curatorMedia', 'id')
                                ->disk('public')
                                ->constrained()
                                ->acceptedFileTypes(['image/*'])
                                ->columnSpanFull(),
                            CuratorPicker::make('gallery')
                                ->label('Thư viện ảnh')
                                ->multiple()
                                ->disk('public')
                                ->constrained()
                                ->acceptedFileTypes(['image/*'])
                                ->helperText('Ảnh gốc được giữ nguyên; Curator chỉ tạo các bản preview WebP khi hiển thị.')
                                ->columnSpanFull(),
                            Textarea::make('excerpt')
                                ->label('Mô tả ngắn')
                                ->rows(3)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (?string $state, $get, $set): void {
                                    if (blank($get('seo_description')) && ($description = ContentSeoFallbacks::description($state))) {
                                        $set('seo_description', $description);
                                    }
                                })
                                ->columnSpanFull(),
                            RichEditor::make('body')
                                ->label('Mô tả chi tiết')
                                ->plugins([ScopedAttachCuratorMediaPlugin::make()])
                                ->enableToolbarButtons(['attachCuratorMedia'])
                                ->disableToolbarButtons(['attachFiles'])
                                ->columnSpanFull(),
                        ])
                        ->columns(2),
                    Section::make('SEO')

                        ->schema(SeoFields::make())
                        ->columns(2),
                ])->columnSpan(['lg' => 2]),
                Section::make('Phân loại & hiển thị')

                    ->schema([
                        Select::make('product_category_id')->label('Danh mục sản phẩm')
                            ->options(fn () => CategoryTree::leafOptions(ProductCategory::class))
                            ->searchable()->preload()
                            ->rules([fn () => function (string $attribute, $value, $fail): void {
                                if ($value !== null && ! array_key_exists($value, CategoryTree::leafOptions(ProductCategory::class))) {
                                    $fail('Chỉ được chọn danh mục không có danh mục con.');
                                }
                            }]),
                        Select::make('tags')
                            ->label('Thẻ')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([TextInput::make('name')->label('Tên thẻ')->required()]),
                        Select::make('status')->label('Trạng thái')->options(['draft' => 'Bản nháp', 'published' => 'Đã xuất bản', 'pending' => 'Chờ duyệt', 'private' => 'Riêng tư'])->required()->default('draft'),
                        TextInput::make('sort_order')->label('Thứ tự')->numeric()->default(fn (): int => ((int) Product::query()->max('sort_order')) + 1),
                        Toggle::make('is_featured')->label('Sản phẩm nổi bật'),
                    ])
                    ->columns(1)
                    ->columnSpan(['lg' => 1]),
            ]);
    }
}
