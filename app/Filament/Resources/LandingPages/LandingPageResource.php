<?php

namespace App\Filament\Resources\LandingPages;

use App\Filament\Resources\Concerns\UsesPrimaryKeyForRecordRoutes;
use App\Filament\Resources\LandingPages\Pages\CreateLandingPage;
use App\Filament\Resources\LandingPages\Pages\EditLandingPage;
use App\Filament\Resources\LandingPages\Pages\ListLandingPages;
use App\Filament\Resources\LandingPages\Schemas\LandingExperienceSchema;
use App\Filament\RichEditor\ScopedAttachCuratorMediaPlugin;
use App\Models\LandingPage;
use App\Support\Localization\LocalizedUrl;
use App\Support\Seo\ContentSeoFallbacks;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use BackedEnum;
use Filament\Actions\Action;
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
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LandingPageResource extends Resource
{
    use UsesPrimaryKeyForRecordRoutes;

    protected static ?string $model = LandingPage::class;

    protected static ?string $recordRouteKeyName = 'id';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;

    protected static ?string $navigationLabel = 'Landing pages';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return 'Nội dung website';
    }

    public static function getModelLabel(): string
    {
        return 'landing page';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Landing pages';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(['lg' => 3])
            ->components([
                Group::make([
                    Section::make('Nội dung landing page')
                        ->icon(Heroicon::OutlinedDocumentText)
                        ->schema([
                            TextInput::make('title')
                                ->label('Tên landing page')
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
                            TextInput::make('slug')->label('Đường dẫn (slug)')->maxLength(255)->formatStateUsing(fn (?string $state, ?LandingPage $record): ?string => $state ?: $record?->slug)->columnSpanFull(),
                            CuratorPicker::make('curator_media_id')->label('Ảnh đại diện / hero')->relationship('curatorMedia', 'id')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->columnSpanFull(),
                            Textarea::make('excerpt')->label('Mô tả ngắn')->rows(3)->columnSpanFull(),
                            RichEditor::make('body')->label('Nội dung bổ sung')->plugins([ScopedAttachCuratorMediaPlugin::make()])->columnSpanFull(),
                            TextInput::make('seo_title')->label('SEO title')->maxLength(255)->formatStateUsing(fn (?string $state, ?LandingPage $record): ?string => $state ?: ContentSeoFallbacks::title($record?->title))->columnSpanFull(),
                            Textarea::make('seo_description')->label('Meta description')->rows(4)->formatStateUsing(fn (?string $state, ?LandingPage $record): ?string => $state ?: ContentSeoFallbacks::description($record?->excerpt))->columnSpanFull(),
                        ])
                        ->columns(2),
                    Section::make('Dữ liệu hiển thị trong landing')
                        ->icon(Heroicon::OutlinedLink)
                        ->description('Các block đọc trực tiếp những quan hệ này từ database Laravel: danh mục dịch vụ, dịch vụ, dự án và blog.')
                        ->schema([
                            Select::make('serviceCategories')->label('Danh mục dịch vụ')->relationship('serviceCategories', 'name')->multiple()->searchable()->preload()->columnSpanFull(),
                            Select::make('services')->label('Dịch vụ liên quan')->relationship('services', 'title')->multiple()->searchable()->preload()->columnSpanFull(),
                            Select::make('projects')->label('Dự án liên quan')->relationship('projects', 'title')->multiple()->searchable()->preload()->columnSpanFull(),
                            Select::make('posts')->label('Bài viết / blog liên quan')->relationship('posts', 'title')->multiple()->searchable()->preload()->columnSpanFull(),
                        ])
                        ->columns(2),
                    ...LandingExperienceSchema::components(),
                ])->columnSpan(['lg' => 2]),
                Section::make('Trạng thái')
                    ->icon(Heroicon::OutlinedCog6Tooth)
                    ->schema([
                        Select::make('status')->label('Trạng thái')->options(['draft' => 'Bản nháp', 'published' => 'Đã xuất bản', 'pending' => 'Chờ duyệt', 'private' => 'Riêng tư'])->required()->default('draft'),
                        TextInput::make('sort_order')->label('Thứ tự')->numeric()->default(fn (): int => ((int) LandingPage::query()->max('sort_order')) + 1),
                        Toggle::make('is_featured')->label('Landing nổi bật'),
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
                TextColumn::make('title')->label('Landing page')->searchable()->sortable()->wrap(),
                TextColumn::make('template_key')->label('Template')->badge()->placeholder('Chưa chọn'),
                TextColumn::make('status')->label('Trạng thái')->badge(),
                TextColumn::make('projects_count')->counts('projects')->label('Dự án')->sortable(),
                TextColumn::make('posts_count')->counts('posts')->label('Blog')->sortable(),
                TextColumn::make('events_count')->counts('events')->label('Tracking')->sortable(),
                IconColumn::make('is_featured')->label('Nổi bật')->boolean(),
                TextColumn::make('updated_at')->label('Cập nhật')->dateTime('d/m/Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('template_key')->label('Template')->options(fn (): array => \App\Support\Landing\LandingTemplateRegistry::options()),
                SelectFilter::make('status')->label('Trạng thái')->options(['draft' => 'Bản nháp', 'published' => 'Đã xuất bản', 'pending' => 'Chờ duyệt', 'private' => 'Riêng tư']),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                Action::make('preview')->label('Xem trang')->icon(Heroicon::OutlinedArrowTopRightOnSquare)->url(fn (LandingPage $record): string => LocalizedUrl::landingPage($record))->openUrlInNewTab(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLandingPages::route('/'),
            'create' => CreateLandingPage::route('/create'),
            'edit' => EditLandingPage::route('/{record}/edit'),
        ];
    }
}
