<?php

namespace App\Filament\Resources\Menus;

use App\Filament\Resources\Menus\Pages\CreateMenu;
use App\Filament\Resources\Menus\Pages\EditMenu;
use App\Filament\Resources\Menus\Pages\ListMenus;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\LandingPage;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Route;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBars3;

    protected static ?string $navigationLabel = 'Quản lý menu';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Cài đặt website';
    }

    public static function getModelLabel(): string
    {
        return 'menu';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Quản lý menu';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Thông tin menu')
                    ->icon(Heroicon::OutlinedBars3)
                    ->schema([
                        TextInput::make('name')
                            ->label('Tên menu')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('location')
                            ->label('Vị trí hiển thị')
                            ->maxLength(100)
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label('Kích hoạt menu')
                            ->default(true)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Section::make('Menu item')
                    ->icon(Heroicon::OutlinedListBullet)
                    ->description('Chọn route hoặc nội dung có sẵn. URL chỉ xuất hiện khi chọn liên kết tuỳ chỉnh.')
                    ->schema([
                        Repeater::make('topLevelItems')
                            ->label('Danh sách menu item')
                            ->relationship()
                            ->orderColumn('position')
                            ->schema([
                                ...self::menuItemFields(),
                                Repeater::make('children')
                                    ->label('Menu item con')
                                    ->relationship()
                                    ->orderColumn('position')
                                    ->schema(self::menuItemFields())
                                    ->columns(1)
                                    ->addActionLabel('Thêm menu item con')
                                    ->reorderable()
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['label'] ?? 'Menu item mới')
                                    ->columnSpanFull(),
                            ])
                            ->columns(1)
                            ->addActionLabel('Thêm menu item')
                            ->reorderable()
                            ->collapsible()
                            ->cloneable()
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? 'Menu item mới')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    /** @return array<int, mixed> */
    private static function menuItemFields(): array
    {
        return [
            TextInput::make('label')
                ->label('Nhãn hiển thị')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
            Select::make('menu_link_type')
                ->label('Loại liên kết')
                ->options([
                    'route' => 'Route hệ thống',
                    'service' => 'Dịch vụ',
                    'landing_page' => 'Landing page',
                    'project' => 'Dự án',
                    'post' => 'Bài viết',
                    'custom' => 'Liên kết tuỳ chỉnh',
                ])
                ->default('route')
                ->formatStateUsing(fn (?string $state, mixed $record): string => self::linkTypeFor($record))
                ->live()
                ->dehydrated(false)
                ->afterStateUpdated(function (?string $state, $set): void {
                    $set('linked_source_type', self::storedLinkType((string) $state));
                    $set('linked_source_id', null);
                    $set('url', null);
                    $set('route_name', null);
                    $set('custom_url', null);
                })
                ->columnSpanFull(),
            Hidden::make('url'),
            Select::make('route_name')
                ->label('Chọn route')
                ->options(self::routeOptions())
                ->searchable()
                ->preload()
                ->live()
                ->dehydrated(false)
                ->required(fn (Get $get): bool => $get('menu_link_type') === 'route')
                ->visible(fn (Get $get): bool => $get('menu_link_type') === 'route')
                ->formatStateUsing(fn (?string $state, mixed $record): ?string => self::routeNameFor($record, $state))
                ->afterStateUpdated(fn (?string $state, $set) => $set('url', $state))
                ->columnSpanFull(),
            Select::make('linked_source_id')
                ->label('Chọn nội dung')
                ->options(fn (Get $get): array => self::contentOptions((string) $get('menu_link_type')))
                ->searchable()
                ->preload()
                ->required(fn (Get $get): bool => self::usesContentReference((string) $get('menu_link_type')))
                ->visible(fn (Get $get): bool => self::usesContentReference((string) $get('menu_link_type')))
                ->columnSpanFull(),
            TextInput::make('custom_url')
                ->label('URL liên kết')
                ->placeholder('https://example.com/...')
                ->helperText('Không dùng #. Menu cha vẫn cần route hoặc URL hợp lệ dù có menu con.')
                ->maxLength(2048)
                ->rules(['not_in:#'])
                ->live(onBlur: true)
                ->dehydrated(false)
                ->required(fn (Get $get): bool => $get('menu_link_type') === 'custom')
                ->visible(fn (Get $get): bool => $get('menu_link_type') === 'custom')
                ->formatStateUsing(fn (?string $state, mixed $record): ?string => self::customUrlFor($record, $state))
                ->afterStateUpdated(fn (?string $state, $set) => $set('url', $state))
                ->columnSpanFull(),
            Select::make('target')
                ->label('Cách mở liên kết')
                ->options([
                    '_self' => 'Cùng tab',
                    '_blank' => 'Tab mới',
                ])
                ->default('_self')
                ->columnSpanFull(),
            Select::make('menu_presentation')
                ->label('Kiểu hiển thị')
                ->options([
                    '' => 'Menu thông thường',
                    'header-services' => 'Menu Dịch vụ có danh mục mở rộng',
                ])
                ->formatStateUsing(fn (?string $state, mixed $record): string => $record instanceof MenuItem && $record->css_classes === 'header-services'
                    ? 'header-services'
                    : '')
                ->live()
                ->dehydrated(false)
                ->afterStateUpdated(fn (?string $state, $set) => $set('css_classes', $state ?: null))
                ->columnSpanFull(),
            Hidden::make('linked_source_type')
                ->default('native_route')
                ->formatStateUsing(fn (?string $state, mixed $record): string => $record instanceof MenuItem
                    ? self::storedLinkType(self::linkTypeFor($record))
                    : ($state ?: 'native_route')),
            Hidden::make('css_classes'),
        ];
    }

    private static function linkTypeFor(mixed $record): string
    {
        if (! $record instanceof MenuItem) {
            return 'route';
        }

        return match ($record->linked_source_type) {
            'native_route' => 'route',
            'native_service' => 'service',
            'native_landing_page' => 'landing_page',
            'native_project' => 'project',
            'native_post' => 'post',
            'custom' => 'custom',
            Service::class => 'service',
            LandingPage::class => 'landing_page',
            Project::class => 'project',
            Post::class => 'post',
            default => self::routeNameFromValue($record->url) ? 'route' : 'custom',
        };
    }

    private static function storedLinkType(string $type): string
    {
        return match ($type) {
            'route' => 'native_route',
            'service' => 'native_service',
            'landing_page' => 'native_landing_page',
            'project' => 'native_project',
            'post' => 'native_post',
            default => 'custom',
        };
    }

    /** @return array<string, string> */
    private static function routeOptions(): array
    {
        return [
            'home' => 'Trang chủ',
            'about' => 'Giới thiệu',
            'services.index' => 'Tất cả dịch vụ',
            'pricing.index' => 'Bảng giá',
            'projects.index' => 'Tất cả dự án',
            'posts.index' => 'Tin tức',
            'contact' => 'Liên hệ',
            'search' => 'Tìm kiếm',
        ];
    }

    private static function routeNameFor(mixed $record, ?string $state): ?string
    {
        if (! $record instanceof MenuItem || self::linkTypeFor($record) !== 'route') {
            return $state;
        }

        return self::routeNameFromValue($record->url) ?: $state;
    }

    private static function customUrlFor(mixed $record, ?string $state): ?string
    {
        if (! $record instanceof MenuItem || self::linkTypeFor($record) !== 'custom') {
            return $state;
        }

        return $record->url ?: $state;
    }

    private static function routeNameFromValue(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value !== '' && Route::has($value)) {
            return $value;
        }

        $path = '/'.trim((string) parse_url($value, PHP_URL_PATH), '/');

        foreach (self::routeOptions() as $routeName => $label) {
            if (! Route::has($routeName)) {
                continue;
            }

            if ($path === '/'.trim((string) parse_url(route($routeName), PHP_URL_PATH), '/')) {
                return $routeName;
            }
        }

        return null;
    }

    private static function usesContentReference(string $type): bool
    {
        return in_array($type, ['service', 'landing_page', 'project', 'post'], true);
    }

    /** @return array<int, string> */
    private static function contentOptions(string $type): array
    {
        return match ($type) {
            'service' => Service::query()
                ->published()
                ->orderBy('title')
                ->pluck('title', 'id')
                ->all(),
            'landing_page' => LandingPage::query()
                ->published()
                ->orderBy('title')
                ->pluck('title', 'id')
                ->all(),
            'project' => Project::query()
                ->published()
                ->orderByDesc('published_at')
                ->pluck('title', 'id')
                ->all(),
            'post' => Post::query()
                ->published()
                ->orderByDesc('published_at')
                ->pluck('title', 'id')
                ->all(),
            default => [],
        };
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Menu')->searchable()->sortable(),
                TextColumn::make('location')->label('Vị trí')->badge()->searchable()->sortable(),
                TextColumn::make('items_count')->label('Menu item')->counts('items')->sortable(),
                IconColumn::make('is_active')->label('Kích hoạt')->boolean(),
                TextColumn::make('updated_at')->label('Cập nhật')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->defaultSort('name')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMenus::route('/'),
            'create' => CreateMenu::route('/create'),
            'edit' => EditMenu::route('/{record}/edit'),
        ];
    }
}
