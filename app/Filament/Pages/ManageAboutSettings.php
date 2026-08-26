<?php

namespace App\Filament\Pages;

use App\Models\Language;
use App\Settings\HomepageSettings;
use App\Settings\WebsiteSettings;
use App\Support\Localization\LanguageCatalog;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageAboutSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?string $navigationLabel = 'Cài đặt giới thiệu';

    protected static ?string $title = 'Cài đặt giới thiệu';

    protected static string|UnitEnum|null $navigationGroup = 'Cài đặt website';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.manage-about-settings';

    public ?array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function mount(HomepageSettings $homepage, WebsiteSettings $website): void
    {
        $this->form->fill([
            'about_eyebrow' => $homepage->about_eyebrow,
            'about_title' => $homepage->about_title,
            'about_content' => $homepage->about_content,
            'commitments' => $homepage->commitments,
            'capabilities' => $homepage->capabilities,
            'about_image_media_id' => $website->about_image_media_id,
            'company_profile_media_id' => $website->company_profile_media_id,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Cài đặt giới thiệu')
                    ->tabs([
                        Tab::make('Nội dung')
                            ->schema([
                                Tabs::make('Nội dung theo ngôn ngữ')
                                    ->tabs(app(LanguageCatalog::class)->active()
                                        ->map(fn (Language $language): Tab => $this->languageTab($language))
                                        ->values()
                                        ->all()),
                            ]),
                        Tab::make('Hình ảnh & hồ sơ')
                            ->schema([
                                Section::make('Hình ảnh và hồ sơ năng lực')
                                    ->icon(Heroicon::OutlinedPhoto)
                                    ->schema([
                                        CuratorPicker::make('about_image_media_id')
                                            ->label('Ảnh giới thiệu')
                                            ->disk('public')
                                            ->constrained()
                                            ->acceptedFileTypes(['image/*']),
                                        CuratorPicker::make('company_profile_media_id')
                                            ->label('Tệp hồ sơ năng lực')
                                            ->disk('public')
                                            ->constrained(),
                                    ])
                                    ->columns(2),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    protected function languageTab(Language $language): Tab
    {
        $locale = $language->code;

        return Tab::make($language->name)
            ->schema([
                Section::make('Nội dung giới thiệu')
                    ->schema([
                        TextInput::make("about_eyebrow.{$locale}")->label('Nhãn nhỏ')->maxLength(255),
                        TextInput::make("about_title.{$locale}")->label('Tiêu đề')->required($language->is_default)->maxLength(255)->columnSpanFull(),
                        Textarea::make("about_content.{$locale}")->label('Nội dung')->rows(4)->columnSpanFull(),
                    ]),
                Section::make('Cam kết và năng lực')
                    ->description('Mỗi dòng là một ý hiển thị trong phần giới thiệu.')
                    ->schema([
                        Textarea::make("commitments.{$locale}")->label('Cam kết')->rows(5),
                        Textarea::make("capabilities.{$locale}")->label('Năng lực')->rows(5),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')->label('Lưu cài đặt')->submit('save'),
        ];
    }

    public function save(HomepageSettings $homepage, WebsiteSettings $website): void
    {
        $data = $this->form->getState();

        foreach (['about_eyebrow', 'about_title', 'about_content', 'commitments', 'capabilities'] as $key) {
            $homepage->{$key} = $data[$key];
        }

        foreach (['about_image_media_id', 'company_profile_media_id'] as $key) {
            $website->{$key} = $data[$key];
        }

        $homepage->save();
        $website->save();

        Notification::make()->title('Đã lưu cài đặt giới thiệu')->success()->send();
    }
}
