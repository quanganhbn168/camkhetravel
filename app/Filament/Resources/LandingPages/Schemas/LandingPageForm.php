<?php

namespace App\Filament\Resources\LandingPages\Schemas;

use App\Filament\Forms\SeoFields;
use App\Filament\RichEditor\ScopedAttachCuratorMediaPlugin;
use App\Models\LandingPage;
use App\Support\Seo\ContentSeoFallbacks;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

final class LandingPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['lg' => 3])
            ->components([
                Group::make([
                    Section::make('Nội dung landing page')
                        ->icon(Heroicon::OutlinedDocumentText)
                        ->schema([
                            TextInput::make('title')
                                ->label('Tên landing page')
                                ->required()
                                ->maxLength(255)
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
                            TextInput::make('slug')->label('Đường dẫn (slug)')->maxLength(255)->formatStateUsing(fn (?string $state, ?LandingPage $record): ?string => $state ?: $record?->slug)->columnSpanFull(),
                            CuratorPicker::make('curator_media_id')->label('Ảnh đại diện / hero')->relationship('curatorMedia', 'id')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->columnSpanFull(),
                            Textarea::make('excerpt')->label('Mô tả ngắn')->rows(3)->columnSpanFull(),
                            RichEditor::make('body')
                                ->label('Nội dung bổ sung')
                                ->plugins([ScopedAttachCuratorMediaPlugin::make()])
                                ->enableToolbarButtons(['attachCuratorMedia'])
                                ->disableToolbarButtons(['attachFiles'])
                                ->columnSpanFull(),
                            ...SeoFields::make(),
                        ])
                        ->columns(2),
                    Section::make('Dữ liệu hiển thị trong landing')
                        ->icon(Heroicon::OutlinedLink)
                        ->description('Các block đọc trực tiếp những quan hệ này từ database Laravel: danh mục dịch vụ, dịch vụ, dự án và blog.')
                        ->schema([
                            Select::make('serviceCategories')->label('Danh mục dịch vụ')->relationship('serviceCategories', 'name')->multiple()->searchable()->preload()->columnSpanFull(),
                            Select::make('services')->label('Dịch vụ liên quan')->relationship('services', 'title')->multiple()->searchable()->preload()->columnSpanFull(),
                            Select::make('projects')->label('Dự án liên quan')->relationship('projects', 'title')->multiple()->searchable()->preload()->columnSpanFull(),
                            Select::make('posts')->label('Bài viết / blog liên quan')->relationship('posts', 'title')->multiple()->searchable()->preload()->columnSpanFull(),
                            Select::make('tags')
                                ->label('Thẻ')
                                ->relationship('tags', 'name')
                                ->multiple()
                                ->searchable()
                                ->preload()
                                ->createOptionForm([
                                    TextInput::make('name')->label('Tên thẻ')->required(),
                                ])
                                ->columnSpanFull(),
                        ])
                        ->columns(2),
                    ...LandingExperienceSchema::components(),
                ])->columnSpan(['lg' => 2]),
                Section::make('Trạng thái')
                    ->icon(Heroicon::OutlinedCog6Tooth)
                    ->schema([
                        Select::make('status')->label('Trạng thái')->options(['draft' => 'Bản nháp', 'published' => 'Đã xuất bản', 'pending' => 'Chờ duyệt', 'private' => 'Riêng tư'])->required()->default('draft'),
                        TextInput::make('sort_order')->label('Thứ tự')->numeric()->default(fn (): int => ((int) LandingPage::query()->max('sort_order')) + 1),
                        Toggle::make('is_featured')->label('Landing nổi bật'),
                    ])
                    ->columns(1)
                    ->columnSpan(['lg' => 1]),
            ]);
    }
}
