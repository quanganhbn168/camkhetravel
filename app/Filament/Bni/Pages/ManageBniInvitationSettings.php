<?php

namespace App\Filament\Bni\Pages;

use App\Settings\BniInvitationSettings;
use App\Support\Bni\BniPanelAccess;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
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

class ManageBniInvitationSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $navigationLabel = 'Mẫu thư mời';

    protected static ?string $title = 'Mẫu thư mời BNI';

    protected static string|UnitEnum|null $navigationGroup = 'Lễ chuyển giao';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.bni.pages.manage-invitation-settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return BniPanelAccess::canManageEverything();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public function mount(BniInvitationSettings $settings): void
    {
        $this->form->fill([
            'label' => $settings->label,
            'event_label' => $settings->event_label,
            'greeting' => $settings->greeting,
            'default_guest_name' => $settings->default_guest_name,
            'content_title' => $settings->content_title,
            'content' => $settings->content,
            'schedule_title' => $settings->schedule_title,
            'note_title' => $settings->note_title,
            'note_content' => $settings->note_content,
            'rsvp_title' => $settings->rsvp_title,
            'rsvp_description' => $settings->rsvp_description,
            'contact_title' => $settings->contact_title,
            'contact_description' => $settings->contact_description,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Mẫu thư mời BNI')
                    ->tabs([
                        Tab::make('Mở đầu')
                            ->schema([
                                Section::make('Nội dung chung toàn hệ thống')
                                    ->icon(Heroicon::OutlinedEnvelope)
                                    ->description('Các trường ở đây áp dụng cho tất cả sự kiện, chapter và thư mời BNI.')
                                    ->schema([
                                        TextInput::make('label')->label('Nhãn thư mời')->required()->maxLength(255),
                                        TextInput::make('event_label')->label('Tên loại sự kiện trên thiệp')->required()->maxLength(255),
                                        TextInput::make('greeting')->label('Lời kính mời')->required()->maxLength(255),
                                        TextInput::make('default_guest_name')->label('Tên mặc định khi chưa chỉ định khách')->required()->maxLength(255),
                                    ])
                                    ->columns(2),
                            ]),
                        Tab::make('Nội dung chương trình')
                            ->schema([
                                Section::make('Lời mời')
                                    ->icon(Heroicon::OutlinedDocumentText)
                                    ->schema([
                                        TextInput::make('content_title')->label('Tiêu đề nội dung')->required()->maxLength(255),
                                        RichEditor::make('content')->label('Nội dung thư mời')->required()->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ]),
                        Tab::make('Lịch trình & lưu ý')
                            ->schema([
                                Section::make('Thông tin tham dự')
                                    ->icon(Heroicon::OutlinedCalendarDays)
                                    ->schema([
                                        TextInput::make('schedule_title')->label('Tiêu đề lịch trình')->required()->maxLength(255),
                                        TextInput::make('note_title')->label('Tiêu đề lưu ý')->required()->maxLength(255),
                                        RichEditor::make('note_content')->label('Lưu ý tham dự / dress code')->required()->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ]),
                        Tab::make('RSVP & liên hệ')
                            ->schema([
                                Section::make('Phản hồi tham dự')
                                    ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                                    ->schema([
                                        TextInput::make('rsvp_title')->label('Tiêu đề RSVP')->required()->maxLength(255),
                                        Textarea::make('rsvp_description')->label('Mô tả RSVP')->required()->rows(3)->columnSpanFull(),
                                        TextInput::make('contact_title')->label('Tiêu đề liên hệ')->required()->maxLength(255),
                                        Textarea::make('contact_description')->label('Mô tả liên hệ')->required()->rows(3)->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')->label('Lưu mẫu thư mời')->submit('save'),
        ];
    }

    public function save(BniInvitationSettings $settings): void
    {
        foreach ($this->form->getState() as $key => $value) {
            $settings->{$key} = $value;
        }

        $settings->save();

        Notification::make()->title('Đã lưu mẫu thư mời chung')->success()->send();
    }
}
