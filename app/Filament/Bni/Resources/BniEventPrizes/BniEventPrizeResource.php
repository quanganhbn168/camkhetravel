<?php

namespace App\Filament\Bni\Resources\BniEventPrizes;

use App\Filament\Bni\Resources\BniEventPrizes\Pages\CreateBniEventPrize;
use App\Filament\Bni\Resources\BniEventPrizes\Pages\EditBniEventPrize;
use App\Filament\Bni\Resources\BniEventPrizes\Pages\ListBniEventPrizes;
use App\Models\BniEventLanding;
use App\Models\BniEventPrize;
use App\Support\Bni\BniPanelAccess;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BniEventPrizeResource extends Resource
{
    protected static ?string $model = BniEventPrize::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationLabel = 'Cơ cấu giải thưởng';

    protected static ?string $modelLabel = 'hạng mục giải thưởng';

    protected static ?string $pluralModelLabel = 'Cơ cấu giải thưởng';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Pickleball';
    }

    public static function canViewAny(): bool
    {
        return BniPanelAccess::canManageEverything();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Hạng mục giải thưởng')
                ->icon('heroicon-o-gift')
                ->schema([
                    Select::make('bni_event_landing_id')
                        ->label('Giải Pickleball')
                        ->options(fn (): array => BniEventLanding::query()
                            ->whereHas('event', fn (Builder $query): Builder => $query->where('type', 'pickleball'))
                            ->with('event:id,title')
                            ->get()
                            ->mapWithKeys(fn (BniEventLanding $landing): array => [$landing->id => $landing->event?->title ?: 'Giải Pickleball'])
                            ->all())
                        ->required()
                        ->searchable()
                        ->preload()
                        ->columnSpanFull(),
                    TextInput::make('title')->label('Tên hạng mục')->required()->maxLength(255)->columnSpanFull(),
                    TextInput::make('value')->label('Giá trị / phần thưởng')->maxLength(255)->columnSpanFull(),
                    Textarea::make('description')->label('Mô tả')->rows(3)->columnSpanFull(),
                    Toggle::make('highlight')->label('Nhấn mạnh')->default(false)->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Hạng mục')->searchable()->sortable(),
                TextColumn::make('value')->label('Giải thưởng')->placeholder('Chưa nhập')->wrap(),
                TextColumn::make('landing.event.title')->label('Giải Pickleball')->toggleable(),
                ToggleColumn::make('highlight')->label('Nhấn mạnh'),
            ])
            ->filters([
                SelectFilter::make('bni_event_landing_id')
                    ->label('Giải Pickleball')
                    ->options(fn (): array => BniEventLanding::query()
                        ->whereHas('event', fn (Builder $query): Builder => $query->where('type', 'pickleball'))
                        ->with('event:id,title')
                        ->get()
                        ->mapWithKeys(fn (BniEventLanding $landing): array => [$landing->id => $landing->event?->title ?: 'Giải Pickleball'])
                        ->all()),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->slideOver(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBniEventPrizes::route('/'),
            'create' => CreateBniEventPrize::route('/create'),
            'edit' => EditBniEventPrize::route('/{record}/edit'),
        ];
    }
}
