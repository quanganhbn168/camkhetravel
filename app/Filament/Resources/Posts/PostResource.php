<?php

namespace App\Filament\Resources\Posts;

use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Filament\Resources\Concerns\UsesPrimaryKeyForRecordRoutes;
use App\Filament\RichEditor\ScopedAttachCuratorMediaPlugin;
use App\Models\Post;
use App\Support\Seo\ContentSeoFallbacks;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\RichEditor;
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

class PostResource extends Resource
{
    use UsesPrimaryKeyForRecordRoutes;

    protected static ?string $model = Post::class;

    protected static ?string $recordRouteKeyName = 'id';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static ?string $navigationLabel = 'Bài viết';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 6;

    public static function getNavigationGroup(): ?string
    {
        return 'Nội dung website';
    }

    public static function getModelLabel(): string
    {
        return 'bài viết';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Bài viết';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(['lg' => 3])
            ->components([
                Group::make([
                    Section::make('Nội dung bài viết')
                        ->icon(Heroicon::OutlinedDocumentText)
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
                            CuratorPicker::make('curator_media_id')->label('Ảnh đại diện')->relationship('curatorMedia', 'id')->disk('public')->constrained()->columnSpanFull(),
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
                                ->columnSpanFull(),
                        ])
                        ->columns(2),
                    Section::make('SEO')
                        ->icon(Heroicon::OutlinedMagnifyingGlass)
                        ->schema([
                            TextInput::make('seo_title')
                                ->label('SEO title')
                                ->maxLength(255)
                                ->formatStateUsing(fn (?string $state, ?Post $record): ?string => $state ?: ContentSeoFallbacks::title($record?->title))
                                ->columnSpanFull(),
                            Textarea::make('seo_description')
                                ->label('Meta description')
                                ->rows(5)
                                ->formatStateUsing(fn (?string $state, ?Post $record): ?string => $state ?: ContentSeoFallbacks::description($record?->excerpt))
                                ->columnSpanFull(),
                        ])
                        ->columns(2),
                ])
                    ->columnSpan(['lg' => 2]),
                Section::make('Phân loại & hiển thị')
                    ->icon(Heroicon::OutlinedCog6Tooth)
                    ->schema([
                        CheckboxList::make('categories')->label('Chuyên mục')->relationship('categories', 'name')->columns(1)->searchable()->bulkToggleable(),
                        Select::make('status')->label('Trạng thái')->options(['draft' => 'Bản nháp', 'published' => 'Đã xuất bản', 'pending' => 'Chờ duyệt', 'private' => 'Riêng tư'])->required()->default('draft'),
                        Toggle::make('is_featured')->label('Bài viết nổi bật'),
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
                TextColumn::make('title')->label('Tiêu đề')->searchable()->sortable()->wrap(),
                TextColumn::make('categories.name')->label('Chuyên mục')->badge()->separator(', ')->toggleable(),
                TextColumn::make('status')->label('Trạng thái')->badge(),
                IconColumn::make('is_featured')->label('Nổi bật')->boolean(),
                TextColumn::make('published_at')->label('Xuất bản')->dateTime('d/m/Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('categories')->label('Chuyên mục')->relationship('categories', 'name'),
                SelectFilter::make('status')->label('Trạng thái')->options(['draft' => 'Bản nháp', 'published' => 'Đã xuất bản', 'pending' => 'Chờ duyệt', 'private' => 'Riêng tư']),
            ])
            ->defaultSort('published_at', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
        ];
    }
}
