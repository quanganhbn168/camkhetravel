<?php

namespace App\Filament\Bni\Resources\BniSponsors;

use App\Filament\Bni\Resources\BniSponsors\Pages\CreateBniSponsor;
use App\Filament\Bni\Resources\BniSponsors\Pages\EditBniSponsor;
use App\Filament\Bni\Resources\BniSponsors\Pages\ListBniSponsors;
use App\Models\BniSponsor;
use App\Support\Bni\BniMediaService;
use App\Support\Bni\BniPanelAccess;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
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

class BniSponsorResource extends Resource
{
    protected static ?string $model = BniSponsor::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Nhà tài trợ';

    protected static ?string $modelLabel = 'nhà tài trợ';

    protected static ?string $pluralModelLabel = 'Nhà tài trợ';

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return 'Sự kiện BNI';
    }

    public static function canViewAny(): bool
    {
        return BniPanelAccess::canManageEverything();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Thông tin nhà tài trợ')
                ->icon('heroicon-o-building-office-2')
                ->description('Mỗi dòng là một logo nhà tài trợ thuộc một sự kiện. Có thể gắn link website hoặc fanpage riêng cho logo.')
                ->schema([
                    Select::make('bni_event_id')
                        ->label('Sự kiện')
                        ->relationship('event', 'title')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->columnSpanFull(),
                    Select::make('tier')
                        ->label('Hạng tài trợ')
                        ->options(BniSponsor::tierOptions())
                        ->required()
                        ->default(BniSponsor::TIER_CO_SPONSOR)
                        ->columnSpanFull(),
                    TextInput::make('name')
                        ->label('Tên nhà tài trợ')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    SpatieMediaLibraryFileUpload::make('logo')
                        ->label('Logo nhà tài trợ')
                        ->collection('logo')
                        ->conversion(BniMediaService::WEBP_CONVERSION)
                        ->disk('public')
                        ->image()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])
                        ->helperText('Ưu tiên PNG nền trong suốt hoặc SVG; logo sẽ được hiển thị vừa khung, không làm méo hình.')
                        ->columnSpanFull(),
                    TextInput::make('url')
                        ->label('Link website / fanpage (tuỳ chọn)')
                        ->url()
                        ->maxLength(2048)
                        ->placeholder('https://...')
                        ->helperText('Có thể để trống nếu logo không cần liên kết.')
                        ->columnSpanFull(),
                    TextInput::make('sort_order')
                        ->label('Thứ tự trong hạng')
                        ->numeric()
                        ->minValue(0)
                        ->default(0)
                        ->columnSpanFull(),
                    Toggle::make('is_active')
                        ->label('Hiển thị')
                        ->default(true)
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('logo')
                    ->label('Logo')
                    ->collection('logo')
                    ->conversion(BniMediaService::WEBP_CONVERSION)
                    ->square(),
                TextColumn::make('name')->label('Nhà tài trợ')->searchable()->sortable()->wrap(),
                TextColumn::make('event.title')->label('Sự kiện')->searchable()->sortable(),
                TextColumn::make('tier')
                    ->label('Hạng')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => BniSponsor::tierOptions()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        BniSponsor::TIER_DIAMOND => 'gray',
                        BniSponsor::TIER_GOLD => 'warning',
                        BniSponsor::TIER_SILVER => 'gray',
                        default => 'primary',
                    }),
                TextColumn::make('url')->label('Link')->limit(36)->placeholder('Không có')->toggleable(),
                TextColumn::make('sort_order')->label('Thứ tự')->sortable(),
                ToggleColumn::make('is_active')->label('Hiển thị'),
            ])
            ->filters([
                SelectFilter::make('bni_event_id')->label('Sự kiện')->relationship('event', 'title'),
                SelectFilter::make('tier')->label('Hạng tài trợ')->options(BniSponsor::tierOptions()),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->slideOver(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBniSponsors::route('/'),
            'create' => CreateBniSponsor::route('/create'),
            'edit' => EditBniSponsor::route('/{record}/edit'),
        ];
    }
}
