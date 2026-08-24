<?php

namespace App\Filament\Resources\HeroSlides;

use App\Filament\Resources\HeroSlides\Pages\CreateHeroSlide;
use App\Filament\Resources\HeroSlides\Pages\EditHeroSlide;
use App\Filament\Resources\HeroSlides\Pages\ListHeroSlides;
use App\Models\HeroSlide;
use App\Support\Localization\LanguageCatalog;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HeroSlideResource extends Resource
{
    protected static ?string $model = HeroSlide::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $navigationLabel = 'Hero slides';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return 'Nội dung trang chủ';
    }

    public static function getModelLabel(): string
    {
        return 'slide hero';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Hero slides';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Nội dung tiếng Việt')
                ->icon(Heroicon::OutlinedPresentationChartLine)
                ->schema([
                    CuratorPicker::make('curator_media_id')
                        ->label('Ảnh nền')
                        ->relationship('curatorMedia', 'id')
                        ->disk('public')
                        ->constrained()
                        ->columnSpanFull(),
                    TextInput::make('eyebrow')->label('Nhãn nhỏ')->maxLength(255),
                    TextInput::make('title')->label('Tiêu đề')->required()->maxLength(255)->columnSpanFull(),
                    Textarea::make('description')->label('Mô tả')->rows(3)->columnSpanFull(),
                    TextInput::make('primary_label')->label('Nhãn nút chính')->maxLength(255),
                    TextInput::make('primary_url')->label('URL nút chính')->url()->maxLength(255),
                    TextInput::make('secondary_label')->label('Nhãn nút phụ')->maxLength(255),
                    TextInput::make('secondary_url')->label('URL nút phụ')->url()->maxLength(255),
                ])
                ->columns(2),
            Section::make('Bản dịch')
                ->icon(Heroicon::OutlinedLanguage)
                ->description('Chỉ thêm bản dịch khi nội dung đã sẵn sàng xuất bản cho ngôn ngữ đó.')
                ->schema([
                    Repeater::make('translations')
                        ->relationship()
                        ->schema([
                            Select::make('locale')
                                ->label('Ngôn ngữ')
                                ->options(fn (): array => app(LanguageCatalog::class)->options())
                                ->required(),
                            TextInput::make('eyebrow')->label('Nhãn nhỏ')->maxLength(255),
                            TextInput::make('title')->label('Tiêu đề')->maxLength(255)->columnSpanFull(),
                            Textarea::make('description')->label('Mô tả')->rows(3)->columnSpanFull(),
                            TextInput::make('primary_label')->label('Nhãn nút chính')->maxLength(255),
                            TextInput::make('primary_url')->label('URL nút chính')->url()->maxLength(255),
                            TextInput::make('secondary_label')->label('Nhãn nút phụ')->maxLength(255),
                            TextInput::make('secondary_url')->label('URL nút phụ')->url()->maxLength(255),
                        ])
                        ->columns(2)
                        ->addActionLabel('Thêm bản dịch')
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => app(LanguageCatalog::class)->find((string) ($state['locale'] ?? ''))?->name)
                        ->columnSpanFull(),
                ]),
            Section::make('Hiển thị')
                ->icon(Heroicon::OutlinedCog6Tooth)
                ->schema([
                    Toggle::make('is_active')->label('Hiển thị trên trang chủ')->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                CuratorColumn::make('curatorMedia')->label('Ảnh')->square(),
                TextColumn::make('title')->label('Nội dung')->searchable()->wrap(),
                TextColumn::make('translations_count')->counts('translations')->label('Bản dịch')->badge(),
                IconColumn::make('is_active')->label('Hiển thị')->boolean(),
                TextColumn::make('updated_at')->label('Cập nhật')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHeroSlides::route('/'),
            'create' => CreateHeroSlide::route('/create'),
            'edit' => EditHeroSlide::route('/{record}/edit'),
        ];
    }
}
