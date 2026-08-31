<?php

namespace Tests\Feature;

use App\Models\BniChapter;
use App\Models\BniInvitation;
use App\Models\User;
use App\Support\Bni\BniInvitationBulkCreator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BniInvitationBulkCreateTest extends TestCase
{
    use DatabaseTransactions;

    public function test_chapter_manager_can_paste_300_named_guests_into_their_own_chapter(): void
    {
        Role::findOrCreate('bni_chapter_manager', 'web');
        $chapter = $this->importableChapter();
        $user = User::factory()->create(['bni_chapter_id' => $chapter->getKey()]);
        $user->assignRole('bni_chapter_manager');
        $this->actingAs($user);
        $guestNames = collect(range(1, 300))
            ->map(fn (int $number): string => "Khách dán hàng loạt {$number}")
            ->implode("\n");

        $count = app(BniInvitationBulkCreator::class)->create($guestNames, $chapter->getKey());

        $guests = BniInvitation::query()
            ->where('bni_chapter_id', $chapter->getKey())
            ->where('guest_name', 'like', 'Khách dán hàng loạt %')
            ->get();

        $this->assertSame(300, $count);
        $this->assertCount(300, $guests);
        $this->assertTrue($guests->every(fn (BniInvitation $guest): bool => $guest->bni_event_id === $chapter->bni_event_id));
        $this->assertTrue($guests->every(fn (BniInvitation $guest): bool => $guest->rsvp_status === BniInvitation::RSVP_PENDING));
        $this->assertCount(300, $guests->pluck('invitation_code')->unique());
    }

    public function test_blank_lines_are_ignored_without_deduplicating_real_guest_rows(): void
    {
        Role::findOrCreate('bni_chapter_manager', 'web');
        $chapter = $this->importableChapter();
        $user = User::factory()->create(['bni_chapter_id' => $chapter->getKey()]);
        $user->assignRole('bni_chapter_manager');
        $this->actingAs($user);

        $count = app(BniInvitationBulkCreator::class)->create(
            "  Nguyễn Văn An  \n\nTrần Thu Hà\r\nNguyễn Văn An",
            $chapter->getKey(),
        );

        $this->assertSame(3, $count);
        $this->assertSame(2, BniInvitation::query()
            ->where('bni_chapter_id', $chapter->getKey())
            ->where('guest_name', 'Nguyễn Văn An')
            ->count());
    }

    public function test_chapter_manager_cannot_tamper_with_the_bulk_target_chapter(): void
    {
        Role::findOrCreate('bni_chapter_manager', 'web');
        $chapter = $this->importableChapter();
        $otherChapter = BniChapter::query()
            ->whereKeyNot($chapter->getKey())
            ->where('is_active', true)
            ->whereNotNull('bni_event_id')
            ->firstOrFail();
        $user = User::factory()->create(['bni_chapter_id' => $chapter->getKey()]);
        $user->assignRole('bni_chapter_manager');
        $this->actingAs($user);

        try {
            app(BniInvitationBulkCreator::class)->create('Khách sai Chapter', $otherChapter->getKey());
            $this->fail('Tạo hàng loạt phải từ chối Chapter không thuộc tài khoản.');
        } catch (ValidationException $exception) {
            $this->assertSame(
                'Chapter không được nhập khách mời cho Chapter khác.',
                $exception->errors()['bni_chapter_id'][0],
            );
        }

        $this->assertDatabaseMissing('bni_invitations', [
            'guest_name' => 'Khách sai Chapter',
        ]);
    }

    public function test_chapter_manager_sees_the_bulk_paste_action(): void
    {
        Role::findOrCreate('bni_chapter_manager', 'web');
        $chapter = $this->importableChapter();
        $user = User::factory()->create(['bni_chapter_id' => $chapter->getKey()]);
        $user->assignRole('bni_chapter_manager');

        $this->actingAs($user)
            ->get('/bni-admin/bni-invitations')
            ->assertOk()
            ->assertSee('Dán danh sách khách mời');
    }

    private function importableChapter(): BniChapter
    {
        return BniChapter::query()
            ->where('is_active', true)
            ->whereNotNull('bni_event_id')
            ->firstOrFail();
    }
}
