<?php

namespace App\Support\Bni;

use App\Models\BniChapter;
use App\Models\BniInvitation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BniInvitationBulkCreator
{
    public const MAX_GUESTS = 1000;

    public function create(string $guestNames, mixed $requestedChapterId): int
    {
        $names = $this->parseNames($guestNames);
        $chapter = $this->resolveChapter($requestedChapterId);

        DB::transaction(function () use ($chapter, $names): void {
            foreach ($names as $name) {
                BniInvitation::query()->create([
                    'bni_event_id' => $chapter->bni_event_id,
                    'bni_chapter_id' => $chapter->getKey(),
                    'guest_name' => $name,
                    'rsvp_status' => BniInvitation::RSVP_PENDING,
                    'guest_count' => 1,
                ]);
            }
        });

        return count($names);
    }

    /** @return list<string> */
    private function parseNames(string $guestNames): array
    {
        $names = collect(preg_split('/\R/u', $guestNames) ?: [])
            ->map(fn (string $name): string => trim($name))
            ->filter(fn (string $name): bool => filled($name))
            ->values();

        if ($names->isEmpty()) {
            $this->fail('guest_names', 'Vui lòng nhập ít nhất một tên khách mời.');
        }

        if ($names->count() > self::MAX_GUESTS) {
            $this->fail('guest_names', 'Mỗi lần chỉ nhập tối đa '.self::MAX_GUESTS.' khách mời.');
        }

        foreach ($names as $index => $name) {
            if (mb_strlen($name) <= 255) {
                continue;
            }

            $this->fail('guest_names', 'Tên khách mời ở dòng '.($index + 1).' dài quá 255 ký tự.');
        }

        return $names->all();
    }

    private function resolveChapter(mixed $requestedChapterId): BniChapter
    {
        $user = BniPanelAccess::user();

        if (! $user instanceof User) {
            $this->fail('bni_chapter_id', 'Không xác định được tài khoản đang nhập danh sách.');
        }

        $requestedChapterId = filled($requestedChapterId) ? (int) $requestedChapterId : null;

        if ($user->hasAnyRole(['super_admin', 'bni_admin'])) {
            $chapterId = $requestedChapterId;
        } elseif ($user->hasRole('bni_chapter_manager')) {
            $chapterId = $user->bni_chapter_id;

            if ($requestedChapterId !== null && $requestedChapterId !== $chapterId) {
                $this->fail('bni_chapter_id', 'Chapter không được nhập khách mời cho Chapter khác.');
            }
        } else {
            $this->fail('bni_chapter_id', 'Tài khoản không có quyền nhập khách mời.');
        }

        if ($chapterId === null) {
            $this->fail('bni_chapter_id', 'Vui lòng chọn Chapter nhận danh sách khách mời.');
        }

        $chapter = BniChapter::query()
            ->whereKey($chapterId)
            ->where('is_active', true)
            ->whereNotNull('bni_event_id')
            ->first();

        if (! $chapter) {
            $this->fail('bni_chapter_id', 'Chapter chưa hoạt động hoặc chưa được gắn với sự kiện.');
        }

        return $chapter;
    }

    private function fail(string $field, string $message): never
    {
        throw ValidationException::withMessages([
            $field => $message,
        ]);
    }
}
