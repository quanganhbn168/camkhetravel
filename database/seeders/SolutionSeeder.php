<?php

namespace Database\Seeders;

use App\Models\Solution;
use Illuminate\Database\Seeder;

final class SolutionSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'key' => 'factory',
                'name' => 'Nhà xưởng',
                'title' => 'Giải pháp PCCC nhà xưởng',
                'description' => 'Thiết kế đồng bộ theo đặc thù sản xuất, quy mô và mức độ rủi ro của từng nhà máy.',
                'items' => ['Báo cháy tự động', 'Chữa cháy Sprinkler', 'Cấp nước chữa cháy', 'Bơm và van', 'Thoát hiểm và chỉ dẫn an toàn'],
            ],
            [
                'key' => 'warehouse',
                'name' => 'Kho bãi',
                'title' => 'Giải pháp PCCC kho bãi',
                'description' => 'Tập trung phát hiện sớm, kiểm soát cháy lan và bảo vệ hàng hóa, tài sản.',
                'items' => ['Báo cháy tự động', 'Sprinkler chữa cháy', 'Họng nước chữa cháy', 'Bơm chữa cháy', 'Chiếu sáng và chỉ dẫn thoát nạn'],
            ],
            [
                'key' => 'office',
                'name' => 'Văn phòng',
                'title' => 'Giải pháp PCCC văn phòng',
                'description' => 'Đảm bảo an toàn, thẩm mỹ và phù hợp đặc thù vận hành của khối văn phòng.',
                'items' => ['Hệ thống báo cháy', 'Bình chữa cháy', 'Đèn exit và chiếu sáng sự cố', 'Họng nước vách tường', 'Phương án thoát nạn'],
            ],
            [
                'key' => 'hotel',
                'name' => 'Khách sạn',
                'title' => 'Giải pháp PCCC khách sạn',
                'description' => 'Tăng khả năng phát hiện sớm và đảm bảo an toàn cho khu vực lưu trú đông người.',
                'items' => ['Báo cháy địa chỉ', 'Sprinkler', 'Tăng áp và hút khói', 'Họng nước chữa cháy', 'Hệ thống thoát nạn'],
            ],
            [
                'key' => 'apartment',
                'name' => 'Chung cư',
                'title' => 'Giải pháp PCCC chung cư',
                'description' => 'Đồng bộ từ phát hiện cháy, chữa cháy đến thoát hiểm, chống khói và cứu nạn.',
                'items' => ['Báo cháy tự động', 'Sprinkler', 'Tăng áp cầu thang', 'Hút khói hành lang', 'Họng nước chữa cháy'],
            ],
        ];
        $images = ['factory' => 'facility', 'warehouse' => 'warehouse', 'office' => 'engineering-team', 'hotel' => 'equipment', 'apartment' => 'installation-team'];
        foreach ($items as $index => $item) {
            $mediaId = MediaSeeder::id($images[$item['key']]);
            Solution::firstOrCreate(['seed_key' => $item['key']], [
                'title' => $item['title'], 'short_title' => $item['name'], 'excerpt' => $item['description'],
                'body' => '<p>'.e($item['description']).'</p><h2>Các hạng mục giải pháp</h2><ul>'.collect($item['items'])->map(fn ($text) => '<li>'.e($text).'</li>')->implode('').'</ul><p>Phương án cụ thể được xác định sau khi khảo sát hiện trạng và nhu cầu vận hành của công trình.</p>',
                'highlights' => $item['items'], 'curator_media_id' => $mediaId, 'banner_media_id' => $mediaId,
                'seo_image_media_id' => $mediaId, 'seo_title' => $item['title'], 'seo_description' => $item['description'],
                'is_active' => true, 'is_home' => true, 'sort_order' => $index + 1,
            ]);
        }
    }
}
