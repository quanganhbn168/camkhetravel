<?php

namespace App\Filament\Resources\AboutDepartments\Schemas;

use App\Models\AboutDepartment;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class AboutDepartmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin phòng ban')
                    ->icon(Heroicon::OutlinedBuildingOffice2)
                    ->description('Tên và mô tả này hiển thị phía trên danh sách nhân sự của từng phòng.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Tên phòng ban')
                            ->placeholder('Ví dụ: Phòng điều hành')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->label('Mô tả phòng ban')
                            ->rows(4)
                            ->columnSpanFull(),
                        TextInput::make('sort_order')
                            ->label('Thứ tự hiển thị')
                            ->numeric()
                            ->minValue(0)
                            ->default(fn (): int => ((int) AboutDepartment::query()->max('sort_order')) + 1),
                        Toggle::make('is_active')
                            ->label('Hiển thị phòng ban')
                            ->default(true),
                    ])
                    ->columns(2),
                Section::make('Nhân sự thuộc phòng')
                    ->icon(Heroicon::OutlinedIdentification)
                    ->description('Mỗi người có ảnh riêng, họ tên và chức vụ. Kéo thả để đổi thứ tự hiển thị trong phòng.')
                    ->schema([
                        Repeater::make('members')
                            ->label('Danh sách nhân sự')
                            ->relationship(modifyQueryUsing: fn (Builder $query): Builder => $query->orderBy('sort_order')->orderBy('id'))
                            ->schema([
                                CuratorPicker::make('media_id')
                                    ->label('Ảnh nhân sự')
                                    ->disk('public')
                                    ->constrained()
                                    ->acceptedFileTypes(['image/*'])
                                    ->required()
                                    ->dehydrated()
                                    ->columnSpanFull(),
                                TextInput::make('name')
                                    ->label('Họ và tên')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('position')
                                    ->label('Chức vụ')
                                    ->required()
                                    ->maxLength(255),
                                Toggle::make('is_active')
                                    ->label('Hiển thị nhân sự')
                                    ->default(true)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Thêm nhân sự')
                            ->orderColumn('sort_order')
                            ->collapsible()
                            ->itemLabel(fn (array $state): string => trim(($state['name'] ?? 'Nhân sự mới').' — '.($state['position'] ?? '')))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
