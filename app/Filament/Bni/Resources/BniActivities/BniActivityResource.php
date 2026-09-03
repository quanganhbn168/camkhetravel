<?php

namespace App\Filament\Bni\Resources\BniActivities;

use App\Filament\Bni\Resources\BniActivities\Pages\CreateBniActivity;
use App\Filament\Bni\Resources\BniActivities\Pages\EditBniActivity;
use App\Filament\Bni\Resources\BniActivities\Pages\ListBniActivities;
use App\Models\BniActivity;
use App\Support\Bni\BniMediaService;
use App\Support\Bni\BniPanelAccess;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BniActivityResource extends Resource
{
    protected static ?string $model = BniActivity::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Album ảnh';

    protected static ?string $modelLabel = 'album ảnh';

    protected static ?string $pluralModelLabel = 'Album ảnh';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return 'Hình ảnh';
    }

    public static function canViewAny(): bool
    {
        return BniPanelAccess::canManageEverything();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Album ảnh')
                ->icon('heroicon-o-rectangle-stack')
                ->description('Album là nhóm ảnh theo từng phần của chương trình, ví dụ Trước lễ chuyển giao hoặc Gala Dinner. Ảnh cụ thể được thêm tại Thư viện ảnh.')
                ->schema([
                    Select::make('bni_event_id')->label('Sự kiện')->relationship('event', 'title')->required()->searchable()->preload()->columnSpanFull(),
                    TextInput::make('title')->label('Tên album')->required()->maxLength(255)->columnSpanFull(),
                    Select::make('type')->label('Phong cách hiển thị')->options([
                        'general' => 'Mặc định',
                        'handover' => 'Lễ chuyển giao',
                        'gala' => 'Gala',
                        'pickleball' => 'Pickleball',
                    ])->default('general')->required()->columnSpanFull(),
                    Textarea::make('description')->label('Mô tả')->rows(3)->columnSpanFull(),
                    SpatieMediaLibraryFileUpload::make('image')
                        ->label('Ảnh đại diện album')
                        ->collection('image')
                        ->conversion(BniMediaService::WEBP_CONVERSION)
                        ->disk('public')
                        ->image()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->columnSpanFull(),
                    TextInput::make('link_url')
                        ->label('Liên kết')
                        ->maxLength(2048)
                        ->helperText('Có thể dùng đường dẫn nội bộ hoặc URL đầy đủ.')
                        ->columnSpanFull(),
                    Toggle::make('is_active')->label('Hiển thị')->default(true)->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')->label('Ảnh')->collection('image')->conversion(BniMediaService::WEBP_CONVERSION)->square(),
                TextColumn::make('title')->label('Album ảnh')->searchable()->wrap(),
                TextColumn::make('event.title')->label('Sự kiện')->sortable(),
                TextColumn::make('gallery_items_count')->counts('galleryItems')->label('Số ảnh')->sortable(),
                ToggleColumn::make('is_active')->label('Hiển thị'),
            ])
            ->filters([
                SelectFilter::make('bni_event_id')->label('Sự kiện')->relationship('event', 'title'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->slideOver(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBniActivities::route('/'),
            'create' => CreateBniActivity::route('/create'),
            'edit' => EditBniActivity::route('/{record}/edit'),
        ];
    }
}
