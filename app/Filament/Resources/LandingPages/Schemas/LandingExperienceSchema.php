<?php

namespace App\Filament\Resources\LandingPages\Schemas;

use App\Support\Landing\LandingTemplateRegistry;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class LandingExperienceSchema
{
    /** @return array<Section> */
    public static function components(): array
    {
        return [
            Section::make('Bố cục landing page')
                ->icon(Heroicon::OutlinedRectangleGroup)
                ->description('Chọn giao diện chuẩn, page builder hoặc template chiến dịch được lập trình riêng.')
                ->schema([
                    ToggleButtons::make('layout_mode')
                        ->label('Chế độ hiển thị')
                        ->options([
                            'standard' => 'Trang chuẩn',
                            'builder' => 'Page builder',
                            'custom_template' => 'Template đặc thù',
                        ])
                        ->icons([
                            'standard' => Heroicon::OutlinedDocumentText,
                            'builder' => Heroicon::OutlinedRectangleGroup,
                            'custom_template' => Heroicon::OutlinedSparkles,
                        ])
                        ->default('standard')
                        ->required()
                        ->inline()
                        ->live()
                        ->columnSpanFull(),
                    Select::make('template_key')
                        ->label('Template giao diện')
                        ->options(fn (): array => LandingTemplateRegistry::options())
                        ->required(fn ($get): bool => $get('layout_mode') === 'custom_template')
                        ->visible(fn ($get): bool => $get('layout_mode') === 'custom_template')
                        ->native(false)
                        ->live()
                        ->afterStateUpdated(function (?string $state, $get, $set): void {
                            $set('landing_template_id', LandingTemplateRegistry::id($state));
                            $set('theme_settings', LandingTemplateRegistry::palette($state));
                            $set('template_settings', LandingTemplateRegistry::defaultSettings($state));

                            if (blank($get('sections'))) {
                                $set('sections', LandingTemplateRegistry::defaultSections($state));
                            }
                        })
                        ->columnSpanFull(),
                    Hidden::make('landing_template_id'),
                    Placeholder::make('template_preview')
                        ->label('Xem nhanh template')
                        ->content(fn ($get): HtmlString => self::templatePreview($get('template_key')))
                        ->visible(fn ($get): bool => $get('layout_mode') === 'custom_template')
                        ->columnSpanFull(),
                    ColorPicker::make('theme_settings.primary')->label('Màu chủ đạo')->default('#075c35'),
                    ColorPicker::make('theme_settings.accent')->label('Màu nhấn')->default('#d6aa43'),
                    ColorPicker::make('theme_settings.surface')->label('Màu nền nhẹ')->default('#f7f4e9'),
                    ColorPicker::make('theme_settings.ink')->label('Màu chữ chính')->default('#10291d'),
                    Toggle::make('show_header')->label('Hiện header website')->default(true),
                    Toggle::make('show_footer')->label('Hiện footer website')->default(true),
                ])
                ->columns(2),
            ...LandingTemplateSettingsSchema::sections(),
            Section::make('Các khối nội dung')
                ->icon(Heroicon::OutlinedSquaresPlus)
                ->description('Kéo thả để đổi thứ tự. Danh mục dịch vụ, dịch vụ, dự án, blog và bảng giá luôn đọc từ dữ liệu liên kết.')
                ->visible(fn ($get): bool => $get('layout_mode') !== 'standard')
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
            Section::make('Chiến dịch & tracking')
                ->icon(Heroicon::OutlinedChartBarSquare)
                ->visible(fn ($get): bool => $get('layout_mode') !== 'standard')
                ->schema([
                    DateTimePicker::make('campaign_starts_at')
                        ->label('Bắt đầu chiến dịch')
                        ->seconds(false),
                    DateTimePicker::make('campaign_ends_at')
                        ->label('Kết thúc chiến dịch')
                        ->seconds(false)
                        ->after('campaign_starts_at'),
                    Select::make('expired_behavior')
                        ->label('Khi hết hạn')
                        ->options([
                            'show_message' => 'Hiện thông báo hết hạn',
                            'hide_offer' => 'Ẩn ưu đãi và bảng giá',
                            'keep_showing' => 'Tiếp tục hiển thị',
                        ])
                        ->default('show_message')
                        ->required(),
                    Toggle::make('tracking_enabled')
                        ->label('Ghi nhận lượt xem và chuyển đổi')
                        ->default(true),
                    Textarea::make('expired_message')
                        ->label('Thông báo khi hết hạn')
                        ->rows(3)
                        ->maxLength(1000)
                        ->visible(fn ($get): bool => $get('expired_behavior') === 'show_message')
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ];
    }

    private static function templatePreview(mixed $key): HtmlString
    {
        $template = LandingTemplateRegistry::find(is_string($key) ? $key : null);

        if ($template === null) {
            return new HtmlString('<div style="padding:1rem;border:1px dashed #cbd5e1;border-radius:1rem;color:#64748b">Chọn một template để xem cấu trúc và bảng màu gợi ý.</div>');
        }

        $palette = $template['palette'];

        return new HtmlString(sprintf(
            '<div style="overflow:hidden;border:1px solid #e2e8f0;border-radius:1rem;background:%1$s"><div style="height:0.55rem;background:linear-gradient(90deg,%2$s 0 72%%,%3$s 72%%)"></div><div style="display:grid;gap:0.7rem;padding:1rem 1.1rem"><strong style="font-size:1rem;color:%4$s">%5$s</strong><span style="color:#64748b;line-height:1.55">%6$s</span><small style="color:%2$s;font-weight:700">%7$s</small></div></div>',
            e($palette['surface']),
            e($palette['primary']),
            e($palette['accent']),
            e($palette['ink']),
            e($template['label']),
            e($template['description']),
            e($template['use_case']),
        ));
    }

    /** @return array<Block> */
    private static function blocks(): array
    {
        return [
            Block::make('hero')
                ->label('Hero chiến dịch')
                ->icon(Heroicon::OutlinedPhoto)
                ->maxItems(1)
                ->schema([
                    self::blockId(),
                    TextInput::make('eyebrow')->label('Nhãn nhỏ')->maxLength(150),
                    TextInput::make('title')->label('Tiêu đề')->required()->maxLength(255)->columnSpanFull(),
                    Textarea::make('subtitle')->label('Mô tả')->rows(3)->maxLength(1000)->columnSpanFull(),
                    CuratorPicker::make('media_id')->label('Ảnh hero')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->columnSpanFull(),
                    TextInput::make('media_caption')->label('Chú thích ảnh')->maxLength(255)->columnSpanFull(),
                    TextInput::make('cta_label')->label('Nút chính')->default('Nhận hỗ trợ')->maxLength(100),
                    TextInput::make('cta_url')->label('Liên kết nút chính')->default('#tu-van')->maxLength(2048),
                    TextInput::make('secondary_label')->label('Nút phụ')->maxLength(100),
                    TextInput::make('secondary_url')->label('Liên kết nút phụ')->default('#uu-dai')->maxLength(2048),
                    TextInput::make('note')->label('Dòng ghi chú')->maxLength(255)->columnSpanFull(),
                ])
                ->columns(2),
            Block::make('countdown')
                ->label('Đếm ngược')
                ->icon(Heroicon::OutlinedClock)
                ->maxItems(1)
                ->schema([
                    self::blockId(),
                    TextInput::make('eyebrow')->label('Nhãn nhỏ')->maxLength(150),
                    TextInput::make('title')->label('Tiêu đề')->default('Thời gian chương trình còn lại')->maxLength(255)->columnSpanFull(),
                    DateTimePicker::make('starts_at')->label('Bắt đầu')->seconds(false),
                    DateTimePicker::make('ends_at')->label('Kết thúc')->seconds(false)->after('starts_at'),
                ])
                ->columns(2),
            Block::make('benefits')
                ->label('Quyền lợi / ưu đãi')
                ->icon(Heroicon::OutlinedGift)
                ->schema([
                    self::blockId(),
                    TextInput::make('eyebrow')->label('Nhãn nhỏ')->maxLength(150),
                    TextInput::make('title')->label('Tiêu đề')->required()->maxLength(255)->columnSpanFull(),
                    Textarea::make('description')->label('Mô tả')->rows(2)->maxLength(1000)->columnSpanFull(),
                    Repeater::make('items')
                        ->label('Nhóm quyền lợi')
                        ->schema([
                            TextInput::make('title')->label('Tên nhóm')->required()->maxLength(255)->columnSpanFull(),
                            Textarea::make('description')->label('Mô tả')->rows(2)->maxLength(500)->columnSpanFull(),
                            TagsInput::make('features')->label('Các quyền lợi')->placeholder('Nhập một quyền lợi rồi nhấn Enter')->columnSpanFull(),
                        ])
                        ->defaultItems(2)
                        ->reorderable()
                        ->cloneable()
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Nhóm quyền lợi mới')
                        ->columnSpanFull(),
                ]),
            Block::make('content_grid')
                ->label('Nhóm nội dung / giải pháp')
                ->icon(Heroicon::OutlinedSquaresPlus)
                ->schema([
                    self::blockId(),
                    TextInput::make('eyebrow')->label('Nhãn nhỏ')->maxLength(150),
                    TextInput::make('title')->label('Tiêu đề')->required()->maxLength(255)->columnSpanFull(),
                    Textarea::make('description')->label('Mô tả')->rows(2)->maxLength(1000)->columnSpanFull(),
                    Repeater::make('items')
                        ->label('Các nhóm nội dung')
                        ->schema([
                            TextInput::make('title')->label('Tên nhóm')->required()->maxLength(255)->columnSpanFull(),
                            Textarea::make('description')->label('Mô tả')->rows(3)->maxLength(1000)->columnSpanFull(),
                            TagsInput::make('features')->label('Điểm chính')->placeholder('Nhập một ý rồi nhấn Enter')->columnSpanFull(),
                        ])
                        ->reorderable()
                        ->cloneable()
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Nhóm nội dung mới')
                        ->columnSpanFull(),
                ]),
            Block::make('process')
                ->label('Quy trình / lộ trình')
                ->icon(Heroicon::OutlinedArrowPath)
                ->schema([
                    self::blockId(),
                    TextInput::make('eyebrow')->label('Nhãn nhỏ')->maxLength(150),
                    TextInput::make('title')->label('Tiêu đề')->required()->maxLength(255)->columnSpanFull(),
                    Textarea::make('description')->label('Mô tả')->rows(2)->maxLength(1000)->columnSpanFull(),
                    Repeater::make('items')
                        ->label('Các bước')
                        ->schema([
                            TextInput::make('step')->label('Số bước')->maxLength(20),
                            TextInput::make('title')->label('Tên bước')->required()->maxLength(255),
                            Textarea::make('description')->label('Mô tả')->rows(3)->maxLength(1000)->columnSpanFull(),
                        ])
                        ->reorderable()
                        ->cloneable()
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Bước mới')
                        ->columnSpanFull(),
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
                        ->cloneable()
                        ->columnSpanFull(),
                ]),
            Block::make('testimonials')
                ->label('Trích dẫn / phản hồi')
                ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                ->schema([
                    self::blockId(),
                    TextInput::make('eyebrow')->label('Nhãn nhỏ')->maxLength(150),
                    TextInput::make('title')->label('Tiêu đề')->required()->maxLength(255)->columnSpanFull(),
                    Repeater::make('items')
                        ->label('Các trích dẫn')
                        ->schema([
                            Textarea::make('quote')->label('Nội dung')->required()->rows(3)->maxLength(1000)->columnSpanFull(),
                            TextInput::make('author')->label('Người phát biểu')->maxLength(150),
                            TextInput::make('role')->label('Vai trò / đơn vị')->maxLength(150),
                        ])
                        ->reorderable()
                        ->cloneable()
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['author'] ?? 'Trích dẫn mới')
                        ->columnSpanFull(),
                ]),
            Block::make('projects')
                ->label('Dự án liên quan')
                ->icon(Heroicon::OutlinedPhoto)
                ->schema([
                    self::blockId(),
                    TextInput::make('eyebrow')->label('Nhãn nhỏ')->maxLength(150),
                    TextInput::make('title')->label('Tiêu đề')->default('Dự án liên quan')->maxLength(255)->columnSpanFull(),
                    Textarea::make('description')->label('Mô tả')->rows(2)->maxLength(1000)->columnSpanFull(),
                    TextInput::make('limit')->label('Số dự án tối đa')->numeric()->minValue(1)->maxValue(12)->default(6),
                ])
                ->columns(2),
            Block::make('service_categories')
                ->label('Danh mục dịch vụ')
                ->icon(Heroicon::OutlinedTag)
                ->schema([
                    self::blockId(),
                    TextInput::make('eyebrow')->label('Nhãn nhỏ')->maxLength(150),
                    TextInput::make('title')->label('Tiêu đề')->default('Danh mục dịch vụ')->maxLength(255)->columnSpanFull(),
                    Textarea::make('description')->label('Mô tả')->rows(2)->maxLength(1000)->columnSpanFull(),
                    TextInput::make('limit')->label('Số danh mục tối đa')->numeric()->minValue(1)->maxValue(12)->default(6),
                ])
                ->columns(2),
            Block::make('services')
                ->label('Dịch vụ liên quan')
                ->icon(Heroicon::OutlinedBriefcase)
                ->schema([
                    self::blockId(),
                    TextInput::make('eyebrow')->label('Nhãn nhỏ')->maxLength(150),
                    TextInput::make('title')->label('Tiêu đề')->default('Dịch vụ liên quan')->maxLength(255)->columnSpanFull(),
                    Textarea::make('description')->label('Mô tả')->rows(2)->maxLength(1000)->columnSpanFull(),
                    TextInput::make('limit')->label('Số dịch vụ tối đa')->numeric()->minValue(1)->maxValue(12)->default(6),
                ])
                ->columns(2),
            Block::make('posts')
                ->label('Bài viết / blog liên quan')
                ->icon(Heroicon::OutlinedNewspaper)
                ->schema([
                    self::blockId(),
                    TextInput::make('eyebrow')->label('Nhãn nhỏ')->maxLength(150),
                    TextInput::make('title')->label('Tiêu đề')->default('Bài viết liên quan')->maxLength(255)->columnSpanFull(),
                    Textarea::make('description')->label('Mô tả')->rows(2)->maxLength(1000)->columnSpanFull(),
                    TextInput::make('limit')->label('Số bài viết tối đa')->numeric()->minValue(1)->maxValue(12)->default(6),
                ])
                ->columns(2),
            Block::make('pricing')
                ->label('Bảng giá / gói hỗ trợ')
                ->icon(Heroicon::OutlinedReceiptPercent)
                ->schema([
                    self::blockId(),
                    TextInput::make('eyebrow')->label('Nhãn nhỏ')->maxLength(150),
                    TextInput::make('title')->label('Tiêu đề')->default('Gói hỗ trợ')->maxLength(255)->columnSpanFull(),
                    Textarea::make('description')->label('Mô tả')->rows(2)->maxLength(1000)->columnSpanFull(),
                ]),
            Block::make('rich_text')
                ->label('Nội dung tự do')
                ->icon(Heroicon::OutlinedDocumentText)
                ->schema([
                    self::blockId(),
                    TextInput::make('eyebrow')->label('Nhãn nhỏ')->maxLength(150),
                    TextInput::make('title')->label('Tiêu đề')->maxLength(255)->columnSpanFull(),
                    RichEditor::make('body')->label('Nội dung')->columnSpanFull(),
                ]),
            Block::make('gallery')
                ->label('Thư viện hình ảnh')
                ->icon(Heroicon::OutlinedPhoto)
                ->schema([
                    self::blockId(),
                    TextInput::make('eyebrow')->label('Nhãn nhỏ')->maxLength(150),
                    TextInput::make('title')->label('Tiêu đề')->default('Hình ảnh chương trình')->maxLength(255)->columnSpanFull(),
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
                    Repeater::make('items')
                        ->label('Danh sách câu hỏi')
                        ->schema([
                            TextInput::make('question')->label('Câu hỏi')->required()->maxLength(500)->columnSpanFull(),
                            Textarea::make('answer')->label('Trả lời')->required()->rows(4)->columnSpanFull(),
                        ])
                        ->reorderable()
                        ->cloneable()
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['question'] ?? 'Câu hỏi mới')
                        ->columnSpanFull(),
                ]),
            Block::make('lead_form')
                ->label('Form đăng ký')
                ->icon(Heroicon::OutlinedInboxArrowDown)
                ->maxItems(1)
                ->schema([
                    self::blockId(),
                    TextInput::make('title')->label('Tiêu đề')->default('Đăng ký nhận hỗ trợ')->required()->maxLength(255)->columnSpanFull(),
                    Textarea::make('description')->label('Mô tả')->rows(3)->maxLength(1000)->columnSpanFull(),
                    TextInput::make('button_label')->label('Nhãn nút gửi')->default('Gửi thông tin đăng ký')->maxLength(100),
                ]),
            Block::make('cta')
                ->label('Kêu gọi hành động')
                ->icon(Heroicon::OutlinedCursorArrowRays)
                ->schema([
                    self::blockId(),
                    TextInput::make('eyebrow')->label('Nhãn nhỏ')->maxLength(150),
                    TextInput::make('title')->label('Tiêu đề')->required()->maxLength(255)->columnSpanFull(),
                    Textarea::make('description')->label('Mô tả')->rows(2)->maxLength(1000)->columnSpanFull(),
                    TextInput::make('cta_label')->label('Nhãn nút')->default('Đăng ký ngay')->maxLength(100),
                    TextInput::make('cta_url')->label('Liên kết')->default('#tu-van')->maxLength(2048),
                ])
                ->columns(2),
        ];
    }

    private static function blockId(): Hidden
    {
        return Hidden::make('block_id')->default(fn (): string => Str::uuid()->toString());
    }
}
