<?php

namespace App\Filament\Resources\ContentItems\Schemas;

use App\Models\MediaAsset;
use App\Support\Seo\ContentSeoFallbacks;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class ContentItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->components([
                        Section::make('Nội dung chính')
                            ->description('Giữ nguyên slug và canonical path nếu nội dung đã được Google lập chỉ mục.')
                            ->icon(Heroicon::OutlinedDocumentText)
                            ->components([
                                TextInput::make('title')
                                    ->label('Tiêu đề')
                                    ->required()
                                    ->maxLength(500)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (?string $state, $get, $set): void {
                                        if (blank($get('slug')) && ($slug = ContentSeoFallbacks::slug($state))) {
                                            $set('slug', $slug);
                                        }

                                        if (blank($get('seo_title')) && ($seoTitle = ContentSeoFallbacks::title($state))) {
                                            $set('seo_title', $seoTitle);
                                        }
                                    })
                                    ->columnSpanFull(),
                                Grid::make(2)
                                    ->components([
                                        TextInput::make('slug')
                                            ->label('Đường dẫn (slug)')
                                            ->required()
                                            ->maxLength(255)
                                            ->helperText('Tự tạo từ tiêu đề khi để trống; anh vẫn có thể sửa khi cần.'),
                                        TextInput::make('canonical_path')
                                            ->label('Đường dẫn canonical')
                                            ->helperText('Dạng /duong-dan/ và có dấu / ở cuối.'),
                                    ]),
                                Textarea::make('excerpt')
                                    ->label('Mô tả ngắn')
                                    ->rows(4)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (?string $state, $get, $set): void {
                                        if (blank($get('seo_description')) && ($description = ContentSeoFallbacks::description($state))) {
                                            $set('seo_description', $description);
                                        }
                                    })
                                    ->columnSpanFull(),
                                Textarea::make('body')
                                    ->label('Nội dung HTML/shortcode gốc')
                                    ->rows(24)
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan(2),
                        Section::make('Xuất bản')
                            ->icon(Heroicon::OutlinedCloudArrowUp)
                            ->components([
                                Select::make('type')
                                    ->label('Loại nội dung')
                                    ->options([
                                        'post' => 'Bài viết',
                                        'page' => 'Trang',
                                        'landing' => 'Landing page',
                                        'service' => 'Dịch vụ',
                                        'us_portfolio' => 'Dự án',
                                        'partner' => 'Đối tác',
                                        'us_testimonial' => 'Đánh giá',
                                        'video' => 'Video',
                                    ])
                                    ->required()
                                    ->searchable(),
                                Select::make('status')
                                    ->label('Trạng thái')
                                    ->options([
                                        'published' => 'Đã xuất bản',
                                        'draft' => 'Bản nháp',
                                        'pending' => 'Chờ duyệt',
                                        'private' => 'Riêng tư',
                                        'scheduled' => 'Đã lên lịch',
                                    ])
                                    ->required()
                                    ->default('draft'),
                                DateTimePicker::make('published_at')
                                    ->label('Ngày xuất bản'),
                                Select::make('parent_id')
                                    ->label('Trang cha')
                                    ->relationship('parent', 'title')
                                    ->searchable()
                                    ->preload(),
                                TextInput::make('menu_order')
                                    ->label('Thứ tự')
                                    ->numeric()
                                    ->helperText('Để trống để tự xếp sau nội dung cùng cấp.'),
                                Select::make('featured_media_source_id')
                                    ->label('Ảnh đại diện')
                                    ->searchable()
                                    ->getSearchResultsUsing(fn (string $search): array => self::searchMedia($search))
                                    ->getOptionLabelUsing(fn ($value): ?string => self::resolveMediaLabel($value))
                                    ->live()
                                    ->helperText('Tìm theo tiêu đề, alt text hoặc ID media.'),
                                Placeholder::make('featured_media_preview')
                                    ->label('Xem trước')
                                    ->content(fn (Get $get): HtmlString => self::mediaPreview($get('featured_media_source_id'))),
                            ])
                            ->columnSpan(1),
                    ])
                    ->columnSpanFull(),
                Section::make('SEO')
                    ->description('Trường trống sẽ dùng template Rank Math đã nhập từ WordPress.')
                    ->icon(Heroicon::OutlinedMagnifyingGlass)
                    ->components([
                        TextInput::make('seo_title')
                            ->label('SEO title')
                            ->helperText('Có thể giữ biến như %title%, %sep%, %sitename%.')
                            ->formatStateUsing(fn (?string $state, $record): ?string => $state ?: ContentSeoFallbacks::title($record?->title))
                            ->columnSpanFull(),
                        Textarea::make('seo_description')
                            ->label('Meta description')
                            ->rows(3)
                            ->formatStateUsing(fn (?string $state, $record): ?string => $state ?: ContentSeoFallbacks::description($record?->excerpt))
                            ->columnSpanFull(),
                        TextInput::make('seo_canonical_url')
                            ->label('Canonical URL ghi đè')
                            ->url(),
                        TagsInput::make('seo_robots')
                            ->label('Robots'),
                        TextInput::make('focus_keyword')
                            ->label('Từ khóa chính'),
                        TextInput::make('seo_score')
                            ->label('Điểm Rank Math cũ')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),
                        Toggle::make('is_pillar_content')
                            ->label('Nội dung trụ cột'),
                        Toggle::make('exclude_from_sitemap')
                            ->label('Loại khỏi sitemap'),
                        Toggle::make('needs_seo_review')
                            ->label('Cần rà soát SEO'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Open Graph & Twitter')
                    ->icon(Heroicon::OutlinedShare)
                    ->collapsed()
                    ->components([
                        TextInput::make('og_title')->label('OG title'),
                        TextInput::make('og_image_url')->label('OG image')->url(),
                        Textarea::make('og_description')->label('OG description')->rows(3)->columnSpanFull(),
                        TextInput::make('twitter_title')->label('Twitter title'),
                        TextInput::make('twitter_image_url')->label('Twitter image')->url(),
                        Textarea::make('twitter_description')->label('Twitter description')->rows(3)->columnSpanFull(),
                        Textarea::make('structured_data')
                            ->label('Schema JSON')
                            ->json()
                            ->formatStateUsing(fn ($state) => is_array($state)
                                ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                                : $state)
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? json_decode($state, true) : null)
                            ->rows(12)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Dữ liệu nguồn WordPress')
                    ->icon(Heroicon::OutlinedCircleStack)
                    ->collapsed()
                    ->components([
                        TextInput::make('source')->label('Nguồn')->disabled()->dehydrated()->default('native'),
                        TextInput::make('source_id')->label('WordPress ID')->disabled()->dehydrated(),
                        TextInput::make('legacy_url')->label('URL cũ')->disabled()->dehydrated(false),
                        TextInput::make('seo_source')->label('Nguồn SEO')->disabled()->dehydrated()->default('template'),
                        DateTimePicker::make('content_modified_at')->label('Sửa lần cuối ở nguồn'),
                        Textarea::make('legacy_meta')
                            ->label('Post meta gốc')
                            ->formatStateUsing(fn ($state) => is_array($state)
                                ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                                : $state)
                            ->disabled()
                            ->dehydrated(false)
                            ->rows(12)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    /**
     * @return array<int|string, string>
     */
    private static function searchMedia(string $search): array
    {
        return MediaAsset::query()
            ->whereNotNull('source_id')
            ->where('mime_type', 'like', 'image/%')
            ->where(function (Builder $query) use ($search): void {
                $query
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('effective_alt_text', 'like', "%{$search}%")
                    ->orWhere('alt_text', 'like', "%{$search}%");

                if (is_numeric($search)) {
                    $query->orWhere('source_id', (int) $search);
                }
            })
            ->latest('published_at')
            ->limit(50)
            ->get()
            ->mapWithKeys(fn (MediaAsset $media): array => [
                (string) $media->source_id => self::mediaLabel($media),
            ])
            ->all();
    }

    private static function resolveMediaLabel(mixed $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        $media = MediaAsset::query()
            ->where('source_id', $value)
            ->first();

        return $media ? self::mediaLabel($media) : "Media #{$value}";
    }

    private static function mediaLabel(MediaAsset $media): string
    {
        $title = trim((string) ($media->title ?: $media->effective_alt_text ?: $media->alt_text ?: $media->slug));

        return sprintf('%s · ID %s', $title ?: 'Ảnh không có tiêu đề', $media->source_id);
    }

    private static function mediaPreview(mixed $value): HtmlString
    {
        if (blank($value)) {
            return new HtmlString('<span class="text-sm text-gray-500">Chưa chọn ảnh đại diện.</span>');
        }

        $media = MediaAsset::query()
            ->where('source_id', $value)
            ->first();

        if (! $media || blank($media->public_url)) {
            return new HtmlString('<span class="text-sm text-danger-600">Không tìm thấy tệp ảnh.</span>');
        }

        $url = e($media->public_url);
        $alt = e($media->effective_alt_text ?: $media->alt_text ?: $media->title ?: 'Ảnh đại diện');

        return new HtmlString(
            '<img src="'.$url.'" alt="'.$alt.'" class="h-28 w-full rounded-lg bg-gray-100 object-cover object-center">'
        );
    }
}
