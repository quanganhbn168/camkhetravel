<?php

namespace App\Filament\Resources\Terms\Schemas;

use App\Support\Seo\ContentSeoFallbacks;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TermForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('taxonomy_id')
                    ->label('Taxonomy')
                    ->relationship('taxonomy', 'label')
                    ->required(),
                TextInput::make('source_id')
                    ->label('WordPress ID')
                    ->numeric(),
                TextInput::make('name')
                    ->label('Tên')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state, $get, $set): void {
                        if (blank($get('slug')) && ($slug = ContentSeoFallbacks::slug($state))) {
                            $set('slug', $slug);
                        }
                    }),
                TextInput::make('slug')
                    ->label('Đường dẫn (slug)')
                    ->required(),
                Textarea::make('description')
                    ->label('Mô tả')
                    ->columnSpanFull(),
                Select::make('parent_id')
                    ->label('Mục cha')
                    ->relationship('parent', 'name'),
                TextInput::make('parent_source_id')
                    ->label('WordPress ID mục cha')
                    ->numeric(),
                TextInput::make('source_count')
                    ->label('Số nội dung từ nguồn')
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('legacy_meta')
                    ->label('Metadata nguồn')
                    ->formatStateUsing(fn ($state) => is_array($state)
                        ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                        : $state)
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? json_decode($state, true) : null)
                    ->rows(8)
                    ->columnSpanFull(),
            ]);
    }
}
