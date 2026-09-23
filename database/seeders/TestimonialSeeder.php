<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

final class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            [
                'client_name' => 'Khách gia đình',
                'client_role' => 'Chuyến đi riêng',
                'quote' => 'Cả nhà có thể chủ động điểm đón và thời gian nghỉ. Đi cùng nhau nên chuyến đi cũng thoải mái hơn.',
            ],
            [
                'client_name' => 'Đối tác lữ hành',
                'client_role' => 'Hợp tác cung cấp xe',
                'quote' => 'Có một đầu mối trao đổi về xe và lịch trình giúp việc chuẩn bị chương trình cho khách thuận tiện hơn.',
            ],
            [
                'client_name' => 'Khách thuê xe cưới',
                'client_role' => 'Xe dâu trong ngày cưới',
                'quote' => 'Một chiếc xe chỉn chu và lịch đón được thống nhất từ trước là điều chúng tôi mong đợi trong ngày cưới.',
            ],
        ] as $index => $data) {
            Testimonial::query()->updateOrCreate(
                ['client_name' => $data['client_name']],
                [
                    ...$data,
                    'company_name' => null,
                    'curator_media_id' => null,
                    'rating' => 5,
                    'is_active' => true,
                    'is_illustrative' => true,
                    'sort_order' => ($index + 1) * 10,
                ],
            );
        }
    }
}
