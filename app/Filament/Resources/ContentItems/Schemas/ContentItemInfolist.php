<?php

namespace App\Filament\Resources\ContentItems\Schemas;

use App\Models\ContentItem;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ContentItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('source'),
                TextEntry::make('source_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('type'),
                TextEntry::make('status'),
                TextEntry::make('title')
                    ->columnSpanFull(),
                TextEntry::make('slug'),
                TextEntry::make('canonical_path')
                    ->placeholder('-'),
                TextEntry::make('legacy_url')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('parent.title')
                    ->label('Parent')
                    ->placeholder('-'),
                TextEntry::make('parent_source_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('author_source_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('menu_order')
                    ->numeric(),
                TextEntry::make('excerpt')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('body')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('featured_media_source_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('published_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('content_modified_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('seo_title')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('seo_description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('seo_canonical_url')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('focus_keyword')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('seo_score')
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('is_pillar_content')
                    ->boolean(),
                IconEntry::make('exclude_from_sitemap')
                    ->boolean(),
                TextEntry::make('og_title')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('og_description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                ImageEntry::make('og_image_url')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('twitter_title')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('twitter_description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                ImageEntry::make('twitter_image_url')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('seo_source'),
                TextEntry::make('effective_seo.title')
                    ->label('SEO title hiệu lực')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('effective_seo.description')
                    ->label('Meta description hiệu lực')
                    ->placeholder('-')
                    ->columnSpanFull(),
                ImageEntry::make('effective_seo.og_image')
                    ->label('Ảnh social hiệu lực')
                    ->getStateUsing(function (ContentItem $record): ?string {
                        $path = data_get($record->effective_seo, 'og_image');

                        if (! is_string($path) || blank($path)) {
                            return null;
                        }

                        return str_starts_with($path, '/')
                            ? rtrim((string) config('app.url'), '/').$path
                            : $path;
                    })
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('seo_materialized_at')
                    ->label('SEO chuẩn hóa lúc')
                    ->dateTime()
                    ->placeholder('-'),
                IconEntry::make('needs_seo_review')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (ContentItem $record): bool => $record->trashed()),
            ]);
    }
}
