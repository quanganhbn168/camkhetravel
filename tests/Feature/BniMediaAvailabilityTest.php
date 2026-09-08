<?php

namespace Tests\Feature;

use App\Models\BniEvent;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BniMediaAvailabilityTest extends TestCase
{
    use DatabaseTransactions;

    public function test_missing_conversions_use_the_original_and_missing_files_do_not_render_broken_urls(): void
    {
        Storage::fake('public');
        $event = BniEvent::create(['title' => 'Kiểm tra ảnh sự kiện', 'status' => 'draft']);
        $media = $event->addMedia(UploadedFile::fake()->image('event.jpg', 640, 360))->toMediaCollection('hero', 'public');

        $this->assertStringStartsWith($media->getUrl('webp'), $event->bniMediaUrl('hero'));
        Storage::disk('public')->delete($media->getPathRelativeToRoot('webp'));
        $this->assertStringStartsWith($media->getUrl(), $event->bniMediaUrl('hero'));
        Storage::disk('public')->delete($media->getPathRelativeToRoot());
        $this->assertNull($event->bniMediaUrl('hero'));
        $this->assertDatabaseHas('media', ['id' => $media->id]);
    }
}
