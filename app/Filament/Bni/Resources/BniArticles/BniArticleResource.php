<?php

namespace App\Filament\Bni\Resources\BniArticles;

use App\Filament\Bni\Resources\BniArticles\Pages\ManageBniArticles;
use App\Models\BniArticle;
use App\Support\Bni\BniPanelAccess;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
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
use Illuminate\Support\Str;

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

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(['lg' => 3])->components([
            Group::make([
                Section::make('Nội dung bài viết')->icon('heroicon-o-document-text')->schema([
                    TextInput::make('title')->label('Tiêu đề')->required()->maxLength(255)->live(onBlur: true)->afterStateUpdated(function (?string $state, $get, $set): void {
                        if (blank($get('slug')) && filled($state)) {
                            $set('slug', Str::slug($state));
                        }
                    })->columnSpanFull(),
                    TextInput::make('slug')->label('Slug')->required()->maxLength(255)->columnSpanFull(),
                    CuratorPicker::make('cover_media_id')->label('Ảnh đại diện')->relationship('coverMedia', 'id')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->columnSpanFull(),
                    Textarea::make('excerpt')->label('Mô tả ngắn')->rows(3)->columnSpanFull(),
                    RichEditor::make('body')->label('Nội dung')->columnSpanFull(),
                ])->columns(2),
            ])->columnSpan(['lg' => 2]),
            Section::make('Phân loại & xuất bản')->icon('heroicon-o-cog-6-tooth')->schema([
                Select::make('bni_event_id')->label('Sự kiện')->relationship('event', 'title', fn (Builder $query): Builder => BniPanelAccess::scopePublishedEvents($query))->searchable()->preload()->columnSpanFull(),
                Select::make('type')->label('Nhóm tin')->options(['event' => 'Tin sự kiện', 'chapter' => 'Tin chapter', 'pickleball' => 'Tin pickleball'])->required()->default('event')->columnSpanFull(),
                Select::make('bni_chapter_id')->label('Chapter')->relationship('chapter', 'name')->searchable()->preload()->visible(fn (): bool => BniPanelAccess::canManageEverything())->columnSpanFull(),
                Select::make('status')->label('Trạng thái')->options(['draft' => 'Bản nháp', 'published' => 'Đã xuất bản'])->required()->default('draft')->columnSpanFull(),
                Toggle::make('is_featured')->label('Tin nổi bật')->columnSpanFull(),
            ])->columnSpan(['lg' => 1]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->label('Tiêu đề')->searchable()->sortable()->wrap(),
            TextColumn::make('chapter.short_name')->label('Chapter')->badge()->toggleable(),
            TextColumn::make('type')->label('Nhóm')->badge()->formatStateUsing(fn (string $state): string => ['event' => 'Sự kiện', 'chapter' => 'Chapter', 'pickleball' => 'Pickleball'][$state] ?? $state),
            TextColumn::make('status')->label('Trạng thái')->badge(),
            TextColumn::make('published_at')->label('Xuất bản')->dateTime('d/m/Y H:i')->sortable(),
        ])->filters([
            SelectFilter::make('type')->label('Nhóm tin')->options(['event' => 'Tin sự kiện', 'chapter' => 'Tin chapter', 'pickleball' => 'Tin pickleball']),
            SelectFilter::make('status')->label('Trạng thái')->options(['draft' => 'Bản nháp', 'published' => 'Đã xuất bản']),
        ])->defaultSort('published_at', 'desc')->recordActions([
            EditAction::make()->mutateDataUsing(fn (array $data): array => BniPanelAccess::prepareArticleData($data)),
            DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageBniArticles::route('/')];
    }
}
