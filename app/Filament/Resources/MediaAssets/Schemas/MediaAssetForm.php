<?php

namespace App\Filament\Resources\MediaAssets\Schemas;

use App\Support\Seo\ContentSeoFallbacks;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class MediaAssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tệp ảnh')
                    ->description('Ảnh mới được lưu trên disk public, tách khỏi thư mục WordPress nguồn.')
                    ->icon(Heroicon::OutlinedPhoto)
                    ->components([
                        FileUpload::make('file_path')
                            ->label('Ảnh')
                            ->disk('public')
                            ->directory('media/library')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->orientImagesFromExif()
                            ->openable()
                            ->downloadable()
                            ->maxSize(51200)
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->helperText('Hỗ trợ ảnh tối đa 50 MB. Ảnh cũ đã đồng bộ vẫn giữ nguyên đường dẫn hiện tại.')
                            ->columnSpanFull(),
                        Hidden::make('disk')
                            ->default('public'),
                    ])
                    ->columnSpanFull(),
                Section::make('Nội dung ảnh')
                    ->description('Tiêu đề và alt text giúp quản trị dễ tìm ảnh, đồng thời hỗ trợ SEO và khả năng truy cập.')
                    ->icon(Heroicon::OutlinedPencilSquare)
                    ->components([
                        TextInput::make('title')
                            ->label('Tiêu đề ảnh')
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->maxLength(500)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (?string $state, $get, $set): void {
                                if (blank($get('slug')) && ($slug = ContentSeoFallbacks::slug($state))) {
                                    $set('slug', $slug);
                                }
                            })
                            ->columnSpanFull(),
                        TextInput::make('slug')
                            ->label('Đường dẫn (slug)')
                            ->maxLength(255)
                            ->helperText('Tự tạo từ tiêu đề khi để trống; anh vẫn có thể sửa khi cần.')
                            ->columnSpanFull(),
                        Textarea::make('effective_alt_text')
                            ->label('Alt SEO')
                            ->rows(2)
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->helperText('Alt được Laravel dùng thực tế. Mô tả ngắn nội dung ảnh, không nhồi từ khóa.')
                            ->columnSpanFull(),
                        Textarea::make('caption')
                            ->label('Chú thích')
                            ->rows(3)
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->label('Mô tả nội bộ')
                            ->rows(5)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Section::make('Trạng thái và thông tin kỹ thuật')
                    ->icon(Heroicon::OutlinedInformationCircle)
                    ->collapsed()
                    ->components([
                        Grid::make(3)
                            ->components([
                                Select::make('localization_status')
                                    ->label('Trạng thái tệp')
                                    ->options([
                                        'localized' => 'Đã lưu nội bộ',
                                        'pending' => 'Chờ đồng bộ',
                                        'missing' => 'Thiếu tệp nguồn',
                                        'failed' => 'Đồng bộ lỗi',
                                    ])
                                    ->default('localized')
                                    ->required()
                                    ->native(false),
                                TextInput::make('mime_type')
                                    ->label('MIME type')
                                    ->maxLength(191),
                                DateTimePicker::make('published_at')
                                    ->label('Ngày tải lên'),
                                TextInput::make('width')
                                    ->label('Chiều rộng')
                                    ->numeric(),
                                TextInput::make('height')
                                    ->label('Chiều cao')
                                    ->numeric(),
                                TextInput::make('file_size')
                                    ->label('Dung lượng (byte)')
                                    ->numeric(),
                            ]),
                        Textarea::make('localization_error')
                            ->label('Lỗi đồng bộ gần nhất')
                            ->rows(3)
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Section::make('Dữ liệu nguồn')
                    ->description('Thông tin đối chiếu từ WordPress; không cần sửa khi quản lý ảnh nội bộ.')
                    ->icon(Heroicon::OutlinedCloudArrowUp)
                    ->collapsed()
                    ->components([
                        Grid::make(3)
                            ->components([
                                TextInput::make('source')
                                    ->label('Nguồn')
                                    ->required()
                                    ->default('native')
                                    ->disabled()
                                    ->dehydrated(),
                                TextInput::make('source_id')
                                    ->label('ID nguồn')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated()
                                    ->helperText('Ảnh mới sẽ được cấp ID nội bộ nếu để trống.'),
                                TextInput::make('parent_source_id')
                                    ->label('ID bài cha')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(),
                            ]),
                        Textarea::make('source_url')
                            ->label('URL nguồn')
                            ->rows(2)
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                        Textarea::make('source_path')
                            ->label('Đường dẫn nguồn')
                            ->rows(2)
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                        Textarea::make('alt_text')
                            ->label('Alt gốc WordPress')
                            ->rows(2)
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
