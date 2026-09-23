<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Database\Seeder;

final class PostSeeder extends Seeder
{
    public function run(): void
    {
        $noImageId = MediaSeeder::id('no-image');

        $category = PostCategory::query()->updateOrCreate(
            ['name' => 'Kinh nghiệm hành trình'],
            [
                'description' => 'Thông tin tham khảo khi chuẩn bị lịch trình và lựa chọn dịch vụ xe.',
                'is_active' => true,
                'sort_order' => 10,
                'curator_media_id' => $noImageId,
                'banner_media_id' => $noImageId,
                'seo_image_media_id' => $noImageId,
            ],
        );

        foreach ([
            [
                'title' => 'Chuẩn bị thông tin khi yêu cầu báo giá xe',
                'excerpt' => 'Một lịch trình rõ ràng giúp việc tư vấn loại xe, thời gian và chi phí sát nhu cầu hơn.',
                'body' => '<h2>Thông tin cần chuẩn bị</h2><p>Hãy ghi lại điểm đón, điểm đến, ngày giờ đi và về dự kiến, số khách, lượng hành lý cùng các điểm dừng trên đường. Nếu lịch trình có thể thay đổi, hãy nói rõ phần nào chưa chắc chắn.</p><h2>Trao đổi trước khi xác nhận</h2><p>Cho đơn vị cung cấp xe biết số điện thoại liên hệ và yêu cầu riêng của đoàn. Hỏi lại về thời gian phục vụ, các chặng di chuyển và khoản chi phí có thể phát sinh. Chỉ xác nhận chuyến đi sau khi hai bên thống nhất phương án.</p>',
                'is_featured' => true,
            ],
            [
                'title' => 'Chọn xe phù hợp với số khách và hành lý',
                'excerpt' => 'Số người chỉ là bước đầu; hành lý và thời gian di chuyển cũng ảnh hưởng đến phương án xe.',
                'body' => '<h2>Xác định quy mô đoàn</h2><p>Đếm số người thực tế tham gia, gồm cả trẻ em, và ước lượng vali hoặc đồ đạc cần mang theo. Với chuyến đi dài, không gian ngồi thoải mái cũng cần được cân nhắc.</p><h2>Nhờ tư vấn cấu hình cụ thể</h2><p>Cùng một nhóm xe có thể có cách bố trí chỗ ngồi và khoang hành lý khác nhau. Hãy trao đổi trực tiếp để chọn xe phù hợp với lịch trình và xác nhận sức chứa trước ngày đi.</p>',
                'is_featured' => false,
            ],
            [
                'title' => 'Trao đổi lịch đón cho chuyến xe cưới',
                'excerpt' => 'Thống nhất điểm đón và các mốc thời gian giúp gia đình chủ động trong ngày trọng đại.',
                'body' => '<h2>Lập lịch trình theo từng chặng</h2><p>Ghi rõ địa chỉ đón dâu, địa điểm tổ chức, các điểm dừng chụp ảnh và thời gian mong muốn ở mỗi chặng. Nếu cần nhiều xe, hãy xác định số khách trên từng xe.</p><h2>Xác nhận trước ngày cưới</h2><p>Trao đổi phương án xe, giờ tài xế có mặt và đầu mối liên hệ của gia đình. Trước ngày diễn ra, kiểm tra lại lịch trình để kịp điều chỉnh khi có thay đổi.</p>',
                'is_featured' => false,
            ],
        ] as $index => $article) {
            Post::query()->updateOrCreate(
                ['title' => $article['title']],
                [
                    'post_category_id' => $category->getKey(),
                    'curator_media_id' => $noImageId,
                    'seo_image_media_id' => $noImageId,
                    'excerpt' => $article['excerpt'],
                    'body' => $article['body'],
                    'status' => 'published',
                    'is_featured' => $article['is_featured'],
                    'published_at' => now()->subDays(3 - $index),
                ],
            );
        }
    }
}
