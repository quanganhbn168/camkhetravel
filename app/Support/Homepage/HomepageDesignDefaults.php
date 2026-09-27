<?php

namespace App\Support\Homepage;

final class HomepageDesignDefaults
{
    public static function audiences(): array
    {
        return [
            ['key' => 'trip', 'title' => 'Chuyến đi riêng', 'description' => 'Một chuyến xe dành riêng cho bạn và những người đồng hành. Chủ động điểm đón, thong thả tận hưởng hành trình.', 'highlights' => "Đi tỉnh, công tác\nDu lịch cùng gia đình\nXe ghép theo lịch", 'cta_label' => 'Tư vấn chuyến đi', 'service_ids' => []],
            ['key' => 'partner', 'title' => 'Đồng hành cùng đối tác', 'description' => 'Bạn lên chương trình, chúng tôi cùng chuẩn bị phương án xe. Phối hợp rõ ràng từ lịch trình đến quy mô đoàn.', 'highlights' => "Đơn vị lữ hành, tổ chức tour\nXe hợp đồng doanh nghiệp\nĐầu mối trao đổi xuyên suốt", 'cta_label' => 'Trao đổi hợp tác', 'service_ids' => []],
            ['key' => 'wedding', 'title' => 'Trọn vẹn ngày chung đôi', 'description' => 'Chăm chút những chặng đường trong ngày vui. Cùng gia đình sắp xếp xe dâu, giờ đón và hành trình phù hợp.', 'highlights' => "Xe dâu cho ngày cưới\nĐưa đón hai bên gia đình\nLịch trình theo ngày trọng đại", 'cta_label' => 'Tư vấn xe cưới', 'service_ids' => []],
        ];
    }

    public static function sections(): array
    {
        return [
            'hero_eyebrow' => 'CAMKHETRAVEL · DỊCH VỤ XE PHÚ THỌ',
            'services_eyebrow' => 'MỖI NHU CẦU, MỘT SỰ CHUẨN BỊ',
            'services_title' => 'Bạn lên kế hoạch. Chúng tôi đồng hành.',
            'services_description' => 'Từ chuyến đi riêng, chương trình của đối tác đến ngày vui của gia đình — cùng tìm phương án xe phù hợp.',
            'fleet_eyebrow' => 'CHỌN XE PHÙ HỢP',
            'fleet_title' => 'Thoải mái trên từng chặng đường',
            'fleet_description' => 'Chia sẻ số người, hành lý và lịch trình. Chúng tôi sẽ cùng bạn chọn phương án xe.',
            'about_eyebrow' => 'CHU ĐÁO TỪ NHỮNG ĐIỀU NHỎ',
            'routes_title' => 'Từ Phú Thọ, cùng bạn đến nơi cần đến',
            'process_eyebrow' => 'ĐƠN GIẢN ĐỂ BẮT ĐẦU',
            'process_title' => 'Một lịch trình rõ ràng. Một chuyến đi an tâm.',
            'journal_eyebrow' => 'TRƯỚC MỖI HÀNH TRÌNH',
            'journal_title' => 'Thêm cảm hứng, thêm kinh nghiệm',
        ];
    }

    public static function faqs(): array
    {
        return [
            ['question' => 'Cần cung cấp thông tin gì để được tư vấn xe?', 'answer' => 'Bạn có thể gửi điểm đón, điểm đến, ngày dự kiến, số người và lượng hành lý. Nếu chưa chốt lịch, hãy ghi rõ phần cần trao đổi thêm.'],
            ['question' => 'Gửi yêu cầu trên website đã được xem là đặt xe chưa?', 'answer' => 'Chưa. Đây là yêu cầu tư vấn. Lịch xe, phương án phục vụ và chi phí cần được hai bên trao đổi và xác nhận trước chuyến đi.'],
            ['question' => 'Chọn xe theo số người hay lượng hành lý?', 'answer' => 'Cần tính cả hai. Bạn nên cho biết số khách, số vali hoặc đồ mang theo để được tư vấn phương án phù hợp với hành trình.'],
            ['question' => 'Đối tác tour, doanh nghiệp gửi nhu cầu như thế nào?', 'answer' => 'Chọn “Hợp tác đối tác”, để lại tên đơn vị, đầu mối liên hệ và chương trình dự kiến. Có thể ghi thêm quy mô đoàn, số chuyến và nhu cầu hợp tác trong phần yêu cầu thêm.'],
            ['question' => 'Có thể trao đổi cả xe dâu và xe đưa đón gia đình không?', 'answer' => 'Bạn có thể chọn nhu cầu xe dâu, đưa đón gia đình hoặc cả hai trong form xe cưới. Hãy chia sẻ ngày cưới, giờ đón và các địa điểm để cùng sắp xếp phương án.'],
            ['question' => 'Làm sao để hỏi lịch xe ghép Hà Nội – Cẩm Khê?', 'answer' => 'Xem thông tin dịch vụ xe ghép hoặc gửi điểm đón, điểm đến và ngày cần đi. Lịch chạy và chỗ phù hợp sẽ được xác nhận khi trao đổi.'],
        ];
    }
}
