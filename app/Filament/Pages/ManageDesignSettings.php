<?php

namespace App\Filament\Pages;

use App\Settings\DesignSettings;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageDesignSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

    protected static ?string $navigationLabel = 'Giao diện & màu sắc';

    protected static ?string $title = 'Giao diện & màu sắc';

    protected static string|UnitEnum|null $navigationGroup = 'Cài đặt website';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.manage-design-settings';

    public ?array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function mount(DesignSettings $settings): void
    {
        $this->form->fill([
            'color_primary' => $settings->color_primary,
            'color_primary_hover' => $settings->color_primary_hover,
            'color_ink' => $settings->color_ink,
            'color_midnight' => $settings->color_midnight,
            'color_surface' => $settings->color_surface,
            'color_muted' => $settings->color_muted,
            'color_green_light' => $settings->color_green_light,
            'color_green_dark' => $settings->color_green_dark,
            'gradient_green_dark_start' => $settings->gradient_green_dark_start,
            'gradient_green_dark_end' => $settings->gradient_green_dark_end,
            'gradient_green_light_start' => $settings->gradient_green_light_start,
            'gradient_green_light_end' => $settings->gradient_green_light_end,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Giao diện & màu sắc')
                    ->tabs([
                        Tab::make('Màu nền tảng')
                            ->schema([
                                Section::make('Màu semantic của website')
                                    ->icon(Heroicon::OutlinedSwatch)
                                    ->schema([
                                        ColorPicker::make('color_primary')->label('color-primary')->required(),
                                        ColorPicker::make('color_primary_hover')->label('color-primary-hover')->required(),
                                        ColorPicker::make('color_ink')->label('color-ink')->required(),
                                        ColorPicker::make('color_midnight')->label('color-midnight')->required(),
                                        ColorPicker::make('color_surface')->label('color-surface')->required(),
                                        ColorPicker::make('color_muted')->label('color-muted')->required(),
                                    ])
                                    ->columns(3),
                            ]),
                        Tab::make('Màu thương hiệu')
                            ->schema([
                                Section::make('Màu thương hiệu xanh lá')
                                    ->icon(Heroicon::OutlinedSparkles)
                                    ->schema([
                                        ColorPicker::make('color_green_light')->label('color-green-light')->required(),
                                        ColorPicker::make('color_green_dark')->label('color-green-dark')->required(),
                                    ])
                                    ->columns(2),
                            ]),
                        Tab::make('Gradient')
                            ->schema([
                                Section::make('Gradient thương hiệu')
                                    ->schema([
                                        ColorPicker::make('gradient_green_dark_start')->label('gradient-green-dark-start')->required(),
                                        ColorPicker::make('gradient_green_dark_end')->label('gradient-green-dark-end')->required(),
                                        ColorPicker::make('gradient_green_light_start')->label('gradient-green-light-start')->required(),
                                        ColorPicker::make('gradient_green_light_end')->label('gradient-green-light-end')->required(),
                                    ])
                                    ->columns(2),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')->label('Lưu design tokens')->submit('save'),
        ];
    }

    public function save(DesignSettings $settings): void
    {
        foreach ($this->form->getState() as $key => $value) {
            $settings->{$key} = $value;
        }

        $settings->save();

        Notification::make()->title('Đã lưu design tokens')->success()->send();
    }
}
