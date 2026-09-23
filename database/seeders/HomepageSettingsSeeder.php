<?php

namespace Database\Seeders;

use App\Settings\HomepageSettings;
use Illuminate\Database\Seeder;

final class HomepageSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = new HomepageSettings([
            'about_title' => 'CamKheTravel đồng hành cùng hành trình của bạn',
            'about_content' => 'Dịch vụ xe du lịch, xe hợp đồng và hỗ trợ lịch trình theo nhu cầu của từng chuyến đi.',
            'stats' => [],
            'commitments' => "Báo giá rõ ràng\nĐúng xe đã xác nhận\nChủ động lịch trình\nĐồng hành chuyến đi",
            'capabilities' => "Bao xe đi tỉnh\nBao xe du lịch\nXe hợp đồng\nXe ghép theo lịch",
            'consultation_title' => 'Bạn cần phương tiện cho chuyến đi sắp tới?',
            'consultation_content' => 'Gửi lịch trình để CamKheTravel tư vấn phương án phù hợp.',
            'faq_title' => 'Câu hỏi thường gặp',
            'faq_description' => '',
            'fleet_types' => [
                ['code' => 'sedan', 'title' => 'Xe 4–5 chỗ', 'features' => "Nhỏ gọn, riêng tư\nCông tác, đi tỉnh, xe dâu"],
                ['code' => 'mpv', 'title' => 'Xe 7 chỗ', 'features' => "Rộng rãi, linh hoạt\nGia đình và nhóm bạn"],
                ['code' => 'van', 'title' => 'Xe 16 chỗ', 'features' => "Thoải mái cho cả đoàn\nNhóm gia đình, đối tác tour"],
            ],
            'tour_types' => [
                ['title' => 'Tour gia đình', 'description' => 'Riêng tư, gắn kết người thân'],
                ['title' => 'Tour riêng', 'description' => 'Theo chương trình của đối tác'],
                ['title' => 'Tour công ty', 'description' => 'Xe cho nhóm và đoàn nhỏ'],
                ['title' => 'Hành trình theo mùa', 'description' => 'Du xuân, nghỉ hè, khám phá'],
            ],
            'partner_benefits' => [
                ['title' => 'Báo giá cho đối tác', 'description' => 'Theo lịch trình và hợp đồng'],
                ['title' => 'Xe theo quy mô đoàn', 'description' => 'Tư vấn nhóm xe theo số khách'],
                ['title' => 'Đầu mối điều phối', 'description' => 'Trao đổi xuyên suốt chuyến đi'],
                ['title' => 'Linh hoạt lịch trình', 'description' => 'Theo tour, theo đoàn riêng'],
                ['title' => 'Hợp tác dài hạn', 'description' => 'Đồng hành cùng phát triển'],
            ],
            'partner_steps' => [
                ['title' => 'Gửi lịch trình', 'description' => 'Tuyến đi, ngày đi, số khách.'],
                ['title' => 'Nhận phương án', 'description' => 'Thống nhất loại xe, chi phí.'],
                ['title' => 'Xác nhận hợp tác', 'description' => 'Chốt lịch và các điều khoản.'],
                ['title' => 'Triển khai', 'description' => 'Bố trí xe, tài xế, điều phối.'],
            ],
            'commitment_items' => [
                ['title' => 'Báo giá rõ ràng', 'description' => 'Thống nhất trước chuyến đi'],
                ['title' => 'Đúng xe đã xác nhận', 'description' => 'Phù hợp số người, hành lý'],
                ['title' => 'Chủ động lịch trình', 'description' => 'Trao đổi thời gian đón trả'],
                ['title' => 'Đồng hành chuyến đi', 'description' => 'Có đầu mối liên hệ hỗ trợ'],
            ],
        ]);
        $settings->settingsConfig()->resetDefaultValueLoadedProperties();
        $settings->save();
    }
}
