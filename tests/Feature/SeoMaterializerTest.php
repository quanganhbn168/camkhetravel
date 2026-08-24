<?php

namespace Tests\Feature;

use App\Models\ContentItem;
use App\Support\Seo\SeoMaterializer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SeoMaterializerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_dry_run_is_read_only_and_materialized_seo_is_idempotent_and_domain_neutral(): void
    {
        if (! Schema::hasColumn('content_items', 'effective_seo_hash')) {
            $this->markTestSkipped(
                'Run the pending local-media/effective-SEO migration before this test.',
            );
        }

        config()->set('app.url', 'https://deployment-specific.example.vn');

        $query = ContentItem::query()
            ->where('source', 'wordpress')
            ->where('status', 'published')
            ->whereNotNull('canonical_path');
        $beforeHashes = (clone $query)
            ->orderBy('id')
            ->pluck('effective_seo_hash', 'id')
            ->all();

        $dryRun = app(SeoMaterializer::class)->materialize(dryRun: true);

        $this->assertGreaterThan(0, $dryRun['targeted']);
        $this->assertSame(
            $dryRun['targeted'],
            $dryRun['changed'] + $dryRun['unchanged'],
        );
        $this->assertSame(
            $beforeHashes,
            (clone $query)->orderBy('id')->pluck('effective_seo_hash', 'id')->all(),
            'A materializer dry-run must not update persisted SEO hashes.',
        );

        $firstRun = app(SeoMaterializer::class)->materialize();
        $secondRun = app(SeoMaterializer::class)->materialize();

        $this->assertSame($firstRun['targeted'], $secondRun['targeted']);
        $this->assertSame(0, $secondRun['changed']);
        $this->assertSame($secondRun['targeted'], $secondRun['unchanged']);

        (clone $query)
            ->select(['id', 'effective_seo'])
            ->chunkById(100, function ($items): void {
                foreach ($items as $item) {
                    $seo = $item->effective_seo;

                    $this->assertIsArray($seo);
                    $this->assertNotEmpty($seo['title'] ?? null);
                    $this->assertNotEmpty($seo['description'] ?? null);

                    foreach (['og_image', 'twitter_image'] as $field) {
                        $url = $seo[$field] ?? null;

                        if ($url === null) {
                            continue;
                        }

                        $this->assertStringStartsWith('/', $url);
                        $this->assertStringNotContainsString('://', $url);
                        $this->assertStringNotContainsString(
                            'deployment-specific.example.vn',
                            $url,
                        );
                    }
                }
            });
    }
}
