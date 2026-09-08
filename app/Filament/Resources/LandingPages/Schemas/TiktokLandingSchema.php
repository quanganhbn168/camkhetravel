<?php

namespace App\Filament\Resources\LandingPages\Schemas;

use App\Support\Landing\LandingRegistry;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

final class TiktokLandingSchema
{
    public static function components(): array
    {
        $sections = [
            Section::make('TikTok · Hero')->schema([
                Hidden::make('landing_content.template_key'), Hidden::make('landing_content.page_class'),
                Hidden::make('landing_content.brand'), Hidden::make('landing_content.navigation'), Hidden::make('landing_content.seo'),
                ...self::copy('landing_content.hero.'), ...self::image('landing_content.hero.'),
            ]),
            Section::make('TikTok · Ưu đãi')->schema([
                Toggle::make('landing_content.promotion.enabled')->label('Hiển thị ưu đãi')->columnSpanFull(),
                ...self::copy('landing_content.promotion.'),
                self::repeater('landing_content.promotion.items', 'Các ưu đãi')->schema(self::copy()),
            ]),
            Section::make('TikTok · Dự án tiêu biểu')->schema([
                self::title('landing_content.projects.title'),
                self::repeater('landing_content.projects.items', 'Các kênh đã triển khai')->schema([
                    self::title('title'), TextInput::make('url')->label('Link kênh TikTok')->url()->required()->columnSpanFull(), Hidden::make('alt'), ...self::image(),
                ]),
            ]),
            Section::make('TikTok · Video đã triển khai')->schema([
                self::title('landing_content.showreel.title'), self::videos('landing_content.showreel.videos'),
            ]),
            Section::make('TikTok · Video mẫu theo ngành')->schema([
                self::title('landing_content.samples.title'),
                self::repeater('landing_content.samples.categories', 'Danh mục video')->itemLabel(fn (array $state) => $state['name'] ?? 'Danh mục')->schema([
                    TextInput::make('name')->label('Tên danh mục')->required()->columnSpanFull(),
                    TextInput::make('slug')->label('Mã danh mục')->required()->regex('/^[a-z0-9-]+$/')->columnSpanFull(),
                    self::repeater('tiers', 'Các gói video')->maxItems(3)->itemLabel(fn (array $state) => $state['label'] ?? 'Gói')->schema([
                        Select::make('key')->label('Loại gói')->options(['basic' => 'Cơ bản', 'standard' => 'Tiêu chuẩn', 'pro' => 'Chuyên nghiệp'])->required()->columnSpanFull(),
                        TextInput::make('label')->label('Tên hiển thị')->required()->columnSpanFull(), self::videos('videos'),
                    ]),
                ]),
            ]),
            Section::make('TikTok · Về THT Media')->schema([
                ...self::copy('landing_content.about.'), ...self::image('landing_content.about.'), self::title('landing_content.about.features_title'),
                self::repeater('landing_content.about.features', 'Các yếu tố cốt lõi')->schema([...self::copy(), ...self::image()]),
            ]),
            Section::make('TikTok · Phản hồi khách hàng')->schema([
                self::title('landing_content.testimonials.title'),
                self::repeater('landing_content.testimonials.items', 'Các phản hồi')->itemLabel(fn (array $state) => $state['name'] ?? 'Khách hàng')->schema([
                    TextInput::make('name')->label('Khách hàng')->required()->columnSpanFull(),
                    Textarea::make('quote')->label('Phản hồi')->rows(3)->required()->columnSpanFull(), ...self::image(),
                ]),
            ]),
            Section::make('TikTok · Bảng giá')->description('Giá điện thoại và máy quay là hai mức độc lập, đúng nội dung bảng giá.')->schema([
                self::title('landing_content.pricing.title'),
                self::repeater('landing_content.pricing.rows', 'Các gói dịch vụ')->itemLabel(fn (array $state) => $state['name'] ?? 'Gói dịch vụ')->schema([
                    TextInput::make('name')->label('Tên gói')->required()->columnSpanFull(),
                    Repeater::make('features')->label('Hạng mục công việc')->simple(TextInput::make('text')->required())->columnSpanFull(),
                    self::repeater('packages', 'Số lượng và giá')->schema([
                        TextInput::make('quantity')->label('Số video')->numeric()->minValue(1)->required()->columnSpanFull(),
                        TextInput::make('phone_price')->label('Tổng giá · Điện thoại')->required(),
                        TextInput::make('phone_unit')->label('Giá / video · Điện thoại')->required(),
                        TextInput::make('camera_price')->label('Tổng giá · Máy quay')->required(),
                        TextInput::make('camera_unit')->label('Giá / video · Máy quay')->required(),
                        Textarea::make('note')->label('Quản trị và bàn giao')->rows(2)->columnSpanFull(),
                    ])->columns(2),
                ]),
            ]),
            Section::make('TikTok · Liên hệ')->schema(self::copy('landing_content.contact_section.')),
        ];

        return array_map(fn (Section $section) => $section
            ->icon(Heroicon::OutlinedPlayCircle)->columns(1)->columnSpanFull()
            ->collapsible()->collapsed()
            ->visible(fn ($get) => $get('template_key') === LandingRegistry::TIKTOK), $sections);
    }

    private static function title(string $path): TextInput
    {
        return TextInput::make($path)->label('Tiêu đề')->required()->maxLength(255)->columnSpanFull();
    }

    private static function copy(string $prefix = ''): array
    {
        return [self::title($prefix.'title'), Textarea::make($prefix.'description')->label('Mô tả')->rows(3)->columnSpanFull()];
    }

    private static function image(string $prefix = ''): array
    {
        return [Hidden::make($prefix.'image'), CuratorPicker::make($prefix.'image_media_id')->label('Ảnh thay thế từ thư viện')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->helperText('Để trống để giữ ảnh đã chuyển từ WordPress.')->columnSpanFull()];
    }

    private static function repeater(string $path, string $label): Repeater
    {
        return Repeater::make($path)->label($label)->collapsible()->collapsed()->reorderable()->itemLabel(fn (array $state) => $state['title'] ?? $label)->columnSpanFull();
    }

    private static function videos(string $path): Repeater
    {
        return self::repeater($path, 'Video')->schema([
            self::title('title'), TextInput::make('url')->label('URL TikTok player')->url()->regex('#^https://www\.tiktok\.com/player/v1/[0-9]+$#')->required()->columnSpanFull(), ...self::image(),
        ]);
    }
}
