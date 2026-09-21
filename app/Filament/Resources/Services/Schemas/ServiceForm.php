<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Filament\Forms\SeoFields;
use App\Filament\RichEditor\ScopedAttachCuratorMediaPlugin;
use App\Models\Service;
use App\Support\Seo\ContentSeoFallbacks;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

final class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['lg' => 3])
            ->components([
                Group::make([
                    Section::make('Nội dung dịch vụ')
                        ->icon(Heroicon::OutlinedDocumentText)
                        ->schema([
                            TextInput::make('title')
                                ->label('Tên dịch vụ')
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
                                ->formatStateUsing(fn (?string $state, ?Service $record): ?string => $state ?: $record?->slug)
                                ->columnSpanFull(),
                            CuratorPicker::make('curator_media_id')->label('Ảnh đại diện')->relationship('curatorMedia', 'id')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->columnSpanFull(),
                            CuratorPicker::make('banner_video_media_id')->label('Video banner dịch vụ')->relationship('bannerVideoMedia', 'id')->disk('public')->constrained()->acceptedFileTypes(['video/*'])->helperText('Video được phát nền ở banner; ảnh đại diện sẽ làm poster khi cần.')->columnSpanFull(),
                            CuratorPicker::make('backstage_gallery')->label('Ảnh hậu trường')->multiple()->disk('public')->constrained()->acceptedFileTypes(['image/*'])->columnSpanFull(),
                            CuratorPicker::make('gallery')->label('Ảnh tài liệu tham khảo')->multiple()->disk('public')->constrained()->acceptedFileTypes(['image/*'])->helperText('Ảnh hiển thị cùng khối Các dự án nổi bật.')->columnSpanFull(),
                            TextInput::make('projects_title')
                                ->label('Tiêu đề khối dự án')
                                ->placeholder('Các dự án nổi bật')
                                ->maxLength(255)
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
                                ->label('Nội dung')
                                ->plugins([ScopedAttachCuratorMediaPlugin::make()])
                                ->toolbarButtons([
                                    ['bold', 'italic', 'underline', 'strike', 'link'],
                                    ['attachCuratorMedia', 'h2', 'h3'],
                                    ['alignStart', 'alignCenter', 'alignEnd'],
                                    ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                                    ['table'],
                                    ['undo', 'redo'],
                                ])
                                ->resizableImages()
                                ->columnSpanFull(),
                        ])
                        ->columns(2),
                    Section::make('SEO')
                        ->icon(Heroicon::OutlinedMagnifyingGlass)
                        ->schema([
                            ...SeoFields::make(),
                        ])
                        ->columns(2),
                    Section::make('Giá trị nổi bật')
                        ->icon(Heroicon::OutlinedSparkles)
                        ->schema([
                            TextInput::make('benefit_title')->label('Tiêu đề')->maxLength(255)->columnSpanFull(),
                            Textarea::make('benefit_description')->label('Mô tả')->rows(2)->columnSpanFull(),
                            Repeater::make('benefit_items')
                                ->label('Các giá trị / hạng mục')
                                ->schema([
                                    TextInput::make('title')->label('Tiêu đề')->required()->maxLength(255),
                                    TextInput::make('media_id')->label('ID media Curator')->numeric()->helperText('Nhập ID media trong Curator nếu hạng mục có ảnh.'),
                                    Textarea::make('description')->label('Mô tả')->rows(3)->columnSpanFull(),
                                ])
                                ->columns(2)
                                ->addActionLabel('Thêm giá trị')
                                ->reorderable()
                                ->collapsible()
                                ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Giá trị mới')
                                ->columnSpanFull(),
                        ])
                        ->columns(2),
                    Section::make('Quy trình triển khai')
                        ->icon(Heroicon::OutlinedListBullet)
                        ->schema([
                            Textarea::make('process_description')->label('Mô tả')->rows(2)->columnSpanFull(),
                            CuratorPicker::make('process_background_media_id')->label('Ảnh nền quy trình')->relationship('processBackgroundMedia', 'id')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->helperText('Ảnh nền cho toàn bộ section quy trình.')->columnSpanFull(),
                            Repeater::make('process_items')
                                ->label('Các bước')
                                ->schema([
                                    TextInput::make('step')->label('Số bước')->numeric()->minValue(1),
                                    TextInput::make('title')->label('Tiêu đề')->required()->maxLength(255),
                                    TextInput::make('media_id')->label('ID media Curator')->numeric()->helperText('Nhập ID ảnh minh họa trong Curator nếu có.'),
                                    TextInput::make('color')->label('Màu nhãn')->maxLength(32),
                                    Textarea::make('description')->label('Mô tả')->rows(3)->columnSpanFull(),
                                ])
                                ->columns(2)
                                ->addActionLabel('Thêm bước')
                                ->reorderable()
                                ->collapsible()
                                ->default([
                                    ['step' => 1, 'title' => 'Tiếp nhận yêu cầu', 'description' => 'Làm rõ mục tiêu, phạm vi và đầu ra cần đạt.'],
                                    ['step' => 2, 'title' => 'Tư vấn & định hướng', 'description' => 'Đề xuất hướng triển khai phù hợp với bối cảnh thực tế.'],
                                    ['step' => 3, 'title' => 'Xây dựng ý tưởng', 'description' => 'Phát triển ý tưởng, nội dung và kế hoạch thực hiện.'],
                                    ['step' => 4, 'title' => 'Triển khai sản xuất', 'description' => 'Phối hợp nhân sự, lịch trình và các hạng mục đã thống nhất.'],
                                    ['step' => 5, 'title' => 'Duyệt & hoàn thiện', 'description' => 'Tiếp nhận phản hồi, hoàn thiện và bàn giao thành phẩm.'],
                                ])
                                ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Bước mới')
                                ->columnSpanFull(),
                        ])
                        ->columns(2),
                    Section::make('Cam kết dịch vụ')
                        ->icon(Heroicon::OutlinedShieldCheck)
                        ->schema([
                            CuratorPicker::make('commitment_media_id')->label('Ảnh cam kết')->relationship('commitmentMedia', 'id')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->helperText('Ảnh hiển thị ở một bên của khối cam kết.')->columnSpanFull(),
                            TextInput::make('commitment_title')->label('Tiêu đề')->placeholder('Cam kết của doanh nghiệp')->maxLength(255)->columnSpanFull(),
                            Textarea::make('commitment_description')->label('Mô tả')->rows(3)->columnSpanFull(),
                            Repeater::make('commitment_items')
                                ->label('Các cam kết')
                                ->schema([
                                    TextInput::make('title')->label('Tiêu đề')->required()->maxLength(255),
                                    Textarea::make('description')->label('Mô tả')->rows(3)->columnSpanFull(),
                                ])
                                ->columns(2)
                                ->addActionLabel('Thêm cam kết')
                                ->reorderable()
                                ->collapsible()
                                ->default([
                                    ['title' => 'Rõ ràng ngay từ đầu', 'description' => 'Phạm vi, tiến độ và đầu ra được thống nhất trước khi triển khai.'],
                                    ['title' => 'Đồng hành xuyên suốt', 'description' => 'Đội ngũ phối hợp cùng khách hàng từ định hướng đến bàn giao.'],
                                    ['title' => 'Chỉn chu từng chi tiết', 'description' => 'Mỗi hạng mục được kiểm tra trước khi hoàn thiện và bàn giao.'],
                                ])
                                ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Cam kết mới')
                                ->columnSpanFull(),
                        ])
                        ->columns(2),
                    Section::make('Số liệu & video tham khảo')
                        ->icon(Heroicon::OutlinedChartBar)
                        ->schema([
                            TextInput::make('stats_title')->label('Tiêu đề số liệu')->maxLength(255)->columnSpanFull(),
                            Textarea::make('stats_description')->label('Mô tả số liệu')->rows(2)->columnSpanFull(),
                            Repeater::make('stats_items')
                                ->label('Số liệu nổi bật')
                                ->schema([
                                    TextInput::make('label')->label('Nhãn')->required()->maxLength(255),
                                    Textarea::make('description')->label('Mô tả')->rows(2),
                                ])
                                ->columns(2)
                                ->addActionLabel('Thêm số liệu')
                                ->reorderable()
                                ->collapsible()
                                ->itemLabel(fn (array $state): ?string => $state['label'] ?? 'Số liệu mới')
                                ->columnSpanFull(),
                            Repeater::make('reference_videos')
                                ->label('Video tham khảo')
                                ->schema([
                                    TextInput::make('title')->label('Tên video')->required()->maxLength(255),
                                    TextInput::make('url')->label('Link video')->url()->required()->maxLength(1000),
                                    TextInput::make('thumbnail_media_id')->label('ID thumbnail Curator')->numeric(),
                                    Textarea::make('description')->label('Mô tả')->rows(2)->columnSpanFull(),
                                ])
                                ->columns(2)
                                ->addActionLabel('Thêm video')
                                ->reorderable()
                                ->collapsible()
                                ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Video mới')
                                ->columnSpanFull(),
                        ])
                        ->columns(2),
                ])->columnSpan(['lg' => 2]),
                Section::make('Phân loại & hiển thị')
                    ->icon(Heroicon::OutlinedCog6Tooth)
                    ->schema([
                        Select::make('service_category_id')->label('Danh mục dịch vụ')->relationship('category', 'name')->searchable()->preload(),
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
                        TextInput::make('sort_order')->label('Thứ tự')->numeric()->default(fn (): int => ((int) Service::query()->max('sort_order')) + 1),
                        Toggle::make('is_featured')->label('Dịch vụ nổi bật'),
                        Toggle::make('is_home')->label('Hiển thị trang chủ'),
                    ])
                    ->columns(1)
                    ->columnSpan(['lg' => 1]),
            ]);
    }
}
