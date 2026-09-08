<?php

namespace Tests\Feature;

use App\Models\BniEvent;
use App\Models\MenuItem;
use Database\Seeders\EventsNavigationSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EventsPageTest extends TestCase
{
    use DatabaseTransactions;

    public function test_the_event_list_belongs_to_the_handover_ecosystem_and_uses_published_content(): void
    {
        $published = BniEvent::create(['title' => 'Chương trình kết nối thử nghiệm', 'type' => 'community', 'status' => 'published', 'summary' => 'Nội dung từ quản trị sự kiện', 'starts_at' => now()->addMonth(), 'venue' => 'Địa điểm trong CMS']);
        $draft = BniEvent::create(['title' => 'Bản nháp sự kiện riêng', 'type' => 'community', 'status' => 'draft']);

        $this->get(route('bni.events.index'))->assertOk()
            ->assertSee('Điều hướng Lễ chuyển giao')
            ->assertSee($published->title)->assertSee($published->summary)->assertSee($published->venue)
            ->assertSee(route('bni.events.show', $published->slug), false)
            ->assertSee(route('bni.handover'), false)->assertSee(route('bni.pickleball'), false)
            ->assertDontSee($draft->title);
        $this->get('/su-kien')->assertRedirect(route('bni.events.index'));
        $this->get(route('bni.events.show', $draft->slug))->assertNotFound();
        $this->get('/sitemap.xml')->assertOk()
            ->assertSee(route('bni.events.index'), false)
            ->assertSee(route('bni.events.show', $published->slug), false)
            ->assertDontSee(route('bni.events.show', $draft->slug), false);
    }

    public function test_event_filters_include_ongoing_events_and_exclude_drafts(): void
    {
        $ongoing = BniEvent::create(['title' => 'Đang diễn ra theo lịch quản trị', 'type' => 'community', 'status' => 'published', 'starts_at' => now()->subDay(), 'ends_at' => now()->addDay()]);
        $past = BniEvent::create(['title' => 'Chương trình đã kết thúc', 'type' => 'community', 'status' => 'published', 'starts_at' => now()->subDays(3), 'ends_at' => now()->subDays(2)]);

        $this->get(route('bni.events.index', ['trang-thai' => 'sap-dien-ra']))->assertOk()->assertSee($ongoing->title)->assertDontSee($past->title);
        $this->get(route('bni.events.index', ['trang-thai' => 'da-dien-ra']))->assertOk()->assertSee($past->title)->assertDontSee($ongoing->title);
        $this->get(route('bni.events.index', ['trang-thai' => 'invalid']))->assertNotFound();
    }

    public function test_older_events_keep_their_own_details_and_hidden_schedule_days_stay_hidden(): void
    {
        $event = BniEvent::create(['title' => 'Lễ chuyển giao nhiệm kỳ trước', 'type' => 'handover', 'status' => 'published', 'is_featured' => false, 'starts_at' => '2001-01-01', 'content' => '<p>Nội dung nhiệm kỳ trước</p>']);
        $event->scheduleDays()->create(['title' => 'Lịch trình chưa công bố', 'event_date' => '2001-01-01', 'is_active' => false]);

        $this->get(route('bni.events.show', $event->slug))->assertOk()->assertSee($event->title)->assertSee('Nội dung nhiệm kỳ trước')->assertDontSee('Lịch trình chưa công bố');
        $current = BniEvent::published()->where('type', 'handover')->orderByDesc('is_featured')->orderByDesc('starts_at')->firstOrFail();
        $this->get(route('bni.events.show', $current->slug))->assertRedirect(route('bni.handover'));
    }

    public function test_the_bni_entry_stays_in_the_main_menu_and_seeding_does_not_duplicate_it(): void
    {
        $this->seed(EventsNavigationSeeder::class);
        $before = MenuItem::count();
        $this->seed(EventsNavigationSeeder::class);
        $this->assertSame($before, MenuItem::count());
        $parent = MenuItem::where('url', 'bni.handover')->firstOrFail();
        $this->assertNull($parent->parent_id);
        $this->assertSame(0, $parent->children()->count());
        $this->get(route('bni.events.index'))->assertOk()->assertSee('Lễ chuyển giao BNI')->assertSee('site-header__phones')->assertDontSee('bni-handover-menu-link');
        $news = $this->get(route('bni.articles.index'))->assertOk()->getContent();
        $this->assertMatchesRegularExpression('~href="'.preg_quote(route('bni.handover'), '~').'"[^>]*aria-current="page"~', $news);
    }
}
