<?php

namespace App\Filament\Pages;

use App\Models\Language;
use App\Models\Menu;
use App\Settings\AboutSettings;
use App\Settings\CompanySettings;
use App\Settings\DesignSettings;
use App\Settings\HomepageSettings;
use App\Settings\WebsiteSettings;
use App\Support\Localization\LanguageCatalog;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
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

class ManageSettings extends Page
{
    protected static ?string $slug = 'settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Cài đặt website';

    protected static ?string $title = 'Cài đặt website';

    protected static string|UnitEnum|null $navigationGroup = 'Cài đặt website';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    public function mount(
        WebsiteSettings $website,
        HomepageSettings $homepage,
        CompanySettings $company,
        AboutSettings $about,
        DesignSettings $design,
    ): void {
        $this->form->fill([
            'site_name' => $website->site_name,
            'tagline' => $website->tagline,
            'logo_media_id' => $website->logo_media_id,
            'favicon_media_id' => $website->favicon_media_id,
            'contact_email' => $website->contact_email,
            'hotline' => $website->hotline,
            'address' => $website->address,
            'google_maps_embed_url' => $website->google_maps_embed_url,
            'facebook_url' => $website->facebook_url,
            'zalo_url' => $website->zalo_url,
            'youtube_url' => $website->youtube_url,
            'seo_title' => $website->seo_title,
            'seo_description' => $website->seo_description,
            'seo_keywords' => $website->seo_keywords,
            'seo_image_media_id' => $website->seo_image_media_id,
            'header_menu_id' => $website->header_menu_id,
            'footer_menu_id' => $website->footer_menu_id,
            'about_eyebrow' => $homepage->about_eyebrow,
            'about_title' => $homepage->about_title,
            'about_content' => $homepage->about_content,
            'stats' => $homepage->stats,
            'commitments' => $homepage->commitments,
            'capabilities' => $homepage->capabilities,
            'faq_title' => $homepage->faq_title,
            'faq_description' => $homepage->faq_description,
            'faq_items' => $homepage->faq_items,
            'company_name' => $website->company_name,
            'company_profile_media_id' => $website->company_profile_media_id,
            'tax_code' => $company->tax_code,
            'representative' => $company->representative,
            'founded_year' => $company->founded_year,
            'business_license' => $company->business_license,
            'about_image_media_id' => $website->about_image_media_id,
            'page_label' => $about->page_label,
            'page_title' => $about->page_title,
            'page_intro' => $about->page_intro,
            'story' => $about->story,
            'history' => $about->history,
            'mission' => $about->mission,
            'vision' => $about->vision,
            'core_values' => $about->core_values,
            'color_primary' => $design->color_primary,
            'color_primary_hover' => $design->color_primary_hover,
            'color_ink' => $design->color_ink,
            'color_midnight' => $design->color_midnight,
            'color_surface' => $design->color_surface,
            'color_muted' => $design->color_muted,
            'color_green_light' => $design->color_green_light,
            'color_green_dark' => $design->color_green_dark,
            'gradient_green_dark_start' => $design->gradient_green_dark_start,
            'gradient_green_dark_end' => $design->gradient_green_dark_end,
            'gradient_green_light_start' => $design->gradient_green_light_start,
            'gradient_green_light_end' => $design->gradient_green_light_end,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('website-settings')
                    ->tabs([
                        Tab::make('Website')
                            ->icon(Heroicon::OutlinedGlobeAlt)
                            ->schema($this->websiteSchema()),
                        Tab::make('Trang chủ')
                            ->icon(Heroicon::OutlinedHome)
                            ->schema($this->homepageSchema()),
                        Tab::make('Doanh nghiệp')
                            ->icon(Heroicon::OutlinedBuildingOffice2)
                            ->schema($this->companySchema()),
                        Tab::make('Giới thiệu')
                            ->icon(Heroicon::OutlinedInformationCircle)
                            ->schema($this->aboutSchema()),
                        Tab::make('Giao diện')
                            ->icon(Heroicon::OutlinedSwatch)
                            ->schema($this->designSchema()),
                    ])
                    ->persistTabInQueryString('tab')
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(
        WebsiteSettings $website,
        HomepageSettings $homepage,
        CompanySettings $company,
        AboutSettings $about,
        DesignSettings $design,
    ): void {
        $data = $this->form->getState();

        $this->saveWebsite($website, $data);
        $this->saveHomepage($homepage, $data);
        $this->saveCompany($company, $data);
        $this->saveAbout($about, $data);
        $this->saveDesign($design, $data);

        Notification::make()
            ->title('Đã lưu cài đặt website')
            ->success()
            ->send();
    }

    /** @return array<int, Section> */
    private function websiteSchema(): array
    {
        return [
            Section::make('Nhận diện website')
                ->icon(Heroicon::OutlinedPhoto)
                ->description('Tên website, logo và favicon được dùng xuyên suốt frontend lẫn khu quản trị.')
                ->schema([
                    TextInput::make('site_name')->label('Tên website')->required()->maxLength(255),
                    TextInput::make('tagline')->label('Tagline')->maxLength(255),
                    CuratorPicker::make('logo_media_id')->label('Logo')->disk('public')->constrained()->acceptedFileTypes(['image/*']),
                    CuratorPicker::make('favicon_media_id')->label('Favicon')->disk('public')->constrained()->acceptedFileTypes(['image/*']),
                ])
                ->columns(2),
            Section::make('Liên hệ và mạng xã hội')
                ->icon(Heroicon::OutlinedPhone)
                ->schema([
                    TextInput::make('contact_email')->label('Email')->email()->maxLength(255),
                    TextInput::make('hotline')->label('Hotline')->tel()->maxLength(50),
                    Textarea::make('address')->label('Địa chỉ')->rows(3)->columnSpanFull(),
                    TextInput::make('google_maps_embed_url')
                        ->label('Google Maps embed URL')
                        ->helperText('Dán URL từ Google Maps > Chia sẻ > Nhúng bản đồ. Nếu để trống, frontend tạo bản đồ từ địa chỉ.')
                        ->url()
                        ->maxLength(2048)
                        ->columnSpanFull(),
                    TextInput::make('facebook_url')->label('Facebook')->url()->maxLength(2048),
                    TextInput::make('zalo_url')->label('Zalo')->url()->maxLength(2048),
                    TextInput::make('youtube_url')->label('YouTube')->url()->maxLength(2048),
                ])
                ->columns(2),
            Section::make('Menu hiển thị')
                ->icon(Heroicon::OutlinedBars3)
                ->schema([
                    Select::make('header_menu_id')
                        ->label('Menu header')
                        ->options(fn (): array => $this->menuOptions())
                        ->searchable()
                        ->preload()
                        ->placeholder('Chọn menu cho header'),
                    Select::make('footer_menu_id')
                        ->label('Menu footer')
                        ->options(fn (): array => $this->menuOptions())
                        ->searchable()
                        ->preload()
                        ->placeholder('Chọn menu cho footer'),
                ])
                ->columns(2),
            Section::make('SEO mặc định')
                ->icon(Heroicon::OutlinedMagnifyingGlass)
                ->description('Dùng khi một trang chưa có metadata riêng.')
                ->schema([
                    TextInput::make('seo_title')->label('SEO title')->required()->maxLength(255)->columnSpanFull(),
                    Textarea::make('seo_description')->label('Meta description')->rows(3)->required()->columnSpanFull(),
                    TextInput::make('seo_keywords')->label('Từ khóa')->maxLength(500),
                    CuratorPicker::make('seo_image_media_id')->label('Ảnh Open Graph mặc định')->disk('public')->constrained()->acceptedFileTypes(['image/*']),
                ])
                ->columns(2),
        ];
    }

    /** @return array<int, Section|Tabs> */
    private function homepageSchema(): array
    {
        return [
            Section::make('Ảnh phần giới thiệu')
                ->icon(Heroicon::OutlinedPhoto)
                ->schema([
                    CuratorPicker::make('about_image_media_id')
                        ->label('Ảnh giới thiệu trên trang chủ')
                        ->disk('public')
                        ->constrained()
                        ->acceptedFileTypes(['image/*'])
                        ->columnSpanFull(),
                    Tabs::make('homepage-intro-languages')
                        ->tabs(app(LanguageCatalog::class)->active()
                            ->map(fn (Language $language): Tab => $this->homepageIntroLanguageTab($language))
                            ->values()
                            ->all())
                        ->contained(false)
                        ->columnSpanFull(),
                ]),
            Section::make('Chỉ số nổi bật')
                ->icon(Heroicon::OutlinedChartBar)
                ->description('Tối đa 4 chỉ số. Nhập “24/7” để cả 24 và 7 cùng chạy hiệu ứng đếm.')
                ->schema([
                    Repeater::make('stats')
                        ->label('Danh sách chỉ số')
                        ->schema([
                            TextInput::make('value')
                                ->label('Giá trị')
                                ->helperText('Ví dụ: 24/7, 10+, 98%')
                                ->required()
                                ->maxLength(100),
                            TextInput::make('prefix')
                                ->label('Prefix')
                                ->maxLength(30),
                            TextInput::make('suffix')
                                ->label('Suffix')
                                ->maxLength(30),
                            TextInput::make('label')
                                ->label('Nhãn mô tả')
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull(),
                        ])
                        ->columns(3)
                        ->defaultItems(0)
                        ->minItems(0)
                        ->maxItems(4)
                        ->addActionLabel('Thêm chỉ số')
                        ->reorderable()
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => filled($state['value'] ?? null)
                            ? trim(($state['prefix'] ?? '').($state['value'] ?? '').($state['suffix'] ?? ''))
                            : 'Chỉ số mới')
                        ->columnSpanFull(),
                ]),
            Tabs::make('homepage-languages')
                ->tabs(app(LanguageCatalog::class)->active()
                    ->map(fn (Language $language): Tab => $this->homepageLanguageTab($language))
                    ->values()
                    ->all())
                ->contained(false)
                ->columnSpanFull(),
        ];
    }

    private function homepageLanguageTab(Language $language): Tab
    {
        $locale = $language->code;

        return Tab::make($language->name)
            ->schema([
                Section::make('Cam kết và năng lực')
                    ->icon(Heroicon::OutlinedSparkles)
                    ->description('Mỗi dòng là một ý hiển thị trên trang chủ.')
                    ->schema([
                        Textarea::make("commitments.{$locale}")->label('Cam kết')->rows(5),
                        Textarea::make("capabilities.{$locale}")->label('Năng lực')->rows(5),
                    ])
                    ->columns(2),
                Section::make('Câu hỏi thường gặp')
                    ->icon(Heroicon::OutlinedQuestionMarkCircle)
                    ->schema([
                        TextInput::make("faq_title.{$locale}")->label('Tiêu đề')->maxLength(255)->columnSpanFull(),
                        Textarea::make("faq_description.{$locale}")->label('Mô tả')->rows(2)->columnSpanFull(),
                        Repeater::make('faq_items')
                            ->label('Danh sách câu hỏi')
                            ->schema([
                                TextInput::make("question.{$locale}")->label('Câu hỏi')->maxLength(500)->columnSpanFull(),
                                Textarea::make("answer.{$locale}")->label('Trả lời')->rows(4)->columnSpanFull(),
                            ])
                            ->addActionLabel('Thêm câu hỏi')
                            ->reorderable()
                            ->cloneable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['question'][$locale] ?? 'Câu hỏi mới')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    private function homepageIntroLanguageTab(Language $language): Tab
    {
        $locale = $language->code;

        return Tab::make($language->name)
            ->schema([
                TextInput::make("about_eyebrow.{$locale}")
                    ->label('Tiêu đề')
                    ->maxLength(255)
                    ->columnSpanFull(),
                Textarea::make("about_title.{$locale}")
                    ->label('Mô tả')
                    ->rows(3)
                    ->columnSpanFull(),
                Textarea::make("about_content.{$locale}")
                    ->label('Nội dung')
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }

    /** @return array<int, Section> */
    private function companySchema(): array
    {
        return [
            Section::make('Thông tin doanh nghiệp')
                ->icon(Heroicon::OutlinedBuildingOffice2)
                ->description('Thông tin pháp lý và hồ sơ năng lực; tên doanh nghiệp cũng được dùng ngoài frontend.')
                ->schema([
                    TextInput::make('company_name')->label('Tên doanh nghiệp')->required()->maxLength(255)->columnSpanFull(),
                    TextInput::make('tax_code')->label('Mã số thuế')->maxLength(100),
                    TextInput::make('representative')->label('Người đại diện')->maxLength(255),
                    TextInput::make('founded_year')->label('Năm thành lập')->numeric()->minValue(1900)->maxValue((int) now()->format('Y')),
                    TextInput::make('business_license')->label('Số đăng ký kinh doanh')->maxLength(100),
                    CuratorPicker::make('company_profile_media_id')
                        ->label('Hồ sơ năng lực')
                        ->disk('public')
                        ->constrained()
                        ->helperText('Có thể chọn PDF hoặc tài liệu hồ sơ năng lực.')
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ];
    }

    /** @return array<int, Tabs> */
    private function aboutSchema(): array
    {
        return [
            Tabs::make('about-languages')
                ->tabs(app(LanguageCatalog::class)->active()
                    ->map(fn (Language $language): Tab => $this->aboutLanguageTab($language))
                    ->values()
                    ->all())
                ->contained(false)
                ->columnSpanFull(),
        ];
    }

    private function aboutLanguageTab(Language $language): Tab
    {
        $locale = $language->code;

        return Tab::make($language->name)
            ->schema([
                Section::make('Mở đầu trang giới thiệu')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->description('Để trống một trường nếu muốn giữ nội dung WordPress đã nhập trước đây ở đúng vị trí đó.')
                    ->schema([
                        TextInput::make("page_label.{$locale}")->label('Nhãn trang')->maxLength(255),
                        TextInput::make("page_title.{$locale}")->label('Tiêu đề trang')->maxLength(255)->columnSpanFull(),
                        Textarea::make("page_intro.{$locale}")->label('Mô tả mở đầu')->rows(3)->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Nội dung giới thiệu')
                    ->icon(Heroicon::OutlinedInformationCircle)
                    ->schema([
                        RichEditor::make("story.{$locale}")->label('Câu chuyện doanh nghiệp')->columnSpanFull(),
                        Textarea::make("history.{$locale}")->label('Lịch sử / cột mốc')->rows(5)->columnSpanFull(),
                        Textarea::make("mission.{$locale}")->label('Sứ mệnh')->rows(4),
                        Textarea::make("vision.{$locale}")->label('Tầm nhìn')->rows(4),
                        RichEditor::make("core_values.{$locale}")->label('Giá trị cốt lõi')->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    /** @return array<int, Section> */
    private function designSchema(): array
    {
        return [
            Section::make('Màu semantic')
                ->icon(Heroicon::OutlinedSwatch)
                ->schema([
                    ColorPicker::make('color_primary')->label('color-primary')->required(),
                    ColorPicker::make('color_primary_hover')->label('color-primary-hover')->required(),
                    ColorPicker::make('color_ink')->label('color-ink')->required(),
                    ColorPicker::make('color_midnight')->label('color-midnight')->required(),
                    ColorPicker::make('color_surface')->label('color-surface')->required(),
                    ColorPicker::make('color_muted')->label('color-muted')->required(),
                    ColorPicker::make('color_green_light')->label('color-green-light')->required(),
                    ColorPicker::make('color_green_dark')->label('color-green-dark')->required(),
                ])
                ->columns(4),
            Section::make('Gradient thương hiệu')
                ->icon(Heroicon::OutlinedSparkles)
                ->schema([
                    ColorPicker::make('gradient_green_dark_start')->label('gradient-green-dark-start')->required(),
                    ColorPicker::make('gradient_green_dark_end')->label('gradient-green-dark-end')->required(),
                    ColorPicker::make('gradient_green_light_start')->label('gradient-green-light-start')->required(),
                    ColorPicker::make('gradient_green_light_end')->label('gradient-green-light-end')->required(),
                ])
                ->columns(2),
        ];
    }

    /** @return array<int, Action> */
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Lưu cài đặt')
                ->submit('save')
                ->keyBindings(['mod+s']),
        ];
    }

    /** @param array<string, mixed> $data */
    private function saveWebsite(WebsiteSettings $website, array $data): void
    {
        foreach (['site_name', 'tagline', 'contact_email', 'hotline', 'address', 'facebook_url', 'zalo_url', 'youtube_url', 'seo_title', 'seo_description', 'seo_keywords'] as $key) {
            $website->{$key} = (string) ($data[$key] ?? '');
        }

        $website->google_maps_embed_url = filled($data['google_maps_embed_url'] ?? null)
            ? (string) $data['google_maps_embed_url']
            : null;

        foreach (['logo_media_id', 'favicon_media_id', 'seo_image_media_id', 'header_menu_id', 'footer_menu_id'] as $key) {
            $website->{$key} = filled($data[$key] ?? null) ? (int) $data[$key] : null;
        }

        $website->save();
    }

    /** @return array<int, string> */
    private function menuOptions(): array
    {
        return Menu::query()
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (Menu $menu): array => [
                $menu->getKey() => $menu->location
                    ? $menu->name.' · '.$menu->location
                    : $menu->name,
            ])
            ->all();
    }

    /** @param array<string, mixed> $data */
    private function saveHomepage(HomepageSettings $homepage, array $data): void
    {
        foreach (['about_eyebrow', 'about_title', 'about_content', 'stats', 'commitments', 'capabilities', 'faq_title', 'faq_description', 'faq_items'] as $key) {
            $homepage->{$key} = is_array($data[$key] ?? null) ? $data[$key] : [];
        }

        $homepage->save();

        $website = app(WebsiteSettings::class);
        $website->about_image_media_id = filled($data['about_image_media_id'] ?? null)
            ? (int) $data['about_image_media_id']
            : null;
        $website->save();
    }

    /** @param array<string, mixed> $data */
    private function saveCompany(CompanySettings $company, array $data): void
    {
        $company->tax_code = (string) ($data['tax_code'] ?? '');
        $company->representative = (string) ($data['representative'] ?? '');
        $company->founded_year = filled($data['founded_year'] ?? null) ? (int) $data['founded_year'] : null;
        $company->business_license = (string) ($data['business_license'] ?? '');
        $company->save();

        $website = app(WebsiteSettings::class);
        $website->company_name = (string) ($data['company_name'] ?? '');
        $website->company_profile_media_id = filled($data['company_profile_media_id'] ?? null)
            ? (int) $data['company_profile_media_id']
            : null;
        $website->save();
    }

    /** @param array<string, mixed> $data */
    private function saveAbout(AboutSettings $about, array $data): void
    {
        foreach (['page_label', 'page_title', 'page_intro', 'story', 'history', 'mission', 'vision', 'core_values'] as $key) {
            $about->{$key} = is_array($data[$key] ?? null) ? $data[$key] : [];
        }

        $about->save();
    }

    /** @param array<string, mixed> $data */
    private function saveDesign(DesignSettings $design, array $data): void
    {
        foreach (['color_primary', 'color_primary_hover', 'color_ink', 'color_midnight', 'color_surface', 'color_muted', 'color_green_light', 'color_green_dark', 'gradient_green_dark_start', 'gradient_green_dark_end', 'gradient_green_light_start', 'gradient_green_light_end'] as $key) {
            $design->{$key} = (string) ($data[$key] ?? '');
        }

        $design->save();
    }
}
