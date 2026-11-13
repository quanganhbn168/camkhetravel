<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

/**
 * Mẫu chung cho trang chi tiết dịch vụ. Dịch vụ nào để trống phần tương ứng thì dùng mẫu này,
 * nên không phải dịch vụ nào cũng phải điền lại "Vì sao chọn", "Quy trình", "Các dòng xe".
 * Trong tiêu đề/mô tả có thể dùng {dich_vu} để chèn tên dịch vụ.
 */
class ServicePageSettings extends Settings
{
    public const SERVICE_PLACEHOLDER = '{dich_vu}';

    public string $benefit_title = '';

    public string $benefit_description = '';

    public array $benefit_items = [];

    public string $process_title = '';

    public string $process_description = '';

    public array $process_items = [];

    public string $vehicle_title = '';

    public string $vehicle_description = '';

    public array $vehicle_items = [];

    public static function group(): string
    {
        return 'service_page';
    }

    public static function withService(?string $text, string $serviceTitle): string
    {
        return trim(str_replace(self::SERVICE_PLACEHOLDER, $serviceTitle, (string) $text));
    }
}
