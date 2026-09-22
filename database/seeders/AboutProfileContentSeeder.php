<?php

namespace Database\Seeders;

use App\Settings\AboutSettings;
use Illuminate\Database\Seeder;

/** Nội dung rút gọn từ brief DVTEC; chỉ cập nhật chữ, giữ nguyên ảnh đã chọn. */
final class AboutProfileContentSeeder extends Seeder
{
    public function run(): void
    {
        $settings = app(AboutSettings::class);
        $settings->page_intro = 'Công ty TNHH Thương mại và Xây dựng DVTEC có trụ sở tại Bắc Ninh, hoạt động trong lĩnh vực phòng cháy chữa cháy, cơ điện và xây dựng công nghiệp. Chúng tôi cung cấp dịch vụ tư vấn, thiết kế, thi công và cung ứng thiết bị cho nhà xưởng, nhà máy và các công trình công nghiệp.';
        $settings->story_title = 'Lĩnh vực hoạt động';
        $settings->story = '<ul><li><strong>Phòng cháy chữa cháy:</strong> Tư vấn, thiết kế, thi công hệ thống báo cháy, chữa cháy và cung ứng thiết bị PCCC.</li><li><strong>Cơ điện công nghiệp:</strong> Thi công hệ thống điện, điều hòa, thông gió và điện nhẹ; lắp đặt camera, kiểm soát ra vào và âm thanh thông báo.</li><li><strong>Xây dựng và hạ tầng kỹ thuật:</strong> Triển khai hạ tầng công nghiệp, cơ khí, trần nhà xưởng, vách ngăn và các hạng mục kỹ thuật liên quan.</li><li><strong>Tư vấn giám sát và cung ứng:</strong> Giám sát thi công, cung cấp vật tư điện và thiết bị công nghiệp phù hợp nhu cầu công trình.</li></ul>';
        $settings->services_link_label = 'Xem các dịch vụ';
        $settings->principles_title = 'Định hướng và giá trị';
        $settings->vision = 'Hướng tới trở thành doanh nghiệp hàng đầu trong lĩnh vực giải pháp an toàn, cơ điện và xây dựng công nghiệp, được khách hàng tin cậy bởi năng lực chuyên môn, chất lượng và uy tín.';
        $settings->mission = 'Mang đến những giải pháp kỹ thuật an toàn, chất lượng và hiệu quả; đồng hành cùng khách hàng để xây dựng những công trình bền vững.';
        $settings->core_values = '<ul><li><strong>Chất lượng</strong> — Chú trọng từng sản phẩm, giải pháp và công trình.</li><li><strong>An toàn</strong> — Lấy an toàn và tuân thủ tiêu chuẩn kỹ thuật làm nền tảng.</li><li><strong>Uy tín</strong> — Giữ cam kết, minh bạch trong hợp tác.</li><li><strong>Tận tâm</strong> — Chủ động đồng hành, lấy khách hàng làm trọng tâm.</li><li><strong>Đoàn kết</strong> — Xây dựng đội ngũ gắn kết, cùng phát triển bền vững.</li></ul>';
        $settings->cta_title = 'Đồng hành cùng công trình của bạn';
        $settings->cta_button_label = 'Trao đổi về công trình';
        $settings->save();
    }
}
