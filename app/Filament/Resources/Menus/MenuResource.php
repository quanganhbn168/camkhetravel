<?php

namespace App\Filament\Resources\Menus;

use App\Filament\Resources\Menus\Pages\CreateMenu;
use App\Filament\Resources\Menus\Pages\EditMenu;
use App\Filament\Resources\Menus\Pages\ListMenus;
use App\Models\LandingPage;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\ServiceCategory;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
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
            ->columns(['default' => 1, 'xl' => 3])
            ->components([
                Section::make('Thêm vào menu')
                    ->icon(Heroicon::OutlinedPlusCircle)
                    ->description('Chọn nội dung có sẵn ở hệ thống hoặc thêm một liên kết riêng.')
                    ->schema([
                        ViewField::make('menu_source_picker')
                            ->label(null)
                            ->view('filament.resources.menus.menu-source-picker')
                            ->viewData(fn (): array => [
                                'sourceGroups' => self::sourceGroups(),
                            ])
                            ->dehydrated(false),
                    ])
                    ->columnSpan(1),
                Group::make([
                    Section::make('Thông tin menu')
                        ->icon(Heroicon::OutlinedBars3)
                        ->schema([
                            TextInput::make('name')
                                ->label('Tên menu')
                                ->required()
                                ->maxLength(255),
                            Select::make('location')
                                ->label('Vị trí hiển thị')
                                ->helperText('Chỉ chọn Header hoặc Footer.')
                                ->options([
                                    'header' => 'Header',
                                    'footer' => 'Footer',
                                ])
                                ->required()
                                ->native(false)
                                ->rule('in:header,footer'),
                            Toggle::make('is_active')
                                ->label('Kích hoạt menu')
                                ->default(true),
                        ])
                        ->columns(2),
                    Section::make('Cấu trúc menu')
                        ->icon(Heroicon::OutlinedListBullet)
                        ->description('Kéo thả hoặc dùng các mũi tên để sắp xếp và thay đổi cấp menu. Các mục đóng mặc định để dễ quản lý.')
                        ->schema([
                            Repeater::make('topLevelItems')
                                ->label('Danh sách menu item')
                                ->relationship()
                                ->defaultItems(0)
                                ->orderColumn('position')
                                ->schema([
                                    ...self::menuItemFields(),
                                    Repeater::make('children')
                                        ->label('Menu item con')
                                        ->relationship()
                                        ->defaultItems(0)
                                        ->orderColumn('position')
                                        ->schema(self::menuItemFields())
                                        ->columns(1)
                                        ->addActionLabel('Thêm menu item con')
                                        ->reorderable()
                                        ->reorderableWithButtons()
                                        ->reorderableWithDragAndDrop()
                                        ->collapsible()
                                        ->collapsed()
                                        ->extraItemActions([
                                            Action::make('moveOutside')
                                                ->label('Ra ngoài một cấp')
                                                ->icon(Heroicon::ArrowLeft)
                                                ->action(function (array $arguments, Repeater $component): void {
                                                    self::moveItemOutside($component, (string) $arguments['item']);
                                                }),
                                        ])
                                        ->itemLabel(fn (array $state): ?string => $state['label'] ?? 'Menu item mới')
                                        ->columnSpanFull(),
                                ])
                                ->columns(1)
                                ->addActionLabel('Thêm menu item')
                                ->reorderable()
                                ->reorderableWithButtons()
                                ->reorderableWithDragAndDrop()
                                ->collapsible()
                                ->collapsed()
                                ->cloneable()
                                ->extraItemActions([
                                    Action::make('moveInside')
                                        ->label('Vào trong làm con')
                                        ->icon(Heroicon::ArrowRight)
                                        ->action(function (array $arguments, Repeater $component): void {
                                            self::moveItemInside($component, (string) $arguments['item']);
                                        }),
                                ])
                                ->itemLabel(fn (array $state): ?string => $state['label'] ?? 'Menu item mới')
                                ->columnSpanFull(),
                        ])
                        ->collapsible(false),
                ])->columnSpan(2),
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
            Hidden::make('url'),
            Hidden::make('linked_source_id'),
            Select::make('target')
                ->label('Cách mở liên kết')
                ->options([
                    '_self' => 'Cùng tab',
                    '_blank' => 'Tab mới',
                ])
                ->default('_self')
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
            'native_service_category' => 'service_category',
            'native_landing_page' => 'landing_page',
            'native_project' => 'project',
            'native_project_category' => 'project_category',
            'native_post' => 'post',
            'native_post_category' => 'post_category',
            'custom' => 'custom',
            Service::class => 'service',
            ServiceCategory::class => 'service_category',
            LandingPage::class => 'landing_page',
            Project::class => 'project',
            ProjectCategory::class => 'project_category',
            Post::class => 'post',
            PostCategory::class => 'post_category',
            default => self::routeNameFromValue($record->url) ? 'route' : 'custom',
        };
    }

    private static function storedLinkType(string $type): string
    {
        return match ($type) {
            'route' => 'native_route',
            'service' => 'native_service',
            'service_category' => 'native_service_category',
            'landing_page' => 'native_landing_page',
            'project' => 'native_project',
            'project_category' => 'native_project_category',
            'post' => 'native_post',
            'post_category' => 'native_post_category',
            default => 'custom',
        };
    }

    /** @return array<string, string> */
    /** @return array<string, string> */
    public static function routeOptions(): array
    {
        return [
            'home' => 'Trang chủ',
            'about' => 'Giới thiệu',
            'services.index' => 'Tất cả dịch vụ',
            'pricing.index' => 'Bảng giá',
            'projects.index' => 'Tất cả dự án',
            'posts.index' => 'Blog',
            'bni.events.index' => 'Sự kiện',
            'bni.handover' => 'Lễ chuyển giao BNI',
            'bni.pickleball' => 'BNI Pickleball',
            'contact' => 'Liên hệ',
            'search' => 'Tìm kiếm',
        ];
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

    /** @return array<int, array{key: string, label: string, items: array<int, array{key: string, label: string, meta: string}>}> */
    public static function sourceGroups(): array
    {
        $groups = [
            [
                'key' => 'routes',
                'label' => 'Trang hệ thống',
                'items' => collect(self::routeOptions())->map(fn (string $label, string $route): array => [
                    'key' => "route:{$route}",
                    'label' => $label,
                    'meta' => $route,
                ])->values()->all(),
            ],
            [
                'key' => 'service-categories',
                'label' => 'Danh mục dịch vụ',
                'items' => ServiceCategory::query()
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get(['id', 'name'])
                    ->map(fn (ServiceCategory $category): array => [
                        'key' => "service_category:{$category->id}",
                        'label' => $category->name,
                        'meta' => 'Danh mục dịch vụ',
                    ])
                    ->all(),
            ],
            [
                'key' => 'services',
                'label' => 'Dịch vụ',
                'items' => Service::query()
                    ->published()
                    ->orderBy('title')
                    ->get(['id', 'title'])
                    ->map(fn (Service $service): array => [
                        'key' => "service:{$service->id}",
                        'label' => $service->title,
                        'meta' => 'Dịch vụ',
                    ])
                    ->all(),
            ],
            [
                'key' => 'landing-pages',
                'label' => 'Landing pages',
                'items' => LandingPage::query()->published()->orderBy('title')->get(['id', 'title'])->map(fn (LandingPage $landingPage): array => [
                    'key' => "landing_page:{$landingPage->id}",
                    'label' => $landingPage->title,
                    'meta' => 'Landing page',
                ])->all(),
            ],
            [
                'key' => 'project-categories',
                'label' => 'Danh mục dự án',
                'items' => ProjectCategory::query()
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get(['id', 'name'])
                    ->map(fn (ProjectCategory $category): array => [
                        'key' => "project_category:{$category->id}",
                        'label' => $category->name,
                        'meta' => 'Danh mục dự án',
                    ])
                    ->all(),
            ],
            [
                'key' => 'projects',
                'label' => 'Dự án',
                'items' => Project::query()
                    ->published()
                    ->orderBy('title')
                    ->get(['id', 'title'])
                    ->map(fn (Project $project): array => [
                        'key' => "project:{$project->id}",
                        'label' => $project->title,
                        'meta' => 'Dự án',
                    ])
                    ->all(),
            ],
            [
                'key' => 'post-categories',
                'label' => 'Chuyên mục blog',
                'items' => PostCategory::query()
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get(['id', 'name'])
                    ->map(fn (PostCategory $category): array => [
                        'key' => "post_category:{$category->id}",
                        'label' => $category->name,
                        'meta' => 'Chuyên mục blog',
                    ])
                    ->all(),
            ],
            [
                'key' => 'posts',
                'label' => 'Bài viết / blog',
                'items' => Post::query()
                    ->published()
                    ->orderByDesc('published_at')
                    ->get(['id', 'title'])
                    ->map(fn (Post $post): array => [
                        'key' => "post:{$post->id}",
                        'label' => $post->title,
                        'meta' => 'Bài viết',
                    ])
                    ->all(),
            ],
        ];

        return array_values(array_filter($groups, fn (array $group): bool => $group['items'] !== []));
    }

    private static function moveItemInside(Repeater $component, string $itemKey): void
    {
        $state = $component->getRawState();

        if (! is_array($state) || ! array_key_exists($itemKey, $state)) {
            return;
        }

        $keys = array_keys($state);
        $index = array_search($itemKey, array_map('strval', $keys), true);

        if ($index === false || $index === 0) {
            return;
        }

        $previousKey = $keys[$index - 1];
        $item = $state[$itemKey];
        unset($state[$itemKey]);

        $children = is_array($state[$previousKey]['children'] ?? null)
            ? $state[$previousKey]['children']
            : [];
        $children[$itemKey] = $item;
        $state[$previousKey]['children'] = $children;

        $component->rawState($state);
        $component->callAfterStateUpdated();
        $component->partiallyRender();
    }

    private static function moveItemOutside(Repeater $component, string $itemKey): void
    {
        $parentRepeater = $component->getParentRepeater();
        $parentItem = $component->getParentRepeaterItem();

        if (! $parentRepeater || ! $parentItem) {
            return;
        }

        $parentKey = (string) $parentItem->getStatePath(isAbsolute: false);
        $state = $parentRepeater->getRawState();
        $children = $state[$parentKey]['children'] ?? null;

        if (! is_array($children) || ! array_key_exists($itemKey, $children)) {
            return;
        }

        $item = $children[$itemKey];
        unset($children[$itemKey]);
        $state[$parentKey]['children'] = $children;

        $newState = [];

        foreach ($state as $key => $data) {
            $newState[$key] = $data;

            if ((string) $key === $parentKey) {
                $newState[$itemKey] = $item;
            }
        }

        $parentRepeater->rawState($newState);
        $parentRepeater->callAfterStateUpdated();
        $parentRepeater->partiallyRender();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Menu')->searchable()->sortable(),
                TextColumn::make('location')->label('Vị trí')->badge()->searchable()->sortable(),
                TextColumn::make('items_count')->label('Menu item')->counts('items')->sortable(),
                ToggleColumn::make('is_active')->label('Kích hoạt'),
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
