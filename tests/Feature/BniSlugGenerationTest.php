<?php

namespace Tests\Feature;

use App\Models\BniArticle;
use App\Models\BniArticleCategory;
use App\Models\BniChapter;
use App\Models\BniEvent;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class BniSlugGenerationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_bni_slugs_are_generated_automatically_and_remain_stable_when_names_change(): void
    {
        $token = Str::lower(Str::random(10));
        $eventTitle = 'Sự kiện kết nối '.$token;
        $expectedEventSlug = Str::slug($eventTitle);

        $event = BniEvent::query()->create([
            'title' => $eventTitle,
            'type' => 'handover',
            'status' => 'draft',
        ]);
        $duplicateEvent = BniEvent::query()->create([
            'title' => $eventTitle,
            'type' => 'handover',
            'status' => 'draft',
        ]);
        $chapter = BniChapter::query()->create(['name' => 'Chapter '.$token]);
        $article = BniArticle::query()->create(['title' => 'Tin tức '.$token]);
        $category = BniArticleCategory::query()->create(['name' => 'Danh mục '.$token]);

        $this->assertSame($expectedEventSlug, $event->slug);
        $this->assertSame($expectedEventSlug.'-2', $duplicateEvent->slug);
        $this->assertSame(Str::slug('Chapter '.$token), $chapter->slug);
        $this->assertSame(Str::slug('Tin tức '.$token), $article->slug);
        $this->assertSame(Str::slug('Danh mục '.$token), $category->slug);

        $event->update(['title' => 'Tên sự kiện đã đổi '.$token]);

        $this->assertSame($expectedEventSlug, $event->fresh()->slug);
    }
}
