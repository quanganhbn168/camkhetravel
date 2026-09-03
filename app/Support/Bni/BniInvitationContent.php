<?php

namespace App\Support\Bni;

use App\Models\BniChapter;
use App\Models\BniContact;
use App\Models\BniEvent;
use App\Settings\BniInvitationSettings;
use Illuminate\Support\Collection;

final class BniInvitationContent
{
    /** @return array<string, string> */
    public static function defaults(): array
    {
        return [
            'label' => 'THƯ MỜI',
            'event_label' => 'LỄ CHUYỂN GIAO',
            'greeting' => 'Trân trọng kính mời',
            'default_guest_name' => 'Anh/Chị chủ doanh nghiệp',
            'content_title' => 'Nội dung chương trình',
            'content' => '<p>Ban tổ chức trân trọng kính mời anh/chị tham dự và cùng lan tỏa những giá trị kết nối trong sự kiện Lễ chuyển giao.</p>',
            'schedule_title' => 'Lịch trình sự kiện',
            'note_title' => 'Lưu ý tham dự',
            'note_content' => '<ul><li>Dress code: Trang phục lịch sự, phù hợp không khí sự kiện.</li><li>Vui lòng có mặt trước giờ bắt đầu để hoàn tất check-in.</li></ul>',
            'rsvp_title' => 'Xác nhận tham dự',
            'rsvp_description' => 'Anh/chị vui lòng xác nhận thông tin tham dự để Ban tổ chức chuẩn bị đón tiếp chu đáo.',
            'contact_title' => 'Liên hệ tham dự',
            'contact_description' => 'Nếu cần hỗ trợ thêm về lịch trình hoặc địa điểm, anh/chị vui lòng liên hệ đầu mối của chapter.',
        ];
    }

    /** @return array<string, mixed> */
    public static function resolve(?BniChapter $chapter = null, ?BniEvent $event = null): array
    {
        $defaults = self::defaults();
        $settings = app(BniInvitationSettings::class);
        $content = [];

        foreach (array_keys($defaults) as $key) {
            $content[$key] = $key === 'schedule_title'
                ? $defaults[$key]
                : (filled($settings->{$key} ?? null)
                ? $settings->{$key}
                : $defaults[$key]);
        }

        $event ??= $chapter?->event;
        $contacts = self::contacts($chapter, $event);
        $primary = $contacts->first();

        return $content + [
            'contacts' => $contacts,
            'contact_name' => $primary['name'] ?? null,
            'contact_email' => $primary['email'] ?? null,
            'contact_phone' => $primary['phone'] ?? null,
        ];
    }

    /** @return Collection<int, array<string, ?string>> */
    private static function contacts(?BniChapter $chapter, ?BniEvent $event): Collection
    {
        $contacts = $chapter?->contacts()->active()->get() ?? collect();

        if ($contacts->isEmpty() && $event) {
            $contacts = $event->contacts()->active()->get();
        }

        if ($contacts->isEmpty()) {
            $contacts = BniContact::query()
                ->general()
                ->active()
                ->whereNull('bni_event_id')
                ->orderByDesc('is_primary')
                ->orderBy('sort_order')
                ->get();
        }

        return $contacts->map(fn (BniContact $contact): array => [
            'name' => $contact->name,
            'position' => $contact->position,
            'phone' => $contact->phone,
            'phone_url' => filled($contact->phone) ? 'tel:'.preg_replace('/[^0-9+]/', '', $contact->phone) : null,
            'email' => $contact->email,
            'email_url' => filled($contact->email) ? 'mailto:'.$contact->email : null,
            'zalo_url' => $contact->zalo_url,
        ])->values();
    }
}
