<?php

namespace App\Filament\Pages;

use App\Models\Language;
use App\Settings\HomepageSettings;
use App\Support\Localization\LanguageCatalog;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageHomepageSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHomeModern;

    protected static ?string $navigationLabel = 'Cài đặt trang chủ';

    protected static ?string $title = 'Nội dung trang chủ';

    protected static string|UnitEnum|null $navigationGroup = 'Cài đặt website';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.manage-homepage-settings';

    public ?array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function mount(): void
    {
        $this->redirect(ManageSettings::getUrl());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Nội dung theo ngôn ngữ')
                    ->tabs(app(LanguageCatalog::class)->active()
                        ->map(fn (Language $language): Tab => $this->languageTab($language))
                        ->values()
                        ->all()),
            ])
            ->statePath('data');
    }

    protected function languageTab(Language $language): Tab
    {
        $locale = $language->code;

        return Tab::make($language->name)
            ->schema([
                Section::make('Form tư vấn')
                    ->schema([
                        TextInput::make("consultation_title.{$locale}")->label('Tiêu đề')->maxLength(255)->columnSpanFull(),
                        Textarea::make("consultation_content.{$locale}")->label('Nội dung')->rows(3)->columnSpanFull(),
                    ]),
                Section::make('Câu hỏi thường gặp')
                    ->schema([
                        TextInput::make("faq_title.{$locale}")
                            ->label('Tiêu đề')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make("faq_description.{$locale}")
                            ->label('Mô tả ngắn')
                            ->rows(2)
                            ->columnSpanFull(),
                        Repeater::make('faq_items')
                            ->label('Danh sách câu hỏi')
                            ->schema([
                                TextInput::make("question.{$locale}")
                                    ->label('Câu hỏi')
                                    ->maxLength(500)
                                    ->columnSpanFull(),
                                Textarea::make("answer.{$locale}")
                                    ->label('Trả lời')
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ])
                            ->addActionLabel('Thêm câu hỏi')
                            ->reorderable()
                            ->cloneable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['question'][$locale] ?? 'Câu hỏi mới')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')->label('Lưu nội dung')->submit('save'),
        ];
    }

    public function save(HomepageSettings $settings): void
    {
        foreach ($this->form->getState() as $key => $value) {
            $settings->{$key} = $value;
        }

        $settings->save();

        Notification::make()->title('Đã lưu nội dung trang chủ')->success()->send();
    }
}
