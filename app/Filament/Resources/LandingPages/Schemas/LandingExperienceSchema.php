<?php

namespace App\Filament\Resources\LandingPages\Schemas;

use App\Filament\RichEditor\ScopedAttachCuratorMediaPlugin;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

/** The one public landing layout: an ordered, database-backed page builder. */
final class LandingExperienceSchema
{
    /** @return array<Section> */
    public static function components(): array
    {
        return [
            Section::make('Hiển thị landing page')
                ->icon(Heroicon::OutlinedEye)
                ->description('Landing dùng chung header và footer của website. Có thể ẩn từng phần khi cần một trang độc lập.')
                ->schema([
                    Toggle::make('show_header')->label('Hiện header website')->default(true),
                    Toggle::make('show_footer')->label('Hiện footer website')->default(true),
                ])
                ->columns(2),
            Section::make('Các khối nội dung')
                ->icon(Heroicon::OutlinedSquaresPlus)
                ->description('Kéo thả để sắp xếp. Nội dung liên kết như dịch vụ, dự án, blog và bảng giá lấy trực tiếp từ CMS.')
                ->schema([
                    Builder::make('sections')
                        ->label('Bố cục trang')
                        ->blocks(self::blocks())
                        ->addActionLabel('Thêm khối nội dung')
                        ->cloneable()
                        ->collapsible()
                        ->blockPickerColumns(2)
                        ->columnSpanFull(),
                ]),
        ];
    }

