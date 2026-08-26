<?php

namespace App\Filament\Resources\Services;

use App\Filament\Resources\Services\Pages\CreateService;
use App\Filament\Resources\Services\Pages\EditService;
use App\Filament\Resources\Services\Pages\ListServices;
use App\Filament\Resources\Concerns\UsesPrimaryKeyForRecordRoutes;
use App\Filament\RichEditor\ScopedAttachCuratorMediaPlugin;
use App\Models\Landing;
use App\Support\Seo\ContentSeoFallbacks;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
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

    protected static ?string $model = Landing::class;

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
                                ->formatStateUsing(fn (?string $state, ?Landing $record): ?string => $state ?: $record?->slug)
                                ->columnSpanFull(),
                            CuratorPicker::make('curator_media_id')->label('Ảnh đại diện')->relationship('curatorMedia', 'id')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->columnSpanFull(),
                            CuratorPicker::make('pricing_media_id')->label('Bảng giá dịch vụ')->relationship('pricingMedia', 'id')->disk('public')->constrained()->helperText('Ảnh hoặc PDF bảng giá. Nếu có gói giá cấu trúc, vẫn quản lý thêm tại mục Bảng giá.')->columnSpanFull(),
                            CuratorPicker::make('backstage_gallery')->label('Ảnh hậu trường')->multiple()->disk('public')->constrained()->acceptedFileTypes(['image/*'])->helperText('Ảnh hậu trường gắn trực tiếp với dịch vụ; hiển thị thành một phần riêng trên trang công khai.')->columnSpanFull(),
                            CuratorPicker::make('gallery')->label('Thư viện hình ảnh')->multiple()->disk('public')->constrained()->acceptedFileTypes(['image/*'])->helperText('Chọn thêm ảnh để hiển thị tại trang chi tiết.')->columnSpanFull(),
                            Select::make('backstageProjects')
                                ->label('Dự án hậu trường')
                                ->relationship('backstageProjects', 'title')
                                ->multiple()
                                ->searchable()
                                ->preload()
                                ->helperText('Các dự án này sẽ hiện ở phần “Hậu trường” của trang dịch vụ và được lọc khi khách xem Dự án.')
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
                            TextInput::make('seo_title')
                                ->label('SEO title')
                                ->maxLength(255)
                                ->formatStateUsing(fn (?string $state, ?Landing $record): ?string => $state ?: ContentSeoFallbacks::title($record?->title))
                                ->columnSpanFull(),
                            Textarea::make('seo_description')
                                ->label('Meta description')
                                ->rows(5)
                                ->formatStateUsing(fn (?string $state, ?Landing $record): ?string => $state ?: ContentSeoFallbacks::description($record?->excerpt))
                                ->columnSpanFull(),
                        ])
                        ->columns(2),
                    Section::make('Câu hỏi thường gặp')
                        ->icon(Heroicon::OutlinedQuestionMarkCircle)
                        ->description('Chỉ hiển thị khi dịch vụ có ít nhất một câu hỏi và câu trả lời.')
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
                ])
                    ->columnSpan(['lg' => 2]),
                Section::make('Phân loại & hiển thị')
                    ->icon(Heroicon::OutlinedCog6Tooth)
                    ->schema([
                        Select::make('landing_category_id')->label('Nhóm dịch vụ')->relationship('category', 'name')->searchable()->preload(),
                        Select::make('status')->label('Trạng thái')->options([
                            'draft' => 'Bản nháp',
                            'published' => 'Đã xuất bản',
                            'pending' => 'Chờ duyệt',
                            'private' => 'Riêng tư',
                        ])->required()->default('draft'),
                        TextInput::make('sort_order')->label('Thứ tự')->numeric()->default(fn (): int => ((int) Landing::query()->max('sort_order')) + 1),
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
                IconColumn::make('is_featured')->label('Nổi bật')->boolean(),
                TextColumn::make('updated_at')->label('Cập nhật')->dateTime('d/m/Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('landing_category_id')->label('Nhóm dịch vụ')->relationship('category', 'name'),
                SelectFilter::make('status')->label('Trạng thái')->options(['draft' => 'Bản nháp', 'published' => 'Đã xuất bản', 'pending' => 'Chờ duyệt', 'private' => 'Riêng tư']),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
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
