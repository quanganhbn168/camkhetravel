<?php

namespace App\Filament\Resources\SolutionCategories\Tables;

use App\Models\SolutionCategory;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

final class SolutionCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('curatorMedia')->withCount('solutions'))
            ->columns([
                CuratorColumn::make('curatorMedia')->label('Ảnh')->square(),
                TextColumn::make('name')->label('Danh mục')->searchable()->sortable()->wrap(),
                TextColumn::make('solutions_count')->label('Số giải pháp')->sortable(),
                ToggleColumn::make('is_active')->label('Công khai')
                    ->disabled(fn (SolutionCategory $record) => ! auth()->user()->can('update', $record)),
                TextColumn::make('sort_order')->label('Thứ tự')->sortable(),
            ])
            ->filters([TernaryFilter::make('is_active')->label('Công khai')])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->disabled(fn (SolutionCategory $record): bool => $record->solutions_count > 0)
                    ->tooltip(fn (SolutionCategory $record): ?string => $record->solutions_count > 0
                        ? 'Chuyển hoặc xóa các giải pháp trong danh mục trước.' : null),
            ]);
    }
}
