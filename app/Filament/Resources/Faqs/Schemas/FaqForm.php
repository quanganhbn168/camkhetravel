<?php

namespace App\Filament\Resources\Faqs\Schemas;

use App\Models\LandingPage;
use App\Models\Post;
use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

final class FaqForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Nội dung câu hỏi')
                ->icon(Heroicon::OutlinedQuestionMarkCircle)
                ->schema([
                    TextInput::make('question')->label('Câu hỏi')->required()->maxLength(500),
                    Textarea::make('answer')->label('Trả lời')->required()->rows(5)->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Phạm vi hiển thị')
                ->icon(Heroicon::OutlinedLink)
                ->description('Một câu hỏi có thể thuộc trang chủ hoặc gắn với một nội dung cụ thể. Không cần thêm cột FAQ vào từng bảng nội dung.')
                ->schema([
                    Select::make('faqable_type')
                        ->label('Gắn với')
                        ->options([
                            'homepage' => 'Trang chủ',
                            'service' => 'Dịch vụ',
                            'project' => 'Dự án',
                            'product' => 'Sản phẩm',
                            'landing-page' => 'Landing page',
                            'post' => 'Bài viết',
                        ])
                        ->default('homepage')
                        ->live()
                        ->afterStateUpdated(function (?string $state, $set): void {
                            if ($state === 'homepage') {
                                $set('faqable_id', null);
                                $set('group', 'homepage');
                            } else {
                                $set('group', 'detail');
                            }
                        })
                        ->required(),
                    Select::make('faqable_id')
                        ->label('Nội dung')
                        ->options(fn (Get $get): array => self::optionsFor((string) $get('faqable_type')))
                        ->visible(fn (Get $get): bool => $get('faqable_type') !== 'homepage')
                        ->required(fn (Get $get): bool => $get('faqable_type') !== 'homepage')
                        ->searchable()
                        ->preload(),
                    Select::make('group')
                        ->label('Nhóm')
                        ->options(['homepage' => 'Trang chủ', 'detail' => 'Chi tiết nội dung'])
                        ->default('homepage')
                        ->required(),
                    TextInput::make('sort_order')->label('Thứ tự')->numeric()->default(0),
                    Toggle::make('is_active')->label('Hiển thị')->default(true),
                ])
                ->columns(2),
        ]);
    }

    /** @return array<int|string, string> */
    private static function optionsFor(string $type): array
    {
        return match ($type) {
            'service' => Service::query()->orderBy('title')->pluck('title', 'id')->all(),
            'project' => Project::query()->orderBy('title')->pluck('title', 'id')->all(),
            'product' => Product::query()->orderBy('title')->pluck('title', 'id')->all(),
            'landing-page' => LandingPage::query()->orderBy('title')->pluck('title', 'id')->all(),
            'post' => Post::query()->orderBy('title')->pluck('title', 'id')->all(),
            default => [],
        };
    }
}
