<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Filament\Forms\SeoFields;
use App\Filament\RichEditor\ScopedAttachCuratorMediaPlugin;
use App\Models\Project;
use App\Support\Projects\ProjectDetailContent;
use App\Support\Seo\ContentSeoFallbacks;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(['lg' => 3])->components([
            Group::make([
                Section::make('Thông tin dự án')->schema([
                    TextInput::make('title')->label('Tên dự án')->required()->maxLength(255)
                        ->live(onBlur: true)->afterStateUpdated(function (?string $state, $get, $set): void {
                            if (blank($get('slug'))) {
                                $set('slug', ContentSeoFallbacks::slug($state));
                            }
                            if (blank($get('seo_title'))) {
                                $set('seo_title', ContentSeoFallbacks::title($state));
                            }
                        }),
                    TextInput::make('slug')->label('Đường dẫn')->maxLength(255)
                        ->formatStateUsing(fn (?string $state, ?Project $record): ?string => $state ?: $record?->slug),
                    Textarea::make('excerpt')->label('Mô tả trên banner')->rows(3),
                    TextInput::make('client_name')->label('Chủ đầu tư')->maxLength(255),
                    TextInput::make('industry')->label('Loại công trình')->maxLength(255),
                    TextInput::make('details.hero.location')->label('Địa điểm')->maxLength(255),
                    TextInput::make('details.hero.area')->label('Quy mô')->maxLength(255),
                    DatePicker::make('completed_at')->label('Ngày hoàn thành'),
                    TextInput::make('video_url')->label('Video dự án')->url(),
                ]),
                Section::make('Tổng quan')->schema([
                    ...self::headings('overview', 'Tổng quan dự án', 'Thông tin chung'),
                    Textarea::make('details.notice')->label('Ghi chú đầu trang')->rows(2),
                    RichEditor::make('body')->label('Nội dung tổng quan')
                        ->plugins([ScopedAttachCuratorMediaPlugin::make()])
                        ->enableToolbarButtons(['attachCuratorMedia'])->disableToolbarButtons(['attachFiles']),
                    TextInput::make('details.overview.period')->label('Thời gian thực hiện')->maxLength(255),
                    Repeater::make('details.overview.facts')->label('Thông tin bổ sung')->schema([
                        TextInput::make('label')->label('Tên thông tin')->required()->maxLength(150),
                        Textarea::make('value')->label('Giá trị')->required()->rows(2),
                    ])->columns(2)->defaultItems(0)->addActionLabel('Thêm thông tin'),
                ]),
                Section::make('Thách thức')->schema([
                    ...self::headings('challenges', 'Yêu cầu & bài toán', 'Thách thức của dự án'),
                    Textarea::make('details.challenges.description')->label('Mô tả')->rows(3),
                    self::cards('challenges', 'Các thách thức'),
                ]),
                Section::make('Giải pháp triển khai')->schema([
                    ...self::headings('solution', 'Giải pháp triển khai', 'Giải pháp toàn diện'),
                    Textarea::make('details.solution.description')->label('Mô tả')->rows(3),
                    Repeater::make('details.solution.items')->label('Danh sách giải pháp')->schema([
                        TextInput::make('text')->label('Giải pháp')->required()->maxLength(255),
                    ])->defaultItems(0)->addActionLabel('Thêm giải pháp'),
                    TextInput::make('details.solution.caption')->label('Chú thích ảnh')->maxLength(255),
                    TextInput::make('details.solution.cta_label')->label('Nút liên hệ')->maxLength(100)->default('Liên hệ tư vấn giải pháp'),
                ]),
                Section::make('Hạng mục thi công')->schema([
                    ...self::headings('scope', 'Hạng mục thi công', 'Các hạng mục chính'),
                    self::cards('scope', 'Các hạng mục'),
                ]),
                Section::make('Ảnh thi công')->schema([
                    ...self::headings('construction', 'Hình ảnh thực tế', 'Một số hình ảnh trong quá trình thi công'),
                ]),
                Section::make('Kết quả')->schema([
                    ...self::headings('results', 'Kết quả đạt được', 'Hiệu quả sau bàn giao'),
                    Textarea::make('details.results.description')->label('Mô tả')->rows(3),
                    self::cards('results', 'Các kết quả'),
                ]),
                Section::make('Nhận xét chủ đầu tư')->schema([
                    Textarea::make('details.testimonial.quote')->label('Nhận xét')->rows(4),
                    TextInput::make('details.testimonial.name')->label('Người đánh giá')->maxLength(255),
                    TextInput::make('details.testimonial.role')->label('Chức vụ / đơn vị')->maxLength(255),
                ]),
                Section::make('Dự án liên quan')->schema([
                    ...self::headings('related', 'Dự án liên quan', 'Có thể bạn quan tâm'),
                    Select::make('details.related.ids')->label('Chọn dự án')->multiple()->searchable()->preload()->maxItems(4)
                        ->options(fn (?Project $record): array => Project::published()
                            ->when($record, fn ($query) => $query->whereKeyNot($record->id))
                            ->orderBy('title')->pluck('title', 'id')->all()),
                ]),
                Section::make('SEO')->schema(SeoFields::make())->columns(1),
            ])->columnSpan(['lg' => 2]),
            Group::make([
                Section::make('Phân loại & hiển thị')->schema([
                    Select::make('project_category_id')->label('Danh mục')->relationship('category', 'name')->searchable()->preload(),
                    Select::make('tags')->label('Thẻ')->relationship('tags', 'name')->multiple()->searchable()->preload(),
                    Select::make('status')->label('Trạng thái')->options(['draft' => 'Bản nháp', 'published' => 'Đã xuất bản', 'pending' => 'Chờ duyệt', 'private' => 'Riêng tư'])->required()->default('draft'),
                    DatePicker::make('published_at')->label('Ngày xuất bản'),
                    TextInput::make('sort_order')->label('Thứ tự')->numeric()->integer()->minValue(0)->default(fn (): int => ((int) Project::max('sort_order')) + 1),
                    Toggle::make('is_featured')->label('Dự án nổi bật'),
                ]),
                Section::make('Ảnh đại diện & banner')->schema([
                    self::image('curator_media_id', 'Ảnh đại diện')->relationship('curatorMedia', 'id'),
                    self::image('details.hero.banner_media_id', 'Banner chi tiết'),
                ]),
                Section::make('Gallery tổng quan')->schema([
                    self::image('gallery', 'Ảnh tổng quan')->multiple(),
                ]),
                Section::make('Hình ảnh thi công')->schema([
                    self::image('details.construction.images', 'Ảnh thi công')->multiple(),
                ]),
                Section::make('Ảnh giải pháp')->schema([
                    self::image('details.solution.media_id', 'Ảnh giải pháp'),
                ]),
                Section::make('Ảnh nhận xét')->schema([
                    self::image('details.testimonial.avatar_media_id', 'Chân dung'),
                    self::image('details.testimonial.background_media_id', 'Ảnh nền'),
                ]),
            ])->columnSpan(['lg' => 1]),
        ]);
    }

    private static function headings(string $section, string $label, string $title): array
    {
        return [
            TextInput::make("details.{$section}.label")->label('Nhãn')->maxLength(150)->default($label),
            TextInput::make("details.{$section}.title")->label('Tiêu đề')->maxLength(255)->default($title),
        ];
    }

    private static function image(string $name, string $label): CuratorPicker
    {
        return CuratorPicker::make($name)->label($label)->disk('public')->constrained()->acceptedFileTypes(['image/*']);
    }

    private static function cards(string $section, string $label): Repeater
    {
        return Repeater::make("details.{$section}.items")->label($label)->schema([
            TextInput::make('title')->label('Tiêu đề')->required()->maxLength(255),
            Textarea::make('description')->label('Mô tả')->rows(2),
            Select::make('icon')->label('Icon')->options(ProjectDetailContent::ICONS)->searchable()->nullable(),
            self::image('media_id', 'Ảnh thay icon'),
        ])->defaultItems(0)->addActionLabel('Thêm mục')->collapsible()
            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null);
    }
}
