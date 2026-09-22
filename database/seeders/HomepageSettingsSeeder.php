<?php

namespace Database\Seeders;

use App\Settings\HomepageSettings;
use Illuminate\Database\Seeder;

final class HomepageSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = new HomepageSettings([
            'about_title' => 'Giải pháp phù hợp cho từng công trình',
            'about_content' => 'Cập nhật nội dung giới thiệu doanh nghiệp tại trang quản trị.',
            'stats' => [],
            'commitments' => "Khảo sát đúng hiện trạng\nThiết kế theo tiêu chuẩn\nBàn giao rõ ràng và đồng hành dài hạn",
            'capabilities' => "Tư vấn & khảo sát\nThiết kế hệ thống PCCC\nThi công - lắp đặt\nBảo trì - bảo dưỡng",
            'consultation_title' => 'Cần tư vấn giải pháp PCCC cho công trình?',
            'consultation_content' => 'Gửi thông tin công trình để nhận phương án phù hợp.',
            'faq_title' => 'Câu hỏi thường gặp',
            'faq_description' => 'Thông tin cần biết trước khi bắt đầu triển khai hệ thống PCCC.',
        ]);
        $settings->settingsConfig()->resetDefaultValueLoadedProperties();
        $settings->save();
    }
}
