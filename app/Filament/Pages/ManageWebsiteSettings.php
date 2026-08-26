<?php

namespace App\Filament\Pages;

use App\Settings\WebsiteSettings;
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
            'address' => $settings->address,
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
                                        CuratorPicker::make('favicon_media_id')->label('Favicon')->disk('public')->constrained()->acceptedFileTypes(['image/*']),
                                    ])
                                    ->columns(2),
                            ]),
                        Tab::make('Liên hệ & mạng xã hội')
                            ->schema([
                                Section::make('Liên hệ và mạng xã hội')
                                    ->icon(Heroicon::OutlinedPhone)
                                    ->schema([
                                        TextInput::make('contact_email')->label('Email')->email(),
                                        TextInput::make('hotline')->label('Hotline'),
                                        Textarea::make('address')->label('Địa chỉ')->rows(3)->columnSpanFull(),
                                        TextInput::make('facebook_url')->label('Facebook')->url(),
                                        TextInput::make('zalo_url')->label('Zalo')->url(),
                                        TextInput::make('youtube_url')->label('YouTube')->url(),
                                        TextInput::make('google_maps_embed_url')
                                            ->label('Google Maps embed URL')
                                            ->helperText('Dán URL từ Google Maps > Chia sẻ > Nhúng bản đồ. Nếu để trống, website dùng địa chỉ ở trên để tạo bản đồ.')
                                            ->url()
                                            ->columnSpanFull(),
                                        TextInput::make('google_maps_url')
                                            ->label('Google Maps link')
                                            ->helperText('Link chia sẻ để khách mở vị trí trên Google Maps, ví dụ maps.app.goo.gl.')
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

    public function save(WebsiteSettings $settings): void
    {
        foreach ($this->form->getState() as $key => $value) {
            $settings->{$key} = $value;
        }

        $settings->save();

        Notification::make()->title('Đã lưu cài đặt website')->success()->send();

        $this->redirect(static::getUrl());
    }
}
