<?php

namespace Tests\Feature;

use App\Support\Bni\BniMediaService;
use Illuminate\Support\Facades\File;
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
}
