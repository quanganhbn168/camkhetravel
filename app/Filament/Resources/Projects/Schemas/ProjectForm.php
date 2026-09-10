<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Filament\Forms\SeoImageField;
use App\Filament\RichEditor\ScopedAttachCuratorMediaPlugin;
use App\Models\Project;
use App\Support\Seo\ContentSeoFallbacks;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\DatePicker;
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

final class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['lg' => 3])
            ->components([
                Group::make([
                    Section::make('Nội dung dự án')
                        ->icon(Heroicon::OutlinedDocumentText)
                        ->schema([
                            TextInput::make('title')
                                ->label('Tên dự án')
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
                                ->formatStateUsing(fn (?string $state, ?Project $record): ?string => $state ?: $record?->slug)
                                ->columnSpanFull(),
                            TextInput::make('client_name')->label('Khách hàng'),
                            TextInput::make('industry')->label('Lĩnh vực'),
                            DatePicker::make('completed_at')->label('Hoàn thành'),
                            TextInput::make('video_url')->label('Video URL')->url(),
                            CuratorPicker::make('curator_media_id')->label('Ảnh đại diện')->relationship('curatorMedia', 'id')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->columnSpanFull(),
                            CuratorPicker::make('gallery')->label('Thư viện hình ảnh')->multiple()->disk('public')->constrained()->acceptedFileTypes(['image/*'])->helperText('Chọn thêm ảnh để hiển thị tại trang chi tiết.')->columnSpanFull(),
                            Textarea::make('excerpt')
                                ->label('Mô tả dự án')
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
                        ->icon(Heroicon::OutlinedMagnifyingGlass)
                        ->schema([
                            SeoImageField::make(),
                            TextInput::make('seo_title')
                                ->label('SEO title')
                                ->maxLength(255)
                                ->formatStateUsing(fn (?string $state, ?Project $record): ?string => $state ?: ContentSeoFallbacks::title($record?->title))
                                ->columnSpanFull(),
                            Textarea::make('seo_description')
                                ->label('Meta description')
                                ->rows(5)
                                ->formatStateUsing(fn (?string $state, ?Project $record): ?string => $state ?: ContentSeoFallbacks::description($record?->excerpt))
                                ->columnSpanFull(),
                        ])
                        ->columns(2),
                    Section::make('Câu hỏi thường gặp')
                        ->icon(Heroicon::OutlinedQuestionMarkCircle)
                        ->description('Chỉ hiển thị tại chi tiết dự án khi có ít nhất một câu hỏi và câu trả lời.')
                        ->schema([
                            TextInput::make('faq_title')
                                ->label('Tiêu đề')
                                ->maxLength(255)
                                ->columnSpanFull(),
                            Textarea::make('faq_description')
                                ->label('Mô tả ngắn')
                                ->rows(2)
                                ->columnSpanFull(),
                            Repeater::make('faq_items')
                                ->label('Danh sách câu hỏi')
                                ->schema([
                                    TextInput::make('question')
                                        ->label('Câu hỏi')
                                        ->maxLength(500)
                                        ->columnSpanFull(),
                                    Textarea::make('answer')
                                        ->label('Trả lời')
                                        ->rows(4)
                                        ->columnSpanFull(),
                                ])
                                ->addActionLabel('Thêm câu hỏi')
                                ->reorderable()
                                ->cloneable()
                                ->collapsible()
                                ->itemLabel(fn (array $state): ?string => $state['question'] ?? 'Câu hỏi mới')
                                ->columnSpanFull(),
                        ])
                        ->columns(1),
                    Section::make('Nội dung liên quan')
                        ->icon(Heroicon::OutlinedLink)
                        ->description('Chọn dịch vụ và bài viết cần hiển thị tại chi tiết dự án.')
                        ->schema([
                            Select::make('backstageServices')
                                ->label('Dịch vụ liên quan')
                                ->relationship('backstageServices', 'title')
                                ->multiple()
                                ->searchable()
                                ->preload()
                                ->helperText('Chỉ dịch vụ đã xuất bản mới hiển thị ở trang ngoài.')
                                ->columnSpanFull(),
                            Select::make('relatedPosts')
                                ->label('Bài viết liên quan')
                                ->relationship('relatedPosts', 'title')
                                ->multiple()
                                ->searchable()
                                ->preload()
                                ->helperText('Chỉ bài viết đã xuất bản mới hiển thị ở trang ngoài.')
                                ->columnSpanFull(),
                        ])
                        ->columns(1),
                ])
                    ->columnSpan(['lg' => 2]),
                Section::make('Phân loại & hiển thị')
                    ->icon(Heroicon::OutlinedCog6Tooth)
                    ->schema([
                        Select::make('project_category_id')->label('Danh mục')->relationship('category', 'name')->searchable()->preload(),
                        Select::make('status')->label('Trạng thái')->options(['draft' => 'Bản nháp', 'published' => 'Đã xuất bản', 'pending' => 'Chờ duyệt', 'private' => 'Riêng tư'])->required()->default('draft'),
                        TextInput::make('sort_order')->label('Thứ tự')->numeric()->default(fn (): int => ((int) Project::query()->max('sort_order')) + 1),
                        Toggle::make('is_featured')->label('Dự án nổi bật'),
                    ])
                    ->columns(1)
                    ->columnSpan(['lg' => 1]),
            ]);
    }
}
