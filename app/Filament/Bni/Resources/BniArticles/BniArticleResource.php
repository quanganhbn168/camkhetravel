<?php

namespace App\Filament\Bni\Resources\BniArticles;

use App\Filament\Bni\Resources\BniArticles\Pages\CreateBniArticle;
use App\Filament\Bni\Resources\BniArticles\Pages\EditBniArticle;
use App\Filament\Bni\Resources\BniArticles\Pages\ListBniArticles;
use App\Models\BniArticle;
use App\Support\Bni\BniMediaService;
use App\Support\Bni\BniPanelAccess;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BniArticleResource extends Resource
{
    protected static ?string $model = BniArticle::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationLabel = 'Tin tức BNI';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return 'Lễ chuyển giao';
    }

    public static function canViewAny(): bool
    {
        return BniPanelAccess::canManageChapterContent();
    }

    public static function getEloquentQuery(): Builder
    {
        return BniPanelAccess::scopeChapter(parent::getEloquentQuery());
    }

    public static function canEdit(Model $record): bool
    {
        return self::canManageRecord($record);
    }

    public static function canDelete(Model $record): bool
    {
        return self::canManageRecord($record);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Group::make([
                Section::make('Nội dung bài viết')->icon('heroicon-o-document-text')->schema([
                    TextInput::make('title')->label('Tiêu đề')->required()->maxLength(255)->columnSpanFull(),
                    SpatieMediaLibraryFileUpload::make('cover')->label('Ảnh đại diện')->collection('cover')->conversion(BniMediaService::WEBP_CONVERSION)->disk('public')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->columnSpanFull(),
                    Textarea::make('excerpt')->label('Mô tả ngắn')->rows(3)->columnSpanFull(),
                    RichEditor::make('body')->label('Nội dung')->columnSpanFull(),
                ])->columns(2),
            ])->columnSpanFull(),
            Section::make('Phân loại & xuất bản')->icon('heroicon-o-cog-6-tooth')->schema([
                Select::make('bni_event_id')->label('Sự kiện')->relationship('event', 'title', fn (Builder $query): Builder => BniPanelAccess::scopeArticleEvents($query))->searchable()->preload()->columnSpanFull(),
                Select::make('type')->label('Khu vực sử dụng')->options(['event' => 'Lễ chuyển giao', 'chapter' => 'Trang chapter', 'pickleball' => 'Pickleball'])->required()->default('event')->columnSpanFull(),
                CheckboxList::make('categories')
                    ->label('Danh mục tin BNI')
                    ->relationship(
                        name: 'categories',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query): Builder => $query->where('is_active', true)->orderBy('sort_order'),
                    )
                    ->required()
                    ->searchable()
                    ->bulkToggleable()
                    ->columns(1)
                    ->columnSpanFull(),
                Select::make('bni_chapter_id')->label('Chapter')->relationship('chapter', 'name')->searchable()->preload()->visible(fn (): bool => BniPanelAccess::canManageEverything())->columnSpanFull(),
                Select::make('status')->label('Trạng thái')->options(['draft' => 'Bản nháp', 'published' => 'Đã xuất bản'])->required()->default('draft')->columnSpanFull(),
                Toggle::make('is_featured')->label('Tin nổi bật')->columnSpanFull(),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->label('Tiêu đề')->searchable()->sortable()->wrap(),
            TextColumn::make('chapter.short_name')->label('Chapter')->badge()->toggleable(),
            TextColumn::make('categories.name')->label('Danh mục')->badge()->separator(', ')->toggleable(),
            TextColumn::make('type')->label('Khu vực')->badge()->formatStateUsing(fn (string $state): string => ['event' => 'Lễ chuyển giao', 'chapter' => 'Chapter', 'pickleball' => 'Pickleball'][$state] ?? $state),
            TextColumn::make('status')->label('Trạng thái')->badge(),
            TextColumn::make('published_at')->label('Xuất bản')->dateTime('d/m/Y H:i')->sortable(),
        ])->filters([
            SelectFilter::make('categories')->label('Danh mục')->relationship('categories', 'name'),
            SelectFilter::make('type')->label('Khu vực')->options(['event' => 'Lễ chuyển giao', 'chapter' => 'Trang chapter', 'pickleball' => 'Pickleball']),
            SelectFilter::make('status')->label('Trạng thái')->options(['draft' => 'Bản nháp', 'published' => 'Đã xuất bản']),
        ])->defaultSort('published_at', 'desc')->recordActions([
            EditAction::make(),
            DeleteAction::make()->slideOver(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBniArticles::route('/'),
            'create' => CreateBniArticle::route('/create'),
            'edit' => EditBniArticle::route('/{record}/edit'),
        ];
    }

    private static function canManageRecord(Model $record): bool
    {
        if (BniPanelAccess::canManageEverything()) {
            return true;
        }

        return BniPanelAccess::isChapterManager()
            && (int) $record->getAttribute('bni_chapter_id') === BniPanelAccess::chapterId();
    }
}
