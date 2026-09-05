<?php

namespace App\Filament\Pages;

use App\Settings\WebsiteSettings;
use App\Support\Branding\FaviconService;
use App\Support\Maps\GoogleMapsShareResolver;
use App\Support\Maps\GoogleMapsUrl;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Models\Media;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
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

class ManageWebsiteSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Cài đặt chung';

    protected static ?string $title = 'Cài đặt chung';

    protected static string|UnitEnum|null $navigationGroup = 'Cài đặt website';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.manage-website-settings';

    public ?array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function mount(WebsiteSettings $settings): void
    {
        $this->form->fill([
            'site_name' => $settings->site_name,
            'tagline' => $settings->tagline,
            'company_name' => $settings->company_name,
            'contact_email' => $settings->contact_email,
            'hotline' => $settings->hotline,
            'contact_phone' => $settings->contact_phone,
            'address' => $settings->address,
            'phones' => $this->contactPhonesForForm($settings),
            'branches' => $this->contactBranchesForForm($settings),
            'facebook_url' => $settings->facebook_url,
            'zalo_url' => $settings->zalo_url,
            'youtube_url' => $settings->youtube_url,
            'seo_title' => $settings->seo_title,
            'seo_description' => $settings->seo_description,
            'seo_keywords' => $settings->seo_keywords,
            'logo_media_id' => $settings->logo_media_id,
            'favicon_media_id' => $settings->favicon_media_id,
            'seo_image_media_id' => $settings->seo_image_media_id,
            'google_maps_embed_url' => $settings->google_maps_embed_url,
            'google_maps_url' => $settings->google_maps_url,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Cài đặt chung')
                    ->tabs([
                        Tab::make('Thương hiệu')
                            ->schema([
                                Section::make('Nhận diện thương hiệu')
                                    ->icon(Heroicon::OutlinedBuildingOffice2)
                                    ->schema([
                                        TextInput::make('site_name')->label('Tên website')->required()->maxLength(255)->columnSpanFull(),
                                        TextInput::make('tagline')->label('Tagline')->maxLength(255)->columnSpanFull(),
                                        TextInput::make('company_name')->label('Tên doanh nghiệp')->required()->maxLength(255),
                                        CuratorPicker::make('logo_media_id')->label('Logo')->disk('public')->constrained()->acceptedFileTypes(['image/*']),
                                        CuratorPicker::make('favicon_media_id')
                                            ->label('Favicon nguồn')
                                            ->disk('public')
                                            ->constrained()
                                            ->acceptedFileTypes(['image/*'])
                                            ->helperText('Khi lưu, hệ thống chuyển đổi file upload và ghi đè trực tiếp bộ favicon cố định trong public.'),
                                    ])
                                    ->columns(2),
                            ]),
                        Tab::make('Liên hệ & mạng xã hội')
                            ->schema([
                                Section::make('Liên hệ và mạng xã hội')
                                        ->icon(Heroicon::OutlinedPhone)
                                        ->schema([
                                            TextInput::make('contact_email')->label('Email')->email(),
                                            Repeater::make('phones')
                                                ->label('Danh sách số điện thoại')
                                                ->schema([
                                                    TextInput::make('label')->label('Nhãn')->placeholder('Hotline / Kinh doanh')->maxLength(100),
                                                    TextInput::make('number')->label('Số điện thoại')->tel()->required()->placeholder('0982 123 456')->maxLength(30),
                                                    \Filament\Forms\Components\Toggle::make('is_primary')->label('Số chính')->default(false),
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
                                                    \Filament\Forms\Components\Toggle::make('is_active')->label('Hiển thị')->default(true),
                                                ])
                                                ->columns(2)
                                                ->addActionLabel('Thêm địa chỉ')
                                                ->reorderable()
                                                ->collapsible()
                                                ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'Địa điểm mới')
                                                ->columnSpanFull(),
                                        TextInput::make('facebook_url')->label('Facebook')->url(),
                                        TextInput::make('zalo_url')->label('Zalo')->url(),
                                        TextInput::make('youtube_url')->label('YouTube')->url(),
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
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ]),
                        Tab::make('SEO & chia sẻ')
                            ->schema([
                                Section::make('SEO mặc định')
                                    ->icon(Heroicon::OutlinedMagnifyingGlass)
                                    ->schema([
                                        TextInput::make('seo_title')->label('SEO title')->required()->maxLength(255)->columnSpanFull(),
                                        Textarea::make('seo_description')->label('Meta description')->rows(3)->required()->columnSpanFull(),
                                        TextInput::make('seo_keywords')->label('Từ khóa'),
                                        CuratorPicker::make('seo_image_media_id')->label('Ảnh Open Graph mặc định')->disk('public')->constrained()->acceptedFileTypes(['image/*']),
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
            Action::make('save')->label('Lưu cài đặt')->submit('save'),
        ];
    }

    public function save(WebsiteSettings $settings, FaviconService $favicons, GoogleMapsShareResolver $maps): void
    {
        foreach ($this->form->getState() as $key => $value) {
            $settings->{$key} = $value;
        }

        $settings->phones = $this->normalizeContactPhones($settings->phones ?? []);
        $settings->hotline = $settings->phones[0]['number'] ?? '';
        $settings->contact_phone = $settings->phones[1]['number'] ?? '';
        $settings->branches = $this->normalizeContactBranches($settings->branches ?? []);
        $settings->address = collect($settings->branches)->firstWhere('is_active', true)['address']
            ?? ($settings->branches[0]['address'] ?? '');

        $settings->google_maps_url = filled($settings->google_maps_url)
            ? trim((string) $settings->google_maps_url)
            : null;
        $settings->google_maps_embed_url = GoogleMapsUrl::normalizeEmbed($settings->google_maps_embed_url)
            ?? $maps->resolveEmbed($settings->google_maps_url);

        $favicons->sync(Media::query()->find($settings->favicon_media_id));
        $settings->save();

        Notification::make()->title('Đã lưu cài đặt website')->success()->send();

        $this->redirect(static::getUrl());
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
}
