<?php

namespace Tests\Feature;

use App\Models\ContentItem;
use App\Models\MediaAsset;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LocalizedMediaTest extends TestCase
{
    use DatabaseTransactions;

    public function test_all_imported_and_external_media_have_verified_local_records(): void
    {
        $assets = MediaAsset::query()
            ->whereIn('source', ['wordpress', 'external'])
            ->get();

        $this->assertCount(1_678, $assets);

        foreach ($assets as $asset) {
            $this->assertSame('localized', $asset->localization_status);
            $this->assertNotEmpty($asset->checksum_sha256);
            $this->assertNotEmpty($asset->file_path);
            $this->assertTrue(Storage::disk($asset->disk)->exists($asset->file_path));
        }
    }

    public function test_persisted_content_has_no_wordpress_upload_or_external_image_hotlinks(): void
    {
        foreach (ContentItem::query()->where('source', 'wordpress')->cursor() as $item) {
            $body = (string) $item->body;

            $this->assertStringNotContainsString(
                'thtmedia.com.vn/wp-content/uploads/',
                $body,
                "Content {$item->source_id} still uses WordPress uploads.",
            );

            preg_match_all('~<(?:img|source)\b[^>]*>~iu', $body, $tags);

            foreach ($tags[0] ?? [] as $tag) {
                $this->assertDoesNotMatchRegularExpression(
                    '~https?://~i',
                    html_entity_decode($tag, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                    "Content {$item->source_id} still hotlinks media.",
                );
            }
        }
    }
}
