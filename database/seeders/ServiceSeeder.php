<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

final class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $category = ServiceCategory::query()->where('name', 'Dịch vụ xe và du lịch')->firstOrFail();

        foreach ([
            ['Bao xe đi tỉnh', 'Đón tận nơi, chủ động thời gian và hành trình.', 'Đón tại điểm đã trao đổi, thống nhất thời gian và lộ trình trước chuyến đi.'],
            ['Bao xe du lịch', 'Xe riêng cho gia đình, nhóm bạn và chuyến đi nhiều ngày.', 'Gửi lịch trình và quy mô đoàn để CamKheTravel tư vấn phương án xe phù hợp.'],
            ['Xe cưới – xe dâu', 'Đồng hành trong ngày trọng đại.', 'Trao đổi trước về điểm đón, thời gian và các điểm dừng trong ngày.'],
            ['Xe hợp đồng', 'Cung cấp phương tiện cho doanh nghiệp và đối tác lữ hành.', 'Phương án xe và lịch trình được trao đổi theo nhu cầu từng đoàn.'],
            ['Xe ghép Hà Nội ⇄ Cẩm Khê', 'Kết nối Hà Nội, Cẩm Khê và Yên Lập theo lịch chạy.', 'Liên hệ để hỏi lịch, điểm đón và chỗ còn phù hợp với chuyến đi.'],
        ] as $index => [$title, $excerpt, $body]) {

            Service::query()->updateOrCreate(['title' => $title], [
                'service_category_id' => $category->getKey(), 'curator_media_id' => MediaSeeder::id('no-image'),
                'excerpt' => $excerpt, 'body' => '<p>'.$body.'</p>', 'status' => 'published',
                'is_home' => true, 'is_featured' => $index === 0, 'sort_order' => ($index + 1) * 10,
                'published_at' => now(), 'seo_title' => $title, 'seo_description' => $excerpt,
                'seo_image_media_id' => MediaSeeder::id('no-image'),
            ]);
        }
    }
}
