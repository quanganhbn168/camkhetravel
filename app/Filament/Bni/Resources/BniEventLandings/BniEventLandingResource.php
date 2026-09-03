<?php

namespace App\Filament\Bni\Resources\BniEventLandings;

use App\Filament\Bni\Resources\BniEventLandings\Pages\CreateBniEventLanding;
use App\Filament\Bni\Resources\BniEventLandings\Pages\EditBniEventLanding;
use App\Filament\Bni\Resources\BniEventLandings\Pages\ListBniEventLandings;
use App\Models\BniEventLanding;
use App\Support\Bni\BniPanelAccess;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BniEventLandingResource extends Resource
{
    protected static ?string $model = BniEventLanding::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationLabel = 'Tổng quan & thể lệ';

    protected static ?string $modelLabel = 'tổng quan Pickleball';

    protected static ?string $pluralModelLabel = 'Tổng quan & thể lệ';

    protected static ?int $navigationSort = 1;

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
            Section::make('Sự kiện Pickleball')
                ->icon('heroicon-o-trophy')
                ->description('Nội dung này được quản lý bằng bảng dữ liệu riêng của sự kiện.')
                ->schema([
                    Select::make('bni_event_id')
                        ->label('Sự kiện')
                        ->relationship('event', 'title', fn (Builder $query): Builder => $query->where('type', 'pickleball'))
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->searchable()
                        ->preload()
                        ->columnSpanFull(),
                    TextInput::make('countdown_label')->label('Nhãn đếm ngược')->default('Đếm ngược đến giải đấu')->maxLength(255)->columnSpanFull(),
                    TextInput::make('registration_title')->label('Tiêu đề đăng ký')->default('Đăng ký tham gia')->maxLength(255)->columnSpanFull(),
                    Textarea::make('registration_description')->label('Mô tả đăng ký')->rows(3)->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull(),
            Section::make('Cơ cấu giải thưởng')
                ->icon('heroicon-o-gift')
                ->description('Các hạng mục giải được thêm, sửa và sắp xếp tại mục “Cơ cấu giải thưởng” trong nhóm Pickleball.')
                ->schema([
                    TextInput::make('prizes_title')->label('Tiêu đề khu giải thưởng')->default('Cơ cấu giải thưởng')->maxLength(255)->columnSpanFull(),
                    Textarea::make('prizes_description')->label('Mô tả chung')->rows(2)->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull(),
            Section::make('Thể lệ giải đấu')
                ->icon('heroicon-o-clipboard-document-check')
                ->schema([
                    TextInput::make('rules_title')->label('Tiêu đề thể lệ')->default('Thể lệ giải đấu')->maxLength(255)->columnSpanFull(),
                    RichEditor::make('rules')->label('Nội dung thể lệ')->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event.title')->label('Sự kiện')->searchable()->sortable(),
                TextColumn::make('prizes_count')->counts('prizes')->label('Hạng mục giải')->sortable(),
                TextColumn::make('rules_title')->label('Thể lệ')->placeholder('Thể lệ giải đấu'),
                TextColumn::make('updated_at')->label('Cập nhật')->since()->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->slideOver(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBniEventLandings::route('/'),
            'create' => CreateBniEventLanding::route('/create'),
            'edit' => EditBniEventLanding::route('/{record}/edit'),
        ];
    }
}
