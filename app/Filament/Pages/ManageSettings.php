<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\PreservesUnchangedSettings;
use App\Filament\Forms\Components\GalleryPicker;
use App\Filament\Forms\TrackingSchema;
use App\Models\Language;
use App\Models\Menu;
use App\Settings\AboutSettings;
use App\Settings\CompanySettings;
use App\Settings\DesignSettings;
use App\Settings\HomepageSettings;
use App\Settings\WebsiteSettings;
use App\Support\Branding\FaviconService;
use App\Support\Localization\LanguageCatalog;
use App\Support\Maps\GoogleMapsShareResolver;
use App\Support\Maps\GoogleMapsUrl;
use App\Support\Tracking\TrackingScripts;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Models\Media;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageSettings extends Page
{
    use InteractsWithFormActions;
    use PreservesUnchangedSettings;

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
        $this->fillSettingsForm([
            ...app(TrackingScripts::class)->formData(),
            'site_name' => $website->site_name,
            'tagline' => $website->tagline,
            'logo_media_id' => $website->logo_media_id,
            'favicon_media_id' => $website->favicon_media_id,
            'contact_email' => $website->contact_email,
            'hotline' => $website->hotline,
            'contact_phone' => $website->contact_phone,
            'address' => $website->address,
            'phones' => $this->contactPhonesForForm($website),
            'branches' => $this->contactBranchesForForm($website),
            'google_maps_embed_url' => $website->google_maps_embed_url,
            'google_maps_url' => $website->google_maps_url,
            'facebook_url' => $website->facebook_url,
            'zalo_url' => $website->zalo_url,
            'youtube_url' => $website->youtube_url,
            'seo_title' => $website->seo_title,
            'seo_description' => $website->seo_description,
            'seo_keywords' => $website->seo_keywords,
            'seo_image_media_id' => $website->seo_image_media_id,
            'header_menu_id' => $website->header_menu_id,
            'footer_menu_id' => $website->footer_menu_id,
            'footer_background_media_id' => $website->footer_background_media_id,
            'banner_media_id' => $website->banner_media_id,
            'contact_image_media_id' => $website->contact_image_media_id,
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
            'page_title' => $about->page_title,
            'page_intro' => $about->page_intro,
            'story_title' => $about->story_title,
            'story' => $about->story,
            'page_stats' => $about->page_stats,
            'default_image_media_id' => $about->default_image_media_id,
            'story_image_media_id' => $about->story_image_media_id,
            'video_source' => $about->video_source,
            'video_youtube_url' => $about->video_youtube_url,
            'video_media_id' => $about->video_media_id,
            'video_poster_media_id' => $about->video_poster_media_id,
            'history' => $about->history,
            'history_title' => $about->history_title,
            'history_description' => $about->history_description,
            'history_timeline' => $about->history_timeline,
            'mission' => $about->mission,
            'vision' => $about->vision,
            'core_values' => $about->core_values,
            'core_values_image_media_id' => $about->core_values_image_media_id,
            'principles_title' => $about->principles_title,
            'services_title' => $about->services_title,
            'services_link_label' => $about->services_link_label,
            'stats_title' => $about->stats_title,
            'team_title' => $about->team_title,
            'team_description' => $about->team_description,
            'team_image_media_id' => $about->team_image_media_id,
            'office_title' => $about->office_title,
            'office_description' => $about->office_description,
            'office_gallery' => $about->office_gallery,
            'cta_title' => $about->cta_title,
            'cta_button_label' => $about->cta_button_label,
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
            'font_size_base' => $design->font_size_base,
            'font_size_body' => $design->font_size_body,
            'font_size_small' => $design->font_size_small,
            'font_size_h1' => $design->font_size_h1,
            'font_size_h2' => $design->font_size_h2,
            'font_size_h3' => $design->font_size_h3,
            'font_size_stat' => $design->font_size_stat,
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
                        Tab::make('Trang Giới thiệu')
                            ->icon(Heroicon::OutlinedInformationCircle)
                            ->schema($this->aboutSchema()),
                        Tab::make('Liên hệ')->icon(Heroicon::OutlinedPhone)->schema($this->contactSchema()),
                        Tab::make('SEO')->icon(Heroicon::OutlinedMagnifyingGlass)->schema($this->seoSchema()),
                        Tab::make('Tracking')
                            ->icon(Heroicon::OutlinedChartBar)
                            ->schema(TrackingSchema::make()),
                        Tab::make('Giao diện')
                            ->icon(Heroicon::OutlinedSwatch)
                            ->schema($this->designSchema()),
                    ])
                    ->persistTabInQueryString('tab')
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getFormContentComponent(),
            ]);
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('save')
            ->footer([
                Actions::make($this->getFormActions())
                    ->alignment($this->getFormActionsAlignment())
                    ->fullWidth($this->hasFullWidthFormActions())
                    ->key('form-actions'),
            ]);
    }

    public function save(
        WebsiteSettings $website,
        HomepageSettings $homepage,
        CompanySettings $company,
        AboutSettings $about,
        DesignSettings $design,
        FaviconService $favicons,
        GoogleMapsShareResolver $maps,
    ): void {
        $data = $this->settingsFormData();

        $this->saveWebsite($website, $data, $favicons, $maps);
        $this->saveHomepage($homepage, $data);
        $this->saveCompany($company, $data);
        $this->saveAbout($about, $data);
        $this->saveDesign($design, $data);
        app(TrackingScripts::class)->save($data);

        app()->call([$this, 'mount']);

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
                    CuratorPicker::make('favicon_media_id')
                        ->label('Favicon nguồn')
                        ->disk('public')
                        ->constrained()
                        ->acceptedFileTypes(['image/*'])
                        ->helperText('Khi lưu, hệ thống chuyển đổi file upload và ghi đè trực tiếp bộ favicon cố định trong public.'),
                ])
                ->columns(2),
            Section::make('Banner')
                ->schema([
                    CuratorPicker::make('banner_media_id')->label('Banner chung')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->helperText('Dùng cho các banner chưa có ảnh riêng.'),
                    CuratorPicker::make('contact_image_media_id')->label('Banner trang liên hệ')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->helperText('Để trống sẽ dùng banner chung. Ảnh hiển thị theo tỷ lệ gốc.'),
                ])->columns(2),
            Section::make('Nền footer')
                ->schema([
                    CuratorPicker::make('footer_background_media_id')
                        ->label('Ảnh nền footer')
                        ->disk('public')
                        ->constrained()
                        ->acceptedFileTypes(['image/*'])
                        ->helperText('Tải lên hoặc chọn ảnh nền. Ảnh có lớp phủ tối để dễ đọc chữ; để trống dùng nền hiện tại.'),
                ]),
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
        ];
    }

    private function seoSchema(): array
    {
        return [
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
    private function contactSchema(): array
    {
        return [
            Section::make('Liên hệ và mạng xã hội')
                ->icon(Heroicon::OutlinedPhone)
                ->schema([
                    TextInput::make('contact_email')->label('Email')->email()->maxLength(255),
                    Repeater::make('phones')
                        ->label('Danh sách số điện thoại')
                        ->schema([
                            TextInput::make('label')->label('Nhãn')->placeholder('Hotline / Kinh doanh')->maxLength(100),
                            TextInput::make('number')->label('Số điện thoại')->tel()->required()->placeholder('0982 123 456')->maxLength(30),
                            Toggle::make('is_primary')->label('Số chính')->default(false),
                        ])
                        ->columns(3)
                        ->addActionLabel('Thêm số điện thoại')
                        ->reorderable()
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['number'] ?? 'Số điện thoại mới')
                        ->columnSpanFull(),
                    Repeater::make('branches')
                        ->label('Danh sách địa chỉ / chi nhánh')
                        ->schema([
                            TextInput::make('name')->label('Tên địa điểm')->required()->placeholder('Trụ sở chính')->maxLength(150),
                            Textarea::make('address')->label('Địa chỉ')->required()->rows(2)->maxLength(500),
                            Toggle::make('is_active')->label('Hiển thị')->default(true),
                        ])
                        ->columns(2)
                        ->addActionLabel('Thêm địa chỉ')
                        ->reorderable()
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'Địa điểm mới')
                        ->columnSpanFull(),
                    Textarea::make('google_maps_embed_url')
                        ->label('Google Maps embed URL')
                        ->helperText('Dán URL embed hoặc nguyên thẻ <iframe>. Nếu chỉ có link share bên dưới, hệ thống sẽ lấy tọa độ từ link khi lưu và tạo embed cố định.')
                        ->rules([GoogleMapsUrl::embedValidationRule()])
                        ->maxLength(10000)
                        ->rows(5)
                        ->columnSpanFull(),
                    TextInput::make('google_maps_url')
                        ->label('Google Maps link')
                        ->helperText('Link chia sẻ để khách mở vị trí trên Google Maps, ví dụ https://maps.app.goo.gl/M1iQjB52X9NqzBYd7.')
                        ->url()
                        ->maxLength(2048)
                        ->columnSpanFull(),
                    TextInput::make('facebook_url')->label('Facebook')->url()->maxLength(2048),
                    TextInput::make('zalo_url')->label('Zalo')->url()->maxLength(2048),
                    TextInput::make('youtube_url')->label('YouTube')->url()->maxLength(2048),
                ])
                ->columns(2),
        ];
    }

    private function homepageSchema(): array
    {
        return [
            Section::make('Giới thiệu trên trang chủ')->description('Chỉ áp dụng cho trang chủ. Trang Giới thiệu có nội dung và hình ảnh riêng ở tab Trang Giới thiệu.')
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
                    ->label('Tiêu đề phần giới thiệu')
                    ->maxLength(255)
                    ->columnSpanFull(),
                Textarea::make("about_title.{$locale}")
                    ->label('Mô tả ngắn')
                    ->rows(3)
                    ->columnSpanFull(),
                Textarea::make("about_content.{$locale}")
                    ->label('Nội dung chi tiết')
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

    /** @return array<int, Section|Tabs> */
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
            Section::make('Chỉ số trang Giới thiệu')->description('Độc lập với chỉ số trên trang chủ.')->schema([
                Repeater::make('page_stats')->label('Chỉ số')->schema([
                    TextInput::make('value')->label('Giá trị')->required(),
                    TextInput::make('prefix')->label('Tiền tố'),
                    TextInput::make('suffix')->label('Hậu tố'),
                    TextInput::make('label')->label('Nhãn')->required(),
                ])->columns(2)->maxItems(4)->reorderable()->collapsible()->defaultItems(0),
            ]),
            Section::make('Hình ảnh riêng từng khu vực')
                ->icon(Heroicon::OutlinedPhoto)
                ->description('Mỗi khu vực dùng media riêng. Chỉ khi để trống, frontend mới dùng ảnh giới thiệu chung làm fallback.')
                ->schema([
                    CuratorPicker::make('default_image_media_id')->label('Ảnh mặc định trang Giới thiệu')->helperText('Độc lập với ảnh giới thiệu trên trang chủ.'),
                    CuratorPicker::make('story_image_media_id')
                        ->label('Ảnh Câu chuyện THT Media')
                        ->disk('public')
                        ->constrained()
                        ->acceptedFileTypes(['image/*'])
                        ->columnSpanFull(),
                    CuratorPicker::make('video_poster_media_id')
                        ->label('Ảnh bìa video giới thiệu')
                        ->disk('public')
                        ->constrained()
                        ->acceptedFileTypes(['image/*'])
                        ->columnSpanFull(),
                    CuratorPicker::make('core_values_image_media_id')
                        ->label('Ảnh Giá trị cốt lõi')
                        ->disk('public')
                        ->constrained()
                        ->acceptedFileTypes(['image/*'])
                        ->columnSpanFull(),
                    CuratorPicker::make('team_image_media_id')
                        ->label('Ảnh Đội ngũ nhân sự')
                        ->disk('public')
                        ->constrained()
                        ->acceptedFileTypes(['image/*'])
                        ->columnSpanFull(),
                    GalleryPicker::make('office_gallery')
                        ->label('Gallery Văn phòng THT Media')
                        ->multiple()
                        ->disk('public')
                        ->constrained()
                        ->acceptedFileTypes(['image/*'])
                        ->helperText('Chọn nhiều ảnh và kéo thả để đổi thứ tự. Nút “Xóa tất cả” luôn yêu cầu xác nhận.')
                        ->columnSpanFull(),
                ])
                ->columns(1)
                ->columnSpanFull(),
            Section::make('Video giới thiệu')
                ->icon(Heroicon::OutlinedVideoCamera)
                ->description('Chọn một nguồn hiển thị sau phần Câu chuyện của chúng tôi. Video tải lên được quản lý trong thư viện Curator.')
                ->schema([
                    Select::make('video_source')
                        ->label('Nguồn video')
                        ->options([
                            'youtube' => 'YouTube',
                            'upload' => 'Video tải lên',
                        ])
                        ->placeholder('Không hiển thị video')
                        ->live()
                        ->columnSpanFull(),
                    TextInput::make('video_youtube_url')
                        ->label('URL video YouTube')
                        ->url()
                        ->maxLength(2048)
                        ->visible(fn ($get): bool => $get('video_source') === 'youtube')
                        ->required(fn ($get): bool => $get('video_source') === 'youtube')
                        ->columnSpanFull(),
                    CuratorPicker::make('video_media_id')
                        ->label('Video tải lên')
                        ->disk('public')
                        ->constrained()
                        ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/quicktime'])
                        ->visible(fn ($get): bool => $get('video_source') === 'upload')
                        ->required(fn ($get): bool => $get('video_source') === 'upload')
                        ->columnSpanFull(),
                ])
                ->columns(1)
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
                    ->description('Nhập nội dung quản trị trực tiếp cho trang giới thiệu.')
                    ->schema([
                        TextInput::make("page_title.{$locale}")->label('Tiêu đề trang')->maxLength(255)->columnSpanFull(),
                        Textarea::make("page_intro.{$locale}")->label('Mô tả mở đầu')->rows(3)->columnSpanFull(),
                    ])
                    ->columns(1),
                Section::make('Câu chuyện doanh nghiệp')
                    ->icon(Heroicon::OutlinedInformationCircle)
                    ->schema([
                        TextInput::make("story_title.{$locale}")
                            ->label('Tiêu đề khối câu chuyện')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        RichEditor::make("story.{$locale}")
                            ->label('Nội dung câu chuyện')
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
                Section::make('Sứ mệnh, tầm nhìn và giá trị')
                    ->icon(Heroicon::OutlinedSparkles)
                    ->schema([
                        TextInput::make("principles_title.{$locale}")
                            ->label('Tiêu đề khối')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make("mission.{$locale}")->label('Sứ mệnh')->rows(4),
                        Textarea::make("vision.{$locale}")->label('Tầm nhìn')->rows(4),
                        RichEditor::make("core_values.{$locale}")->label('Giá trị cốt lõi')->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Lịch sử hình thành')
                    ->icon(Heroicon::OutlinedClock)
                    ->description('Có thể dùng nội dung lịch sử đơn hoặc danh sách mốc. Mỗi mốc gồm năm, ảnh nguồn từ Curator, tiêu đề và mô tả.')
                    ->schema([
                        TextInput::make("history_title.{$locale}")
                            ->label('Tiêu đề khối')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make("history_description.{$locale}")
                            ->label('Mô tả khối')
                            ->rows(3)
                            ->columnSpanFull(),
                        Textarea::make("history.{$locale}")
                            ->label('Nội dung lịch sử đơn')
                            ->helperText('Chỉ dùng khi không có danh sách mốc bên dưới.')
                            ->rows(5)
                            ->columnSpanFull(),
                        Repeater::make('history_timeline')
                            ->label('Các mốc lịch sử')
                            ->schema([
                                TextInput::make('year')
                                    ->label('Năm')
                                    ->required()
                                    ->maxLength(30),
                                CuratorPicker::make('media_id')
                                    ->label('Hình ảnh')
                                    ->disk('public')
                                    ->constrained()
                                    ->acceptedFileTypes(['image/*']),
                                TextInput::make("title.{$locale}")
                                    ->label('Tiêu đề')
                                    ->required($language->is_default)
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Textarea::make("description.{$locale}")
                                    ->label('Mô tả')
                                    ->required($language->is_default)
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Thêm mốc lịch sử')
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): string => trim(($state['year'] ?? 'Mốc mới').' — '.($state['title'][$locale] ?? '')))
                            ->columnSpanFull(),
                    ]),
                Section::make('Dịch vụ và số liệu')
                    ->icon(Heroicon::OutlinedChartBar)
                    ->description('Các dịch vụ lấy từ danh sách dịch vụ đã xuất bản; tại đây chỉ nhập tiêu đề của khối và tiêu đề liên kết.')
                    ->schema([
                        TextInput::make("services_title.{$locale}")
                            ->label('Tiêu đề khối dịch vụ')
                            ->maxLength(255),
                        TextInput::make("services_link_label.{$locale}")
                            ->label('Nhãn liên kết xem dịch vụ')
                            ->maxLength(255),
                        TextInput::make("stats_title.{$locale}")
                            ->label('Tiêu đề khối số liệu')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Đội ngũ nhân sự')
                    ->icon(Heroicon::OutlinedUserGroup)
                    ->schema([
                        TextInput::make("team_title.{$locale}")
                            ->label('Tiêu đề khối')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make("team_description.{$locale}")
                            ->label('Mô tả')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
                Section::make('Văn phòng THT Media')
                    ->icon(Heroicon::OutlinedBuildingOffice2)
                    ->schema([
                        TextInput::make("office_title.{$locale}")
                            ->label('Tiêu đề khối')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make("office_description.{$locale}")
                            ->label('Mô tả')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
                Section::make('Kêu gọi liên hệ')
                    ->icon(Heroicon::OutlinedPhone)
                    ->description('Nếu để trống, khối kêu gọi liên hệ sẽ không hiển thị trên trang giới thiệu.')
                    ->schema([
                        TextInput::make("cta_title.{$locale}")
                            ->label('Tiêu đề kêu gọi liên hệ')
                            ->maxLength(255),
                        TextInput::make("cta_button_label.{$locale}")
                            ->label('Nhãn nút liên hệ')
                            ->maxLength(255),
                    ])
                    ->columns(2),
            ]);
    }

    /** @return array<int, Section> */
    private function designSchema(): array
    {
        return [
            Section::make('Bảng màu frontend')
                ->icon(Heroicon::OutlinedSwatch)
                ->description('Cam là màu chủ đạo; xanh THT dùng cho điều hướng và điểm nhấn riêng. Màu chữ, nền sáng và nền phụ giữ trung tính để không nhuộm xanh toàn trang.')
                ->schema([
                    ColorPicker::make('color_primary')->label('Cam chủ đạo')->required(),
                    ColorPicker::make('color_primary_hover')->label('Cam khi hover')->required(),
                    ColorPicker::make('color_ink')->label('Chữ / nền đậm trung tính')->required(),
                    ColorPicker::make('color_midnight')->label('Xanh thanh điều hướng')->required(),
                    ColorPicker::make('color_surface')->label('Nền sáng trung tính')->required(),
                    ColorPicker::make('color_muted')->label('Nền phụ trung tính')->required(),
                    ColorPicker::make('color_green_light')->label('Xanh THT sáng')->required(),
                    ColorPicker::make('color_green_dark')->label('Xanh THT đậm')->required(),
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
            Section::make('Typography website')
                ->icon(Heroicon::OutlinedAdjustmentsHorizontal)
                ->description('Các cỡ chữ semantic dùng xuyên suốt frontend. Có thể nhập đơn vị rem hoặc biểu thức clamp().')
                ->schema([
                    TextInput::make('font_size_base')->label('Cỡ chữ gốc')->required()->maxLength(100),
                    TextInput::make('font_size_body')->label('Nội dung')->required()->maxLength(100),
                    TextInput::make('font_size_small')->label('Nội dung nhỏ')->required()->maxLength(100),
                    TextInput::make('font_size_h1')->label('Tiêu đề H1')->required()->maxLength(100),
                    TextInput::make('font_size_h2')->label('Tiêu đề H2')->required()->maxLength(100),
                    TextInput::make('font_size_h3')->label('Tiêu đề H3')->required()->maxLength(100),
                    TextInput::make('font_size_stat')->label('Số liệu nổi bật')->required()->maxLength(100),
                ])
                ->columns(2),
        ];
    }

    /** @return array<int, Action> */
    protected function getFormActions(): array
    {
        return [
            Action::make('discard')->label('Hủy thay đổi')->color('gray')->action(fn () => app()->call([$this, 'mount'])),
            Action::make('save')
                ->label('Lưu cài đặt')
                ->submit('save')
                ->keyBindings(['mod+s']),
        ];
    }

    /** @param array<string, mixed> $data */
    private function saveWebsite(WebsiteSettings $website, array $data, FaviconService $favicons, GoogleMapsShareResolver $maps): void
    {
        foreach (['site_name', 'tagline', 'contact_email', 'facebook_url', 'zalo_url', 'youtube_url', 'seo_title', 'seo_description', 'seo_keywords'] as $key) {
            $website->{$key} = (string) ($data[$key] ?? '');
        }

        $website->phones = $this->normalizeContactPhones($data['phones'] ?? []);
        $website->hotline = $website->phones[0]['number'] ?? '';
        $website->contact_phone = $website->phones[1]['number'] ?? '';
        $website->branches = $this->normalizeContactBranches($data['branches'] ?? []);
        $website->address = collect($website->branches)->firstWhere('is_active', true)['address']
            ?? ($website->branches[0]['address'] ?? '');

        $website->google_maps_url = filled($data['google_maps_url'] ?? null)
            ? trim((string) $data['google_maps_url'])
            : null;
        $website->google_maps_embed_url = GoogleMapsUrl::normalizeEmbed($data['google_maps_embed_url'] ?? null)
            ?? $maps->resolveEmbed($website->google_maps_url);

        foreach (['logo_media_id', 'favicon_media_id', 'seo_image_media_id', 'header_menu_id', 'footer_menu_id', 'footer_background_media_id', 'banner_media_id', 'contact_image_media_id'] as $key) {
            $website->{$key} = filled($data[$key] ?? null) ? (int) $data[$key] : null;
        }

        $favicons->sync(Media::query()->find($website->favicon_media_id));
        $website->save();
    }

    private function contactPhonesForForm(WebsiteSettings $website): array
    {
        $phones = is_array($website->phones ?? null) ? $website->phones : [];

        if ($phones !== []) {
            return $this->normalizeContactPhones($phones);
        }

        return array_values(array_filter([
            filled($website->hotline) ? ['label' => 'Hotline chính', 'number' => $website->hotline, 'is_primary' => true] : null,
            filled($website->contact_phone) ? ['label' => 'Số điện thoại phụ', 'number' => $website->contact_phone, 'is_primary' => false] : null,
        ]));
    }

    private function contactBranchesForForm(WebsiteSettings $website): array
    {
        $branches = is_array($website->branches ?? null) ? $website->branches : [];

        if ($branches !== []) {
            return $this->normalizeContactBranches($branches);
        }

        return filled($website->address)
            ? [['name' => 'Trụ sở chính', 'address' => $website->address, 'is_active' => true]]
            : [];
    }

    private function normalizeContactPhones(mixed $phones): array
    {
        $normalized = array_values(array_filter(array_map(
            static function (mixed $phone): ?array {
                if (! is_array($phone)) {
                    return null;
                }

                $number = trim((string) ($phone['number'] ?? ''));

                return $number === '' ? null : [
                    'label' => trim((string) ($phone['label'] ?? '')),
                    'number' => $number,
                    'is_primary' => (bool) ($phone['is_primary'] ?? false),
                ];
            },
            is_array($phones) ? $phones : [],
        )));

        if ($normalized !== []) {
            $primaryIndex = collect($normalized)->search(fn (array $phone): bool => $phone['is_primary']);
            $primaryIndex = $primaryIndex === false ? 0 : $primaryIndex;

            foreach ($normalized as $index => &$phone) {
                $phone['is_primary'] = $index === $primaryIndex;
            }
            unset($phone);
        }

        return $normalized;
    }

    private function normalizeContactBranches(mixed $branches): array
    {
        return array_values(array_filter(array_map(
            static function (mixed $branch): ?array {
                if (! is_array($branch)) {
                    return null;
                }

                $address = trim((string) ($branch['address'] ?? ''));

                return $address === '' ? null : [
                    'name' => trim((string) ($branch['name'] ?? 'Địa điểm')),
                    'address' => $address,
                    'is_active' => (bool) ($branch['is_active'] ?? true),
                ];
            },
            is_array($branches) ? $branches : [],
        )));
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
        foreach (['page_stats', 'page_title', 'page_intro', 'story_title', 'story', 'history', 'history_title', 'history_description', 'history_timeline', 'mission', 'vision', 'core_values', 'principles_title', 'services_title', 'services_link_label', 'stats_title', 'team_title', 'team_description', 'office_title', 'office_description', 'cta_title', 'cta_button_label'] as $key) {
            $about->{$key} = is_array($data[$key] ?? null) ? $data[$key] : [];
        }

        $about->video_source = in_array($data['video_source'] ?? null, ['youtube', 'upload'], true)
            ? $data['video_source']
            : '';
        $about->video_youtube_url = trim((string) ($data['video_youtube_url'] ?? ''));
        $about->video_media_id = filled($data['video_media_id'] ?? null)
            ? (int) $data['video_media_id']
            : null;

        foreach (['default_image_media_id', 'story_image_media_id', 'video_poster_media_id', 'core_values_image_media_id', 'team_image_media_id'] as $key) {
            $about->{$key} = filled($data[$key] ?? null) ? (int) $data[$key] : null;
        }

        $about->office_gallery = collect($data['office_gallery'] ?? [])
            ->filter(fn (mixed $mediaId): bool => is_numeric($mediaId))
            ->map(fn (mixed $mediaId): int => (int) $mediaId)
            ->unique()
            ->values()
            ->all();
        $about->office_image_media_id = $about->office_gallery[0] ?? null;

        $about->save();
    }

    /** @param array<string, mixed> $data */
    private function saveDesign(DesignSettings $design, array $data): void
    {
        foreach (['color_primary', 'color_primary_hover', 'color_ink', 'color_midnight', 'color_surface', 'color_muted', 'color_green_light', 'color_green_dark', 'gradient_green_dark_start', 'gradient_green_dark_end', 'gradient_green_light_start', 'gradient_green_light_end', 'font_size_base', 'font_size_body', 'font_size_small', 'font_size_h1', 'font_size_h2', 'font_size_h3', 'font_size_stat'] as $key) {
            $design->{$key} = (string) ($data[$key] ?? '');
        }

        $design->save();
    }
}
