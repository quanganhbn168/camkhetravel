<?php

namespace App\Support\Services;

/** Danh sách icon (FontAwesome, đã nạp sẵn toàn site) cho các thẻ trên trang dịch vụ. */
final class ServiceIcons
{
    /** khoá => [nhãn trong admin, class FontAwesome] */
    private const ICONS = [
        'users' => ['Nhóm người / ghép xe', 'fa-solid fa-users'],
        'car' => ['Xe ô tô', 'fa-solid fa-car-side'],
        'taxi' => ['Taxi', 'fa-solid fa-taxi'],
        'van' => ['Xe đông chỗ', 'fa-solid fa-van-shuttle'],
        'box' => ['Gửi hàng', 'fa-solid fa-box'],
        'plane' => ['Sân bay', 'fa-solid fa-plane-departure'],
        'hospital' => ['Bệnh viện', 'fa-solid fa-hospital'],
        'briefcase' => ['Công tác', 'fa-solid fa-briefcase'],
        'mountain' => ['Du lịch', 'fa-solid fa-mountain-sun'],
        'route' => ['Đường dài / đi tỉnh', 'fa-solid fa-route'],
        'location' => ['Đón tận nơi', 'fa-solid fa-location-dot'],
        'clock' => ['Đúng giờ / linh hoạt', 'fa-regular fa-clock'],
        'money' => ['Giá rõ ràng', 'fa-solid fa-coins'],
        'shield' => ['An toàn', 'fa-solid fa-shield-halved'],
        'headset' => ['Hỗ trợ', 'fa-solid fa-headset'],
        'star' => ['Chất lượng', 'fa-solid fa-star'],
        'heart' => ['Xe cưới', 'fa-solid fa-heart'],
        'calendar' => ['Đặt lịch hẹn', 'fa-regular fa-calendar-check'],
        'phone' => ['Gọi điện', 'fa-solid fa-phone'],
        'list' => ['Lịch trình', 'fa-solid fa-list-check'],
        'tag' => ['Báo giá', 'fa-solid fa-tag'],
        'check' => ['Xác nhận', 'fa-regular fa-circle-check'],
        'building' => ['Thành phố', 'fa-solid fa-city'],
    ];

    /** Hình thức của một dòng giá: khoá => [nhãn, class icon]. */
    private const PRICING_KINDS = [
        'private' => ['Bao xe riêng', 'fa-solid fa-car-side'],
        'shared' => ['Ghép khách', 'fa-solid fa-users'],
        'cargo' => ['Gửi hàng', 'fa-solid fa-box'],
        'other' => ['Khác', 'fa-solid fa-tag'],
    ];

    /** @return array<string, string> */
    public static function pricingKinds(): array
    {
        return array_map(fn (array $kind): string => $kind[0], self::PRICING_KINDS);
    }

    public static function pricingKindIcon(string $kind): string
    {
        return (self::PRICING_KINDS[$kind] ?? self::PRICING_KINDS['other'])[1];
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return array_map(fn (array $icon): string => $icon[0], self::ICONS);
    }

    /** Icon theo khoá; khoá trống/không hợp lệ thì lấy icon mặc định theo thứ tự trong danh sách $fallbacks. */
    public static function classFor(?string $key, array $fallbacks = [], int $index = 0): string
    {
        $key = isset(self::ICONS[$key ?? '']) ? $key : ($fallbacks === [] ? 'check' : $fallbacks[$index % count($fallbacks)]);

        return self::ICONS[$key][1];
    }
}