    /** @return array<Block> */
    private static function blocks(): array
    {
        return [
            Block::make('hero')
                ->label('Hero')
                ->icon(Heroicon::OutlinedPhoto)
                ->maxItems(1)
                ->schema([
                    self::blockId(),
                    TextInput::make('eyebrow')->label('Nhãn nhỏ')->maxLength(150),
                    TextInput::make('title')->label('Tiêu đề')->required()->maxLength(255)->columnSpanFull(),
                    Textarea::make('subtitle')->label('Mô tả')->rows(3)->maxLength(1000)->columnSpanFull(),
                    CuratorPicker::make('media_id')->label('Ảnh hero')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->columnSpanFull(),
                    TextInput::make('media_caption')->label('Chú thích ảnh')->maxLength(255)->columnSpanFull(),
                    TextInput::make('cta_label')->label('Nút chính')->default('Nhận tư vấn')->maxLength(100),
                    TextInput::make('cta_url')->label('Liên kết nút chính')->default('#tu-van')->maxLength(2048),
                    TextInput::make('secondary_label')->label('Nút phụ')->maxLength(100),
                    TextInput::make('secondary_url')->label('Liên kết nút phụ')->maxLength(2048),
                    TextInput::make('note')->label('Dòng ghi chú')->maxLength(255)->columnSpanFull(),
                ])
                ->columns(2),
            Block::make('content_grid')
                ->label('Nhóm nội dung / giải pháp')
                ->icon(Heroicon::OutlinedSquaresPlus)
                ->schema([
                    self::blockId(),
                    TextInput::make('eyebrow')->label('Nhãn nhỏ')->maxLength(150),
                    TextInput::make('title')->label('Tiêu đề')->required()->maxLength(255)->columnSpanFull(),
                    Textarea::make('description')->label('Mô tả')->rows(2)->maxLength(1000)->columnSpanFull(),
                    self::itemsRepeater('Các nhóm nội dung', [
                        TextInput::make('title')->label('Tên nhóm')->required()->maxLength(255)->columnSpanFull(),
                        Textarea::make('description')->label('Mô tả')->rows(3)->maxLength(1000)->columnSpanFull(),
                        TagsInput::make('features')->label('Điểm chính')->placeholder('Nhập một ý rồi nhấn Enter')->columnSpanFull(),
                    ]),
                ]),
            Block::make('process')
                ->label('Quy trình / lộ trình')
                ->icon(Heroicon::OutlinedArrowPath)
                ->schema([
                    self::blockId(),
                    TextInput::make('eyebrow')->label('Nhãn nhỏ')->maxLength(150),
                    TextInput::make('title')->label('Tiêu đề')->required()->maxLength(255)->columnSpanFull(),
                    Textarea::make('description')->label('Mô tả')->rows(2)->maxLength(1000)->columnSpanFull(),
                    self::itemsRepeater('Các bước', [
                        TextInput::make('step')->label('Số bước')->maxLength(20),
                        TextInput::make('title')->label('Tên bước')->required()->maxLength(255),
                        Textarea::make('description')->label('Mô tả')->rows(3)->maxLength(1000)->columnSpanFull(),
                    ]),
                ]),
            Block::make('stats')
                ->label('Số liệu nổi bật')
                ->icon(Heroicon::OutlinedChartBar)
                ->schema([
                    self::blockId(),
                    TextInput::make('eyebrow')->label('Nhãn nhỏ')->maxLength(150),
                    TextInput::make('title')->label('Tiêu đề')->maxLength(255)->columnSpanFull(),
                    Repeater::make('items')
                        ->label('Số liệu')
                        ->schema([
                            TextInput::make('value')->label('Giá trị')->required()->maxLength(50),
                            TextInput::make('title')->label('Nhãn')->required()->maxLength(150),
                        ])
                        ->columns(2)
                        ->reorderable()
                        ->collapsible()
                        ->columnSpanFull(),
                ]),
            Block::make('testimonials')
                ->label('Phản hồi khách hàng')
                ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                ->schema([
                    self::blockId(),
                    TextInput::make('eyebrow')->label('Nhãn nhỏ')->maxLength(150),
                    TextInput::make('title')->label('Tiêu đề')->required()->maxLength(255)->columnSpanFull(),
                    self::itemsRepeater('Các trích dẫn', [
                        Textarea::make('quote')->label('Nội dung')->required()->rows(3)->maxLength(1000)->columnSpanFull(),
                        TextInput::make('author')->label('Người phát biểu')->maxLength(150),
                        TextInput::make('role')->label('Vai trò / đơn vị')->maxLength(150),
                    ]),
                ]),
            Block::make('projects')
                ->label('Dự án liên quan')
                ->icon(Heroicon::OutlinedPhoto)
                ->schema(self::linkedContentFields('Dự án liên quan', 'Số dự án tối đa', 'Dự án liên quan')),
            Block::make('services')
                ->label('Dịch vụ liên quan')
                ->icon(Heroicon::OutlinedBriefcase)
                ->schema(self::linkedContentFields('Dịch vụ liên quan', 'Số dịch vụ tối đa', 'Dịch vụ liên quan')),
            Block::make('posts')
                ->label('Bài viết liên quan')
                ->icon(Heroicon::OutlinedNewspaper)
                ->schema(self::linkedContentFields('Bài viết / blog liên quan', 'Số bài viết tối đa', 'Bài viết liên quan')),
            Block::make('pricing')
                ->label('Bảng giá')
                ->icon(Heroicon::OutlinedReceiptPercent)
                ->schema([
                    self::blockId(),
                    TextInput::make('eyebrow')->label('Nhãn nhỏ')->maxLength(150),
                    TextInput::make('title')->label('Tiêu đề')->default('Bảng giá dịch vụ')->maxLength(255)->columnSpanFull(),
                    Textarea::make('description')->label('Mô tả')->rows(2)->maxLength(1000)->columnSpanFull(),
                ]),
            Block::make('rich_text')
                ->label('Nội dung tự do')
                ->icon(Heroicon::OutlinedDocumentText)
                ->schema([
                    self::blockId(),
                    TextInput::make('eyebrow')->label('Nhãn nhỏ')->maxLength(150),
                    TextInput::make('title')->label('Tiêu đề')->maxLength(255)->columnSpanFull(),
                    RichEditor::make('body')
                        ->label('Nội dung')
                        ->plugins([ScopedAttachCuratorMediaPlugin::make()])
                        ->enableToolbarButtons(['attachCuratorMedia'])
                        ->disableToolbarButtons(['attachFiles'])
                        ->columnSpanFull(),
                ]),
            Block::make('gallery')
                ->label('Thư viện hình ảnh')
                ->icon(Heroicon::OutlinedPhoto)
                ->schema([
                    self::blockId(),
                    TextInput::make('eyebrow')->label('Nhãn nhỏ')->maxLength(150),
                    TextInput::make('title')->label('Tiêu đề')->default('Hình ảnh')->maxLength(255)->columnSpanFull(),
                    Textarea::make('description')->label('Mô tả')->rows(2)->maxLength(1000)->columnSpanFull(),
                    CuratorPicker::make('media_ids')->label('Hình ảnh')->multiple()->disk('public')->constrained()->acceptedFileTypes(['image/*'])->columnSpanFull(),
                ]),
            Block::make('faqs')
                ->label('Câu hỏi thường gặp')
                ->icon(Heroicon::OutlinedQuestionMarkCircle)
                ->schema([
                    self::blockId(),
                    TextInput::make('eyebrow')->label('Nhãn nhỏ')->maxLength(150),
                    TextInput::make('title')->label('Tiêu đề')->default('Câu hỏi thường gặp')->maxLength(255)->columnSpanFull(),
                    Textarea::make('description')->label('Mô tả')->rows(2)->maxLength(1000)->columnSpanFull(),
                    self::itemsRepeater('Danh sách câu hỏi', [
                        TextInput::make('question')->label('Câu hỏi')->required()->maxLength(500)->columnSpanFull(),
                        Textarea::make('answer')->label('Trả lời')->required()->rows(4)->columnSpanFull(),
                    ]),
                ]),
            Block::make('lead_form')
                ->label('Form tư vấn')
                ->icon(Heroicon::OutlinedInboxArrowDown)
                ->maxItems(1)
                ->schema([
                    self::blockId(),
                    TextInput::make('title')->label('Tiêu đề')->default('Đăng ký nhận tư vấn')->required()->maxLength(255)->columnSpanFull(),
                    Textarea::make('description')->label('Mô tả')->rows(3)->maxLength(1000)->columnSpanFull(),
                    TextInput::make('button_label')->label('Nhãn nút gửi')->default('Gửi thông tin')->maxLength(100),
                ]),
            Block::make('cta')
                ->label('Kêu gọi hành động')
                ->icon(Heroicon::OutlinedCursorArrowRays)
                ->schema([
                    self::blockId(),
                    TextInput::make('eyebrow')->label('Nhãn nhỏ')->maxLength(150),
                    TextInput::make('title')->label('Tiêu đề')->required()->maxLength(255)->columnSpanFull(),
                    Textarea::make('description')->label('Mô tả')->rows(2)->maxLength(1000)->columnSpanFull(),
                    TextInput::make('cta_label')->label('Nhãn nút')->default('Liên hệ')->maxLength(100),
                    TextInput::make('cta_url')->label('Liên kết')->default('#tu-van')->maxLength(2048),
                ])
                ->columns(2),
        ];
    }

    /** @return array<int, mixed> */
    private static function linkedContentFields(string $title, string $limitLabel, string $defaultTitle): array
    {
        return [
            self::blockId(),
            TextInput::make('eyebrow')->label('Nhãn nhỏ')->maxLength(150),
            TextInput::make('title')->label('Tiêu đề')->default($defaultTitle)->maxLength(255)->columnSpanFull(),
            Textarea::make('description')->label('Mô tả')->rows(2)->maxLength(1000)->columnSpanFull(),
            TextInput::make('limit')->label($limitLabel)->numeric()->minValue(1)->maxValue(12)->default(6),
        ];
    }

    /** @param array<int, mixed> $schema */
    private static function itemsRepeater(string $label, array $schema): Repeater
    {
        return Repeater::make('items')
            ->label($label)
            ->schema($schema)
            ->reorderable()
            ->cloneable()
            ->collapsible()
            ->itemLabel(fn (array $state): ?string => $state['title'] ?? $state['question'] ?? $state['author'] ?? 'Mục mới')
            ->columnSpanFull();
    }

    private static function blockId(): Hidden
    {
        return Hidden::make('block_id')->default(fn (): string => Str::uuid()->toString());
    }
}
