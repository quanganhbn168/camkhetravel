<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Filament\Forms\SeoFields;
use App\Filament\RichEditor\ScopedAttachCuratorMediaPlugin;
use App\Models\Post;
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

final class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['lg' => 3])
            ->components([
                Group::make([
                    Section::make('Nội dung bài viết')

                        ->schema([
                            TextInput::make('title')
                                ->label('Tiêu đề')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (?string $state, $get, $set): void {
                                    if (blank($get('slug')) && ($slug = ContentSeoFallbacks::slug($state))) {
                                        $set('slug', $slug);
                                    }

                                    if (blank($get('seo_title')) && ($seoTitle = ContentSeoFallbacks::title($state))) {
                                        $set('seo_title', $seoTitle);
                                    }
                                })
                                ->columnSpanFull(),
                            TextInput::make('slug')
                                ->label('Đường dẫn (slug)')
                                ->maxLength(255)
                                ->helperText('Tự tạo từ tiêu đề khi để trống; anh vẫn có thể sửa khi cần.')
                                ->formatStateUsing(fn (?string $state, ?Post $record): ?string => $state ?: $record?->slug)
                                ->columnSpanFull(),
                            CuratorPicker::make('curator_media_id')->label('Ảnh đại diện')->relationship('curatorMedia', 'id')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->columnSpanFull(),
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
                                ->label('Nội dung')
                                ->plugins([ScopedAttachCuratorMediaPlugin::make()])
                                ->enableToolbarButtons(['attachCuratorMedia'])
                                ->disableToolbarButtons(['attachFiles'])
                                ->columnSpanFull(),
                        ])
                        ->columns(2),
                    Section::make('SEO')

                        ->schema([
                            ...SeoFields::make(),
                        ])
                        ->columns(2),
                ])
                    ->columnSpan(['lg' => 2]),
                Section::make('Phân loại & hiển thị')

                    ->schema([
                        Select::make('post_category_id')->label('Chuyên mục')->relationship('category', 'name')->searchable()->preload(),
                        Select::make('tags')
                            ->label('Thẻ')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')->label('Tên thẻ')->required(),
                            ]),
                        Select::make('status')->label('Trạng thái')->options(['draft' => 'Bản nháp', 'published' => 'Đã xuất bản', 'pending' => 'Chờ duyệt', 'private' => 'Riêng tư'])->required()->default('draft'),
                        Toggle::make('is_featured')->label('Bài viết nổi bật'),
                    ])
                    ->columns(1)
                    ->columnSpan(['lg' => 1]),
            ]);
    }
}
