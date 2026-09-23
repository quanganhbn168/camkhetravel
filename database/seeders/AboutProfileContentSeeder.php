<?php

namespace Database\Seeders;

use App\Settings\AboutSettings;
use Illuminate\Database\Seeder;

/** Nội dung mặc định CamKheTravel; chỉ cập nhật chữ, giữ nguyên ảnh đã chọn. */
final class AboutProfileContentSeeder extends Seeder
{
    public function run(): void
    {
        $settings = app(AboutSettings::class);
        $settings->page_intro = 'CamKheTravel tiếp nhận nhu cầu di chuyển, tư vấn phương tiện và trao đổi lịch trình trước chuyến đi.';
        $settings->story_title = 'Dịch vụ và cách phối hợp';
        $settings->story = '<p>Khách hàng có thể gửi điểm đón, điểm đến, thời gian và quy mô đoàn để CamKheTravel tiếp nhận yêu cầu tư vấn.</p><p>Với đối tác lữ hành, thông tin về tuyến đi, số khách và kế hoạch dự kiến giúp hai bên trao đổi phương án xe và lịch trình.</p>';
        $settings->services_link_label = 'Xem dịch vụ';
        $settings->principles_title = 'Nguyên tắc đồng hành';
        $settings->vision = 'Cùng khách hàng và đối tác chuẩn bị những hành trình thuận tiện.';
        $settings->mission = 'Tiếp nhận yêu cầu rõ ràng và tư vấn phương tiện theo lịch trình được trao đổi.';
        $settings->core_values = '<ul><li><strong>Rõ ràng</strong> — Thống nhất thông tin chuyến đi trước khi triển khai.</li><li><strong>Chủ động</strong> — Trao đổi điểm đón, thời gian và quy mô đoàn.</li><li><strong>Đồng hành</strong> — Duy trì đầu mối hỗ trợ trong quá trình chuẩn bị.</li></ul>';
        $settings->cta_title = 'Bắt đầu trao đổi về hành trình của bạn';
        $settings->cta_button_label = 'Gửi yêu cầu tư vấn';
        $settings->save();
    }
}
