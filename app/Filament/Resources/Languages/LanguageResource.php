<?php

namespace App\Filament\Resources\Languages;

use App\Filament\Resources\Languages\Pages\CreateLanguage;
use App\Filament\Resources\Languages\Pages\EditLanguage;
use App\Filament\Resources\Languages\Pages\ListLanguages;
use App\Models\Language;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LanguageResource extends Resource
{
    protected static ?string $model = Language::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLanguage;

    protected static ?string $navigationLabel = 'Ngôn ngữ';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return 'Hệ thống';
    }

    public static function getModelLabel(): string
    {
        return 'ngôn ngữ';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Ngôn ngữ';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Thông tin ngôn ngữ')
                ->icon(Heroicon::OutlinedLanguage)
                ->description('Thêm ngôn ngữ mới tại đây. Route locale sẽ tự nhận mã mới; route cache sẽ tự được xóa khi lưu.')
                ->schema([
                    TextInput::make('name')
                        ->label('Tên hiển thị')
                        ->required()
                        ->maxLength(100)
                        ->columnSpanFull(),
                    TextInput::make('code')
                        ->label('Mã locale')
                        ->helperText('Ví dụ: en, ko, zh hoặc pt-BR.')
                        ->required()
                        ->regex('/^[a-z]{2,8}(?:-[A-Za-z0-9]{2,8})?$/')
                        ->unique(ignoreRecord: true)
                        ->maxLength(10),
                    TextInput::make('native_name')
                        ->label('Nhãn ngắn/native')
                        ->required()
                        ->maxLength(100),
                    TextInput::make('og_locale')
                        ->label('Open Graph locale')
                        ->helperText('Ví dụ: vi_VN, en_US, ko_KR.')
                        ->required()
                        ->maxLength(20),
                    TextInput::make('sort_order')
                        ->label('Thứ tự')
                        ->numeric()
                        ->default(fn (): int => ((int) Language::query()->max('sort_order')) + 1),
                ])
                ->columns(2),
            Section::make('Xuất bản và SEO')
                ->icon(Heroicon::OutlinedGlobeAlt)
                ->description('Chỉ bật Google index khi nội dung của ngôn ngữ này đã được dịch và xuất bản đầy đủ.')
                ->schema([
                    Toggle::make('is_active')
                        ->label('Hiển thị trong bộ chuyển ngôn ngữ')
                        ->default(true),
                    Toggle::make('is_indexable')
                        ->label('Cho phép Google lập chỉ mục')
                        ->default(false),
                    Toggle::make('is_default')
                        ->label('Ngôn ngữ mặc định')
                        ->helperText('Chọn ngôn ngữ mới làm mặc định sẽ tự bỏ mặc định ở ngôn ngữ cũ.')
                        ->disabled(fn (?Language $record): bool => (bool) $record?->is_default)
                        ->dehydrated(),
                ])
                ->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Ngôn ngữ')->searchable()->sortable(),
                TextColumn::make('code')->label('Mã')->badge()->searchable(),
                TextColumn::make('native_name')->label('Nhãn'),
                TextColumn::make('og_locale')->label('OG locale')->toggleable(),
                IconColumn::make('is_default')->label('Mặc định')->boolean(),
                IconColumn::make('is_active')->label('Hiển thị')->boolean(),
                IconColumn::make('is_indexable')->label('Index')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->visible(fn (Language $record): bool => ! $record->is_default),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLanguages::route('/'),
            'create' => CreateLanguage::route('/create'),
            'edit' => EditLanguage::route('/{record}/edit'),
        ];
    }
}
