<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\BniChapter;
use App\Models\BniEvent;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BniExperienceRoutesTest extends TestCase
{
    use DatabaseTransactions;

    public function test_the_bni_handover_experience_uses_the_shared_layout_main_and_public_sections(): void
    {
        $this->get(route('bni.handover'))
            ->assertOk()
            ->assertSee('id="bni-handover-main"', false)
            ->assertSee('Sự kiện chuyển giao')
            ->assertSee('Lịch trình sự kiện')
            ->assertSee('Những hoạt động đặc biệt')
            ->assertSee('Thư viện ảnh');
    }

    public function test_the_pickleball_landing_and_bni_panel_login_routes_are_available(): void
    {
        $this->get(route('bni.pickleball'))
            ->assertOk()
            ->assertSee('id="bni-pickleball-main"', false)
            ->assertSeeText('Lịch thi đấu & kết quả')
            ->assertSee('Đăng ký tham gia');

        $this->get('/bni-admin/login')->assertOk();
    }

    public function test_a_bni_administrator_can_open_the_dedicated_event_cms(): void
    {
        Role::findOrCreate('bni_admin', 'web');
        $user = User::factory()->create();
        $user->assignRole('bni_admin');

        $this->actingAs($user)
            ->get('/bni-admin/bni-events')
            ->assertOk()
            ->assertSee('Sự kiện');

        $this->actingAs($user)
            ->get('/bni-admin/bni-members')
            ->assertOk()
            ->assertSee('Hội viên');
    }

    public function test_a_chapter_manager_is_limited_to_chapter_workflows(): void
    {
        Role::findOrCreate('bni_chapter_manager', 'web');
        $event = BniEvent::query()->firstOrCreate(['slug' => 'test-bni-event'], ['title' => 'Sự kiện kiểm thử']);
        $chapter = BniChapter::query()->firstOrCreate(['slug' => 'test-bni-chapter'], [
            'bni_event_id' => $event->id,
            'name' => 'Test BNI Chapter',
        ]);
        $user = User::factory()->create(['bni_chapter_id' => $chapter->id]);
        $user->assignRole('bni_chapter_manager');

        $this->actingAs($user)->get('/bni-admin/bni-articles')->assertOk();
        $this->actingAs($user)->get('/bni-admin/bni-invitations')->assertOk();
        $this->actingAs($user)->get('/bni-admin/bni-registrations')->assertOk();
        $this->actingAs($user)->get('/bni-admin/bni-events')->assertForbidden();
    }
}
