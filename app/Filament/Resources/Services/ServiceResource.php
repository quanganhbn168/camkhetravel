<?php

namespace App\Filament\Resources\Services;

use App\Filament\Resources\Concerns\UsesPrimaryKeyForRecordRoutes;
use App\Filament\Resources\Services\Pages\CreateService;
use App\Filament\Resources\Services\Pages\EditService;
use App\Filament\Resources\Services\Pages\ListServices;
use App\Filament\Resources\ServicePricings\ServicePricingResource;
use App\Filament\RichEditor\ScopedAttachCuratorMediaPlugin;
use App\Models\Service;
use App\Support\Localization\LocalizedUrl;
use App\Support\Seo\ContentSeoFallbacks;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    use UsesPrimaryKeyForRecordRoutes;

    protected static ?string $model = Service::class;

    protected static ?string $recordRouteKeyName = 'id';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $navigationLabel = 'Dịch vụ';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Nội dung website';
    }

    public static function getModelLabel(): string
    {
        return 'dịch vụ';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Dịch vụ';
    }

    public static function form(Schema $schema): Schema
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
                            Select::make('backstageProjects')
                                ->label('Dự án đã triển khai')
                                ->relationship('backstageProjects', 'title')
                                ->multiple()
                                ->searchable()
                                ->preload()
                                ->helperText('Các dự án này hiển thị ở chi tiết dịch vụ và bộ lọc Dự án.')
                                ->columnSpanFull(),
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
                            TextInput::make('seo_title')->label('SEO title')->maxLength(255)->formatStateUsing(fn (?string $state, ?Service $record): ?string => $state ?: ContentSeoFallbacks::title($record?->title))->columnSpanFull(),
                            Textarea::make('seo_description')->label('Meta description')->rows(5)->formatStateUsing(fn (?string $state, ?Service $record): ?string => $state ?: ContentSeoFallbacks::description($record?->excerpt))->columnSpanFull(),
                        ])
                        ->columns(2),
                    Section::make('Câu hỏi thường gặp')
                        ->icon(Heroicon::OutlinedQuestionMarkCircle)
                        ->schema([
                            TextInput::make('faq_title')->label('Tiêu đề')->maxLength(255)->columnSpanFull(),
                            Textarea::make('faq_description')->label('Mô tả ngắn')->rows(2)->columnSpanFull(),
                            Repeater::make('faq_items')
                                ->label('Danh sách câu hỏi')
                                ->schema([
                                    TextInput::make('question')->label('Câu hỏi')->maxLength(500)->columnSpanFull(),
                                    Textarea::make('answer')->label('Trả lời')->rows(4)->columnSpanFull(),
                                ])
                                ->addActionLabel('Thêm câu hỏi')
                                ->reorderable()
                                ->cloneable()
                                ->collapsible()
                                ->itemLabel(fn (array $state): ?string => $state['question'] ?? 'Câu hỏi mới')
                                ->columnSpanFull(),
                        ])
                        ->columns(1),
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
                            TextInput::make('commitment_title')->label('Tiêu đề')->placeholder('Cam kết của THT MEDIA')->maxLength(255)->columnSpanFull(),
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
                        Select::make('status')->label('Trạng thái')->options(['draft' => 'Bản nháp', 'published' => 'Đã xuất bản', 'pending' => 'Chờ duyệt', 'private' => 'Riêng tư'])->required()->default('draft'),
                        TextInput::make('sort_order')->label('Thứ tự')->numeric()->default(fn (): int => ((int) Service::query()->max('sort_order')) + 1),
                        Toggle::make('is_featured')->label('Dịch vụ nổi bật'),
                    ])
                    ->columns(1)
                    ->columnSpan(['lg' => 1]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                CuratorColumn::make('curatorMedia')->label('Ảnh')->square(),
                TextColumn::make('title')->label('Dịch vụ')->searchable()->sortable()->wrap(),
                TextColumn::make('category.name')->label('Danh mục')->badge()->toggleable(),
                TextColumn::make('status')->label('Trạng thái')->badge(),
                TextColumn::make('backstage_projects_count')->counts('backstageProjects')->label('Dự án')->sortable(),
                TextColumn::make('pricingCatalog.title')->label('Bảng giá')->placeholder('Chưa có')->wrap()->toggleable(),
                IconColumn::make('is_featured')->label('Nổi bật')->boolean(),
                TextColumn::make('updated_at')->label('Cập nhật')->dateTime('d/m/Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('service_category_id')->label('Danh mục dịch vụ')->relationship('category', 'name'),
                SelectFilter::make('status')->label('Trạng thái')->options(['draft' => 'Bản nháp', 'published' => 'Đã xuất bản', 'pending' => 'Chờ duyệt', 'private' => 'Riêng tư']),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                Action::make('preview')->label('Xem dịch vụ')->icon(Heroicon::OutlinedArrowTopRightOnSquare)->url(fn (Service $record): string => LocalizedUrl::service($record))->openUrlInNewTab(),
                Action::make('pricing')
                    ->label(fn (Service $record): string => $record->pricingCatalog()->exists() ? 'Bảng giá' : 'Tạo bảng giá')
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->url(function (Service $record): string {
                        $pricing = $record->pricingCatalog()->first();

                        return $pricing
                            ? ServicePricingResource::getUrl('edit', ['record' => $pricing])
                            : ServicePricingResource::getUrl('create', ['service_id' => $record->id]);
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServices::route('/'),
            'create' => CreateService::route('/create'),
            'edit' => EditService::route('/{record}/edit'),
        ];
    }
}
