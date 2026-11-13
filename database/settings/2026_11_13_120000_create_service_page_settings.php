<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

/** Mẫu chung cho trang chi tiết dịch vụ (Vì sao chọn, Quy trình đặt xe, Các dòng xe phục vụ). */
return new class extends SettingsMigration
{
    public function up(): void
    {
        $defaults = [
            'benefit_title' => 'Vì sao chọn {dich_vu}?',
            'benefit_description' => 'Đặt xe thuận tiện, lịch trình linh hoạt và chi phí rõ ràng trước khi khởi hành.',
            'benefit_items' => [
                ['title' => 'Đón khách tận nơi', 'description' => 'Tiếp nhận đặt xe và đón tại điểm hẹn đã trao đổi.'],
                ['title' => 'Đặt xe linh hoạt theo yêu cầu', 'description' => 'Chủ động thời gian, điểm đón và điểm đến theo nhu cầu.'],
                ['title' => 'Đa dạng hình thức di chuyển', 'description' => 'Ghép xe tiết kiệm hoặc bao xe riêng tùy lịch trình.'],
                ['title' => 'Báo giá rõ ràng', 'description' => 'Tư vấn chi phí trước khi xác nhận chuyến đi.'],
                ['title' => 'Hỗ trợ đặt xe thuận tiện', 'description' => 'Liên hệ nhanh qua điện thoại hoặc Zalo.'],
            ],
            'process_title' => 'Quy trình đặt xe',
            'process_description' => 'Chỉ với 4 bước đơn giản để đặt xe và xác nhận chuyến đi.',
            'process_items' => [
                ['title' => 'Liên hệ đặt xe', 'description' => 'Gọi điện hoặc nhắn Zalo gửi yêu cầu.'],
                ['title' => 'Cung cấp lịch trình', 'description' => 'Điểm đón, điểm đến, thời gian và số lượng khách.'],
                ['title' => 'Nhận báo giá', 'description' => 'Tư vấn phương án di chuyển và chi phí.'],
                ['title' => 'Xác nhận chuyến đi', 'description' => 'Thống nhất thông tin và thời gian đón khách.'],
            ],
            'vehicle_title' => 'Các dòng xe phục vụ',
            'vehicle_description' => 'Lựa chọn phương tiện phù hợp với số lượng hành khách và nhu cầu di chuyển.',
            'vehicle_items' => [
                ['title' => 'Xe 4 chỗ', 'seats' => '4 chỗ', 'description' => 'Nhỏ gọn, tiện lợi cho cá nhân và nhóm nhỏ.', 'media_id' => null],
                ['title' => 'Xe 5 chỗ', 'seats' => '5 chỗ', 'description' => 'Phổ biến, linh hoạt cho gia đình.', 'media_id' => null],
                ['title' => 'Xe 7 chỗ', 'seats' => '7 chỗ', 'description' => 'Rộng rãi, thoải mái cho nhóm đông hoặc nhiều hành lý.', 'media_id' => null],
            ],
        ];

        foreach ($defaults as $name => $value) {
            if (! $this->migrator->exists('service_page.'.$name)) {
                $this->migrator->add('service_page.'.$name, $value);
            }
        }
    }
};
