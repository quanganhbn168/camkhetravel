<?php

namespace App\Filament\Resources\HeroSlides\Schemas;

use App\Support\Localization\LanguageCatalog;
use App\Support\Media\VideoMediaLibrary;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class HeroSlideForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Nội dung tiếng Việt')
                ->icon(Heroicon::OutlinedPresentationChartLine)
                ->schema([
                    CuratorPicker::make('curator_media_id')
                        ->label('Ảnh nền')
                        ->relationship('curatorMedia', 'id')
                        ->disk('public')
                        ->constrained()
                        ->acceptedFileTypes(['image/*'])
                        ->columnSpanFull(),
                    TextInput::make('eyebrow')->label('Nhãn nhỏ')->maxLength(255),
                    TextInput::make('title')
                        ->label('Tiêu đề')
                        ->helperText('Để trống toàn bộ phần nội dung và nút nếu slide chỉ hiển thị ảnh.')
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Textarea::make('description')->label('Mô tả')->rows(3)->columnSpanFull(),
                    TextInput::make('primary_label')->label('Nhãn nút chính')->maxLength(255),
                    TextInput::make('primary_url')->label('URL nút chính')->url()->maxLength(255),
                    TextInput::make('secondary_label')->label('Nhãn nút phụ')->maxLength(255),
                    TextInput::make('secondary_url')->label('URL nút phụ')->url()->maxLength(255),
                ])
                ->columns(2),
            Section::make('Video cho slide')
                ->icon(Heroicon::OutlinedVideoCamera)
                ->description('Khi có video, trang chủ sẽ hiện nút Play ở giữa slide và mở video bằng GLightbox.')
                ->schema([
                    ToggleButtons::make('video_source')
                        ->label('Nguồn video')
                        ->options([
                            'youtube' => 'Link YouTube',
                            'upload' => 'Thư viện / tải video',
                        ])
                        ->icons([
                            'youtube' => Heroicon::OutlinedPlayCircle,
                            'upload' => Heroicon::OutlinedArrowUpTray,
                        ])
                        ->colors([
                            'youtube' => 'danger',
                            'upload' => 'primary',
                        ])
                        ->live()
                        ->inline()
                        ->grouped()
                        ->columnSpanFull(),
                    TextInput::make('video_url')
                        ->label('Link YouTube')
                        ->helperText('Dán link video YouTube công khai; video chỉ tải khi khách bấm Play.')
                        ->url()
                        ->maxLength(1024)
                        ->required(fn ($get): bool => $get('video_source') === 'youtube')
                        ->visible(fn ($get): bool => $get('video_source') === 'youtube')
                        ->columnSpanFull(),
                    CuratorPicker::make('video_media_id')
                        ->label('Video từ thư viện media')
                        ->relationship('videoMedia', 'id')
                        ->disk('public')
                        ->constrained()
                        ->acceptedFileTypes(['video/*'])
                        ->hintAction(Action::make('chooseLibraryVideo')
                            ->label('Chọn video có sẵn')
                            ->icon(Heroicon::OutlinedFilm)
                            ->modalHeading('Chọn video từ thư viện media')
                            ->modalSubmitActionLabel('Dùng video này')
                            ->schema([
                                Select::make('media_id')->label('Video trong thư viện')
                                    ->options(fn () => VideoMediaLibrary::options())
                                    ->getSearchResultsUsing(fn (string $search) => VideoMediaLibrary::options($search))
                                    ->searchable()->required()
                                    ->helperText('Chỉ hiển thị file video: MP4, WebM, MOV, M4V, AVI, MKV, MPEG, MPG, OGV.')
                                    ->columnSpanFull(),
                            ])
                            ->action(function (array $data, CuratorPicker $component): void {
                                $media = VideoMediaLibrary::query()->find($data['media_id']);
                                if (! $media) {
                                    throw ValidationException::withMessages(['media_id' => 'Vui lòng chọn một file video trong thư viện.']);
                                }
                                $component->state([(string) Str::uuid() => $media->toArray()]);
                            }))
                        ->helperText('Ưu tiên MP4 hoặc WebM để phát tốt trên trình duyệt.')
                        ->required(fn ($get): bool => $get('video_source') === 'upload')
                        ->visible(fn ($get): bool => $get('video_source') === 'upload')
                        ->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Bản dịch')
                ->icon(Heroicon::OutlinedLanguage)
                ->description('Chỉ thêm bản dịch khi nội dung đã sẵn sàng xuất bản cho ngôn ngữ đó.')
                ->schema([
                    Repeater::make('translations')
                        ->relationship()
                        ->schema([
                            Select::make('locale')
                                ->label('Ngôn ngữ')
                                ->options(fn (): array => app(LanguageCatalog::class)->options())
                                ->required(),
                            TextInput::make('eyebrow')->label('Nhãn nhỏ')->maxLength(255),
                            TextInput::make('title')->label('Tiêu đề')->maxLength(255)->columnSpanFull(),
                            Textarea::make('description')->label('Mô tả')->rows(3)->columnSpanFull(),
                            TextInput::make('primary_label')->label('Nhãn nút chính')->maxLength(255),
                            TextInput::make('primary_url')->label('URL nút chính')->url()->maxLength(255),
                            TextInput::make('secondary_label')->label('Nhãn nút phụ')->maxLength(255),
                            TextInput::make('secondary_url')->label('URL nút phụ')->url()->maxLength(255),
                        ])
                        ->columns(2)
                        ->addActionLabel('Thêm bản dịch')
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => app(LanguageCatalog::class)->find((string) ($state['locale'] ?? ''))?->name)
                        ->columnSpanFull(),
                ]),
            Section::make('Hiển thị')
                ->icon(Heroicon::OutlinedCog6Tooth)
                ->schema([
                    Toggle::make('is_active')->label('Hiển thị trên trang chủ')->default(true),
                ]),
        ]);
    }
}
