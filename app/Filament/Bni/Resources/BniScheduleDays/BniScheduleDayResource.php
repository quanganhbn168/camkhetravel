<?php

namespace App\Filament\Bni\Resources\BniScheduleDays;

use App\Filament\Bni\Resources\BniScheduleDays\Pages\CreateBniScheduleDay;
use App\Filament\Bni\Resources\BniScheduleDays\Pages\EditBniScheduleDay;
use App\Filament\Bni\Resources\BniScheduleDays\Pages\ListBniScheduleDays;
use App\Models\BniScheduleDay;
use App\Support\Bni\BniPanelAccess;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;

class BniScheduleDayResource extends Resource
{
    protected static ?string $model = BniScheduleDay::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Lịch trình sự kiện';

    protected static ?string $modelLabel = 'ngày lịch trình';

    protected static ?string $pluralModelLabel = 'Lịch trình sự kiện';

    protected static ?int $navigationSort = 8;

    public static function getNavigationGroup(): ?string
    {
        return 'Sự kiện BNI';
    }

    public static function canViewAny(): bool
    {
        return BniPanelAccess::canManageEverything();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('event', fn (Builder $query): Builder => $query->where('type', static::eventType()));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Ngày diễn ra')
                ->icon('heroicon-o-calendar')
                ->description('Tạo một ngày trước, sau đó thêm các mốc giờ thuộc ngày đó.')
                ->schema([
                    Select::make('bni_event_id')
                        ->label('Sự kiện')
                        ->relationship('event', 'title', fn (Builder $query): Builder => $query->where('type', static::eventType()))
                        ->required()
                        ->searchable()
                        ->preload()
                        ->columnSpanFull(),
                    DatePicker::make('event_date')
                        ->label('Ngày')
                        ->required()
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->unique(
                            ignoreRecord: true,
                            modifyRuleUsing: fn (Unique $rule, Get $get): Unique => $rule->where('bni_event_id', $get('bni_event_id')),
                        )
                        ->validationMessages(['unique' => 'Sự kiện này đã có lịch trình cho ngày đã chọn.'])
                        ->columnSpanFull(),
                    TextInput::make('title')->label('Tên ngày')->maxLength(255)->placeholder('Ví dụ: Ngày khai mạc')->columnSpanFull(),
                    Textarea::make('description')->label('Ghi chú chung')->rows(2)->columnSpanFull(),
                    Toggle::make('is_active')->label('Hiển thị')->default(true)->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull(),
            Section::make('Các mốc trong ngày')
                ->icon('heroicon-o-clock')
                ->description('Chỉ cần thời gian, nội dung và mô tả. Có thể kéo thả để đổi thứ tự.')
                ->schema([
                    Repeater::make('items')
                        ->relationship('items')
                        ->label('Lịch trình')
                        ->schema([
                            TimePicker::make('starts_at')->label('Bắt đầu')->seconds(false)->columnSpanFull(),
                            TimePicker::make('ends_at')->label('Kết thúc')->seconds(false)->columnSpanFull(),
                            TextInput::make('title')->label('Nội dung')->required()->maxLength(255)->columnSpanFull(),
                            Textarea::make('description')->label('Mô tả')->rows(2)->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->defaultItems(0)
                        ->addActionLabel('Thêm mốc giờ')
                        ->orderColumn('sort_order')
                        ->reorderable()
                        ->collapsible()
                        ->itemLabel(fn (array $state): string => collect([$state['starts_at'] ?? null, $state['title'] ?? null])->filter()->implode(' · ') ?: 'Mốc lịch trình mới')
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event_date')->label('Ngày')->date('d/m/Y')->sortable(),
                TextColumn::make('title')->label('Tên ngày')->placeholder('Theo ngày diễn ra')->searchable(),
                TextColumn::make('event.title')->label('Sự kiện')->sortable(),
                TextColumn::make('items_count')->counts('items')->label('Số mốc')->sortable(),
                ToggleColumn::make('is_active')->label('Hiển thị'),
            ])
            ->filters([
                SelectFilter::make('bni_event_id')->label('Sự kiện')->relationship('event', 'title', fn (Builder $query): Builder => $query->where('type', static::eventType())),
            ])
            ->defaultSort('event_date')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->slideOver(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBniScheduleDays::route('/'),
            'create' => CreateBniScheduleDay::route('/create'),
            'edit' => EditBniScheduleDay::route('/{record}/edit'),
        ];
    }

    protected static function eventType(): string
    {
        return 'handover';
    }
}
