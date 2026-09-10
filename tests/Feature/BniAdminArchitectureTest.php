<?php

namespace Tests\Feature;

use App\Support\Bni\BniMediaService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BniAdminArchitectureTest extends TestCase
{
    public function test_bni_resources_do_not_use_curator_or_modal_crud_pages(): void
    {
        $files = File::allFiles(app_path('Filament/Bni/Resources'));
        $source = collect($files)->map(fn ($file): string => File::get($file->getPathname()))->implode("\n");

        $this->assertStringNotContainsString('Awcodes\\Curator', $source);
        $this->assertStringNotContainsString('CuratorPicker', $source);
        $this->assertStringNotContainsString('CuratorColumn', $source);
        $this->assertStringNotContainsString('extends ManageRecords', $source);
        $this->assertStringContainsString('extends CreateBniRecord', $source);
        $this->assertStringContainsString('extends EditBniRecord', $source);
        $this->assertStringContainsString('->slideOver()', $source);
        $this->assertStringNotContainsString("TextInput::make('slug')", $source);
        $this->assertSame(100, BniMediaService::WEBP_QUALITY);
    }

    public function test_event_content_is_split_into_relational_resources_and_schedule_days(): void
    {
        $eventResource = File::get(app_path('Filament/Bni/Resources/BniEvents/BniEventResource.php'));
        $chapterResource = File::get(app_path('Filament/Bni/Resources/BniChapters/BniChapterResource.php'));
        $sponsorResource = File::get(app_path('Filament/Bni/Resources/BniSponsors/BniSponsorResource.php'));
        $scheduleResource = File::get(app_path('Filament/Bni/Resources/BniScheduleDays/BniScheduleDayResource.php'));
        $pickleballScheduleResource = File::get(app_path('Filament/Bni/Resources/BniPickleballScheduleDays/BniPickleballScheduleDayResource.php'));

        $this->assertStringNotContainsString('Repeater::make', $eventResource);
        $this->assertStringNotContainsString('settings.', $eventResource);
        $this->assertStringNotContainsString("TextInput::make('stage')", $scheduleResource);
        $this->assertStringNotContainsString("Textarea::make('result')", $scheduleResource);
        $this->assertStringNotContainsString("TextInput::make('location')", $scheduleResource);
        $this->assertStringContainsString("\$navigationLabel = 'Lịch trình sự kiện'", $pickleballScheduleResource);
        $this->assertStringNotContainsString('Lịch thi đấu', $pickleballScheduleResource);

        foreach (['bni_event_videos', 'bni_event_landings', 'bni_event_prizes', 'bni_schedule_days', 'bni_contacts', 'bni_sponsors'] as $table) {
            $this->assertTrue(Schema::hasTable($table), $table);
        }

        foreach (['bni_event_id', 'day_number', 'stage', 'result', 'location'] as $column) {
            $this->assertFalse(Schema::hasColumn('bni_schedule_items', $column), $column);
        }

        $invitationSettings = File::get(app_path('Filament/Bni/Pages/ManageBniInvitationSettings.php'));
        $galleryResource = File::get(app_path('Filament/Bni/Resources/BniGalleryItems/BniGalleryItemResource.php'));
        $slideResource = File::get(app_path('Filament/Bni/Resources/BniEventSlides/BniEventSlideResource.php'));

        $this->assertStringNotContainsString("TextInput::make('schedule_title')", $invitationSettings);
        $this->assertStringContainsString('Thông báo chung: thời gian & địa điểm', $eventResource);
        $this->assertStringContainsString("SpatieMediaLibraryFileUpload::make('activity_image')", $eventResource);
        $this->assertStringContainsString("->imageAspectRatio('1:1')", $eventResource);
        $this->assertStringContainsString('class BniSponsorResource', $sponsorResource);
        $this->assertStringContainsString("SpatieMediaLibraryFileUpload::make('logo')", $sponsorResource);
        $this->assertStringContainsString('BniSponsor::tierOptions()', $sponsorResource);
        $this->assertStringContainsString("->reorderable('sort_order')", $chapterResource);
        $this->assertStringContainsString("->label('Ảnh cover / poster video')", $chapterResource);
        $this->assertStringContainsString('khung video lớn trên /le-chuyen-giao', $chapterResource);
        $this->assertStringContainsString("->label('Ảnh slide / ảnh cover video')", $slideResource);
        $this->assertStringContainsString('được dùng làm poster khi slide có video', $slideResource);

        foreach (['starts_at', 'ends_at', 'venue', 'address', 'directions_url', 'landing_url'] as $field) {
            $this->assertStringContainsString("::make('{$field}')", $eventResource, $field);
            $this->assertStringNotContainsString("::make('{$field}')", $invitationSettings, $field);
        }

        $this->assertStringContainsString('Stack::make', $galleryResource);
        $this->assertStringContainsString('->contentGrid([', $galleryResource);
        $this->assertStringNotContainsString("Select::make('bni_event_id')", $slideResource);
        $this->assertStringNotContainsString("Textarea::make('description')", $slideResource);
        $this->assertStringNotContainsString("TextInput::make('button_label')", $slideResource);
        $this->assertStringNotContainsString("TextInput::make('button_url')", $slideResource);
        $this->assertStringNotContainsString("TextColumn::make('event.title')", $slideResource);
        $this->assertStringNotContainsString("SelectFilter::make('bni_event_id')", $slideResource);
        $this->assertStringContainsString('$data[\'bni_event_id\'] = $event->getKey();', $slideResource);
    }
}
