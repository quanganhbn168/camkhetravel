<?php

namespace App\Filament\Bni\Resources\BniEventSlides;

use App\Filament\Bni\Resources\BniEventSlides\Pages\CreateBniEventSlide;
use App\Filament\Bni\Resources\BniEventSlides\Pages\EditBniEventSlide;
use App\Filament\Bni\Resources\BniEventSlides\Pages\ListBniEventSlides;
use App\Models\BniEventSlide;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BniEventSlideResource extends Resource
{
    protected static ?string $model = BniEventSlide::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Slide sự kiện';

    protected static ?string $modelLabel = 'slide sự kiện';

    protected static ?string $pluralModelLabel = 'Slide sự kiện';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Lễ chuyển giao';
    }

    public static function canViewAny(): bool
    {
        return BniPanelAccess::canManageEverything();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Slide đầu trang')
                ->icon('heroicon-o-photo')
                ->description('Mỗi bản ghi là một slide độc lập. Ảnh là bắt buộc; phần chữ và nút có thể để trống.')
                ->schema([
                    Select::make('bni_event_id')
                        ->label('Sự kiện')
                        ->relationship('event', 'title', fn (Builder $query): Builder => $query->where('type', 'handover'))
                        ->required()
                        ->searchable()
                        ->preload()
                        ->columnSpanFull(),
                    SpatieMediaLibraryFileUpload::make('image')
                        ->label('Ảnh slide')
                        ->collection('image')
                        ->conversion(BniMediaService::WEBP_CONVERSION)
                        ->disk('public')
                        ->image()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->required()
                        ->columnSpanFull(),
                    TextInput::make('title')->label('Tiêu đề')->maxLength(255)->columnSpanFull(),
                    Textarea::make('description')->label('Mô tả')->rows(3)->columnSpanFull(),
                    TextInput::make('button_label')->label('Nhãn nút')->maxLength(255)->columnSpanFull(),
                    TextInput::make('button_url')
                        ->label('Liên kết nút')
                        ->maxLength(2048)
                        ->helperText('Có thể dùng liên kết nội bộ như #lich-trinh hoặc URL đầy đủ.')
                        ->columnSpanFull(),
                    TextInput::make('alt_text')->label('Mô tả ảnh')->maxLength(255)->columnSpanFull(),
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
                TextColumn::make('title')->label('Tiêu đề')->placeholder('Slide chỉ có ảnh')->searchable()->wrap(),
                TextColumn::make('event.title')->label('Sự kiện')->sortable(),
                TextColumn::make('is_active')->label('Hiển thị')->badge()->formatStateUsing(fn (bool $state): string => $state ? 'Có' : 'Ẩn'),
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
            'index' => ListBniEventSlides::route('/'),
            'create' => CreateBniEventSlide::route('/create'),
            'edit' => EditBniEventSlide::route('/{record}/edit'),
        ];
    }
}
