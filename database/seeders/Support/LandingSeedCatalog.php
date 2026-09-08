<?php

namespace Database\Seeders\Support;

/**
 * Source-backed catalog for the ten landing pages.
 *
 * This is a Laravel-owned snapshot of the source page data. It is deliberately
 * kept free of external URLs, models, and runtime imports so the public page
 * can be rendered from MySQL, Curator, and the Laravel view layer only.
 */
final class LandingSeedCatalog
{
    public const ADS = 'landing_ads';

    public const WEDDING = 'landing_wedding';

    public const COMMUNICATIONS = 'landing_communications';

    public const ACADEMY = 'landing_academy';

    public const ACADEMY_V2 = 'landing_academy_v2';

    public const OUTSOURCED_MARKETING = 'landing_outsourced_marketing';

    public const PROFILE = 'landing_profile';

    public const EVENT_ORGANIZATION = 'landing_event_organization';

    /** @return array<string, array<string, mixed>> */
    public static function pages(): array
    {
        return [
            'san-xuat-video-va-chay-quang-cao-facebook' => self::page(
                self::ADS,
                'Dịch vụ quảng cáo trực tuyến cho doanh nghiệp',
                'THT Media xây dựng, triển khai và tối ưu quảng cáo Facebook, Google, TikTok giúp doanh nghiệp tiếp cận đúng khách hàng và tối ưu ngân sách.',
                'Dịch Vụ Quảng Cáo Trực Tuyến Cho Doanh Nghiệp',
                'THT Media xây dựng, triển khai và tối ưu quảng cáo Facebook, Google, TikTok giúp doanh nghiệp tiếp cận đúng khách hàng và tối ưu ngân sách.',
                'DỊCH VỤ QUẢNG CÁO TRỰC TUYẾN CHO DOANH NGHIỆP',
                'Đừng để ngân sách quảng cáo trở thành chi phí. Hãy biến nó thành khoản đầu tư mang lại khách hàng.',
                'DỊCH VỤ QUẢNG CÁO FACEBOOK · GOOGLE · TIKTOK',
                'Mỗi chiến dịch quảng cáo chỉ thực sự hiệu quả khi được xây dựng trên nền tảng chiến lược đúng, nội dung phù hợp và dữ liệu được đo lường liên tục.',
                [
                    ['title' => 'Bài toán quảng cáo', 'description' => 'Chi phí tăng, nhiều lượt nhắn tin nhưng ít khách hàng, khó chọn kênh và chưa đo lường được hiệu quả từng chiến dịch.'],
                    ['title' => 'Giải pháp từ THT Media', 'description' => 'Quảng cáo Facebook, Google, TikTok, tiếp thị lại, landing page và đo lường được triển khai trong cùng một hệ thống.'],
                    ['title' => 'Chiến lược trước, quảng cáo sau', 'description' => 'Mọi quyết định tối ưu bắt đầu từ mục tiêu kinh doanh, khách hàng, thị trường và số liệu thực tế.'],
                ],
                [
                    ['title' => 'Tiếp nhận mục tiêu', 'description' => 'Làm rõ mục tiêu kinh doanh của doanh nghiệp.'],
                    ['title' => 'Đánh giá hiện trạng', 'description' => 'Đánh giá hệ thống quảng cáo đang vận hành.'],
                    ['title' => 'Lập kế hoạch', 'description' => 'Phân tích thị trường, khách hàng và phân bổ ngân sách.'],
                    ['title' => 'Sáng tạo và triển khai', 'description' => 'Sản xuất nội dung, thiết lập và vận hành chiến dịch.'],
                    ['title' => 'Tối ưu và báo cáo', 'description' => 'Theo dõi dữ liệu, tối ưu định kỳ và báo cáo minh bạch.'],
                ],
                [
                    ['name' => 'Ngân sách đến 15.000.000đ', 'description' => 'Phí quản lý tài khoản, tối đa 3 chiến dịch mỗi tháng.', 'price_label' => 'Phí quản lý 3.000.000đ', 'features' => ['Làm việc qua nhóm Zalo', 'Báo cáo hàng tuần', 'Ba ngày đầu báo cáo theo ngày']],
                    ['name' => 'Ngân sách 15–30 triệu', 'description' => 'Mức phí quản lý theo ngân sách quảng cáo thực tế.', 'price_label' => '20% ngân sách', 'features' => ['Tối đa 3 chiến dịch mỗi tháng', 'Báo cáo số liệu qua Google Sheet', 'Theo dõi và tối ưu định kỳ']],
                    ['name' => 'Ngân sách 30–50 triệu', 'description' => 'Mức phí quản lý theo ngân sách quảng cáo thực tế.', 'price_label' => '17% ngân sách', 'features' => ['Tối đa 3 chiến dịch mỗi tháng', 'Báo cáo số liệu qua Google Sheet', 'Đánh giá hiệu quả từng nhóm']],
                    ['name' => 'Ngân sách 50–100 triệu', 'description' => 'Mức phí quản lý theo ngân sách quảng cáo thực tế.', 'price_label' => '15% ngân sách', 'features' => ['Tối đa 3 chiến dịch mỗi tháng', 'Theo dõi chi phí và chuyển đổi', 'Tối ưu theo từng giai đoạn']],
                    ['name' => 'Ngân sách 100–500 triệu', 'description' => 'Mức phí quản lý theo ngân sách quảng cáo thực tế.', 'price_label' => '12% ngân sách', 'features' => ['Tối đa 3 chiến dịch mỗi tháng', 'Báo cáo và đề xuất hành động', 'Quản lý theo mục tiêu']],
                    ['name' => 'Ngân sách từ 500 triệu', 'description' => 'Mức phí quản lý theo ngân sách quảng cáo thực tế.', 'price_label' => '10% ngân sách', 'features' => ['Tối đa 3 chiến dịch mỗi tháng', 'Theo dõi nhiều nhóm quảng cáo', 'Báo cáo và tối ưu liên tục']],
                ],
                [
                    ['question' => 'Ngân sách quảng cáo có nằm trong phí dịch vụ không?', 'answer' => 'Cần tách rõ phí vận hành và ngân sách trả trực tiếp cho nền tảng quảng cáo.'],
                    ['question' => 'THT có cam kết doanh thu không?', 'answer' => 'Việc cam kết cần dựa trên dữ liệu, hệ thống theo dõi và trách nhiệm của cả Marketing lẫn bộ phận bán hàng.'],
                    ['question' => 'Tài khoản quảng cáo và dữ liệu thuộc về ai?', 'answer' => 'Nên sử dụng tài khoản thuộc sở hữu doanh nghiệp và quy định quyền truy cập, bàn giao trong hợp đồng.'],
                ],
                ['quảng cáo', 'facebook', 'ads', 'tvc'],
            ),
            'phong-su-cuoi' => self::page(
                self::WEDDING,
                'Quay chụp phóng sự cưới',
                'Quay phim và chụp ảnh phóng sự cưới trên toàn quốc và quốc tế cùng THT Media: lễ ăn hỏi, lễ đón dâu và tiệc cưới.',
                'Quay Chụp Phóng Sự Cưới Toàn Quốc & Quốc Tế',
                'Quay phim và chụp ảnh phóng sự cưới trên toàn quốc và quốc tế cùng THT Media: lễ ăn hỏi, lễ đón dâu và tiệc cưới.',
                'QUAY CHỤP PHÓNG SỰ CƯỚI',
                'Ghi lại những khoảnh khắc tự nhiên, cảm xúc và câu chuyện riêng của ngày cưới.',
                'PHÓNG SỰ CƯỚI · TOÀN QUỐC & QUỐC TẾ',
                'THT Media đồng hành từ lễ ăn hỏi, lễ đón dâu đến tiệc cưới với cách kể chuyện chân thật và bộ ảnh, video có thể lưu giữ lâu dài.',
                [
                    ['title' => 'Chụp phóng sự cưới', 'description' => 'Bắt trọn nghi thức, cảm xúc gia đình, khách mời và những chi tiết tự nhiên trong ngày cưới.'],
                    ['title' => 'Video phóng sự cưới', 'description' => 'Kể lại ngày cưới bằng hình ảnh chuyển động, âm thanh và những khoảnh khắc không dàn dựng.'],
                    ['title' => 'Bàn giao có chọn lọc', 'description' => 'Ảnh được chọn màu, video được hoàn thiện theo phạm vi đã thống nhất và sẵn sàng chia sẻ.'],
                ],
                [
                    ['title' => 'Trao đổi ngày cưới', 'description' => 'Xác nhận địa điểm, timeline và những nghi thức quan trọng.'],
                    ['title' => 'Lập shot-list', 'description' => 'Thống nhất những khoảnh khắc cần ưu tiên và cách ekip phối hợp.'],
                    ['title' => 'Ghi hình tự nhiên', 'description' => 'Theo sát diễn biến, cảm xúc và câu chuyện của gia đình.'],
                    ['title' => 'Chọn lọc và hậu kỳ', 'description' => 'Hoàn thiện ảnh, video và các phiên bản bàn giao theo gói.'],
                ],
                [
                    ['name' => 'Gói chụp phóng sự cưới', 'description' => 'Dành cho lễ ăn hỏi, lễ đón dâu hoặc tiệc cưới cần bộ ảnh phóng sự.', 'price' => 3500000, 'features' => ['Chụp lễ ăn hỏi hoặc đón dâu', 'Chọn màu ảnh', 'Bàn giao ảnh hoàn thiện', 'Giảm 20% khi in ảnh']],
                    ['name' => 'Gói video phóng sự cưới', 'description' => 'Dành cho gia đình cần video ghi lại câu chuyện và cảm xúc ngày cưới.', 'price' => 4000000, 'features' => ['Quay phóng sự cưới', 'Dựng video hoàn thiện', 'Âm thanh và màu sắc phù hợp', 'Bàn giao theo thỏa thuận']],
                    ['name' => 'Combo quay và chụp phóng sự cưới', 'description' => 'Kết hợp ảnh và video để lưu giữ đầy đủ những khoảnh khắc trong ngày cưới.', 'price' => 7000000, 'badge' => 'Đề xuất', 'features' => ['Bao gồm chụp và quay', 'Video phóng sự 5–7 phút', 'Ảnh hậu kỳ hoàn thiện', 'Tặng thiệp online']],
                    ['name' => 'Combo chụp và video cưới truyền thống', 'description' => 'Phương án đầy đủ cho gia đình cần ghi lại các nghi thức cưới truyền thống.', 'price' => 9000000, 'features' => ['Chụp và quay các nghi thức', 'Ảnh chọn màu', 'Video hoàn thiện', 'Bàn giao theo lịch thống nhất']],
                ],
                [
                    ['question' => 'THT có quay chụp ở tỉnh khác không?', 'answer' => 'Có. Lịch, địa điểm và các chi phí di chuyển, lưu trú sẽ được trao đổi rõ trong báo giá.'],
                    ['question' => 'Có được nhận file gốc không?', 'answer' => 'Quy định file gốc cần được thống nhất trong báo giá và hợp đồng trước khi triển khai.'],
                    ['question' => 'Có thể chỉ chụp hoặc chỉ quay không?', 'answer' => 'Có. THT Media có các gói chụp riêng, video riêng và combo theo nhu cầu.'],
                ],
                ['cưới', 'phóng sự cưới', 'wedding'],
            ),
            'giai-phap-truyen-thong-doanh-nghiep' => self::page(
                self::COMMUNICATIONS,
                'Giải pháp truyền thông doanh nghiệp',
                'THT Media đồng hành cùng doanh nghiệp xây dựng hệ thống truyền thông bài bản, từ hoạch định chiến lược, sản xuất hình ảnh/video đến quảng cáo đa kênh.',
                'Giải Pháp Truyền Thông Doanh Nghiệp Uy Tín, Chuyên Nghiệp',
                'THT Media đồng hành cùng doanh nghiệp xây dựng hệ thống truyền thông bài bản, từ hoạch định chiến lược, sản xuất hình ảnh/video đến quảng cáo đa kênh.',
                'GIẢI PHÁP TRUYỀN THÔNG DOANH NGHIỆP',
                'Xây dựng hệ thống truyền thông bài bản. Đồng hành cùng doanh nghiệp tăng trưởng bền vững.',
                'CHIẾN LƯỢC · NỘI DUNG · MEDIA · QUẢNG CÁO',
                'Một hệ thống truyền thông tốt giúp doanh nghiệp nói đúng điều cần nói, xuất hiện đúng nơi khách hàng tìm kiếm và tạo ra tư liệu dùng được lâu dài.',
                [
                    ['title' => 'Hoạch định chiến lược', 'description' => 'Làm rõ mục tiêu, khách hàng, thông điệp, kênh và thứ tự ưu tiên trước khi sản xuất.'],
                    ['title' => 'Sản xuất hình ảnh và video', 'description' => 'Tạo tư liệu thương hiệu, phim doanh nghiệp, TVC, ảnh và nội dung phù hợp từng kênh.'],
                    ['title' => 'Triển khai đa kênh', 'description' => 'Kết nối nội dung, website, landing page, quảng cáo và dữ liệu đo lường thành một hệ thống.'],
                ],
                [
                    ['title' => 'Làm rõ mục tiêu', 'description' => 'Xác định doanh nghiệp cần tăng nhận diện, tạo lead, bán hàng hay tuyển dụng.'],
                    ['title' => 'Audit hiện trạng', 'description' => 'Đánh giá kênh, nội dung, tài nguyên hình ảnh và điểm nghẽn chuyển đổi.'],
                    ['title' => 'Xây dựng kế hoạch', 'description' => 'Chốt thông điệp, định dạng, lịch triển khai và nguồn lực.'],
                    ['title' => 'Sản xuất và phát hành', 'description' => 'Triển khai media, nội dung, landing page và quảng cáo theo kế hoạch.'],
                    ['title' => 'Đo lường và tối ưu', 'description' => 'Đọc dữ liệu, tổng kết và điều chỉnh ưu tiên cho giai đoạn tiếp theo.'],
                ],
                [
                    ['name' => 'Tư vấn định hướng', 'description' => 'Phù hợp khi doanh nghiệp cần xác định bài toán và thứ tự ưu tiên.', 'price_label' => 'Theo brief thực tế', 'features' => ['Audit hiện trạng', 'Đề xuất chiến lược', 'Khung triển khai theo giai đoạn']],
                    ['name' => 'Sản xuất media', 'description' => 'Phù hợp khi doanh nghiệp cần hình ảnh, video hoặc tư liệu thương hiệu.', 'price_label' => 'Theo phạm vi sản xuất', 'features' => ['Ý tưởng và kịch bản', 'Quay chụp và hậu kỳ', 'Phiên bản theo kênh sử dụng']],
                    ['name' => 'Hệ thống truyền thông đồng bộ', 'description' => 'Kết hợp chiến lược, media, nội dung, quảng cáo và website theo mục tiêu.', 'price_label' => 'Theo scope thực tế', 'badge' => 'Tư vấn', 'features' => ['Kế hoạch theo giai đoạn', 'Một đầu mối phối hợp', 'Báo cáo và tối ưu']],
                ],
                [
                    ['question' => 'Doanh nghiệp chưa có chiến lược truyền thông thì bắt đầu từ đâu?', 'answer' => 'Bắt đầu bằng việc làm rõ mục tiêu kinh doanh, khách hàng, nguồn lực và những kênh doanh nghiệp đang có.'],
                    ['question' => 'THT có thể chỉ triển khai một hạng mục không?', 'answer' => 'Có. Phạm vi có thể là tư vấn, sản xuất media, nội dung, quảng cáo, website hoặc một hệ thống đồng bộ.'],
                    ['question' => 'Báo giá được xây dựng như thế nào?', 'answer' => 'Báo giá dựa trên mục tiêu, phạm vi, số lượng đầu ra, timeline và nguồn lực cần phối hợp.'],
                ],
                ['tvc', 'truyền thông', 'profile', 'sự kiện'],
            ),
            'khoa-hoc-nhiep-anh-thuc-chien' => self::page(
                self::ACADEMY,
                'Khóa học nhiếp ảnh thực chiến',
                'Khóa học nhiếp ảnh thực chiến tại THT Academy giúp học viên cầm máy, hiểu ánh sáng, bố cục, hậu kỳ và xây dựng Portfolio.',
                'Khóa Học Nhiếp Ảnh Thực Chiến | THT Academy',
                'Khóa học nhiếp ảnh thực chiến tại THT Academy: cầm máy, ánh sáng, bố cục, hậu kỳ và xây dựng Portfolio qua chương trình học thực hành.',
                'KHÓA HỌC NHIẾP ẢNH THỰC CHIẾN',
                'Đừng chỉ học cách chụp ảnh. Hãy học cách tạo ra giá trị từ từng khung hình.',
                'THT ACADEMY · HỌC BẰNG CÁCH LÀM RA SẢN PHẨM',
                'Chương trình tập trung vào thực hành, tư duy hình ảnh và khả năng biến kiến thức thành sản phẩm có thể trình bày.',
                [
                    ['title' => 'Cầm máy và hiểu thiết bị', 'description' => 'Nắm máy ảnh, ống kính, exposure và cài đặt để chủ động trong từng bối cảnh.'],
                    ['title' => 'Ánh sáng và bố cục', 'description' => 'Thực hành ánh sáng, màu sắc, bố cục, ngoại cảnh và studio để tạo hình ảnh có chủ đích.'],
                    ['title' => 'Hậu kỳ và Portfolio', 'description' => 'Hoàn thiện Lightroom, Photoshop và xây dựng sản phẩm để trình bày năng lực.'],
                ],
                [
                    ['title' => 'Học thử miễn phí', 'description' => 'Làm quen phương pháp học, thiết bị và mục tiêu của khóa.'],
                    ['title' => 'Nền tảng máy ảnh', 'description' => 'Máy ảnh, ống kính, exposure và cài đặt máy.'],
                    ['title' => 'Thực hành ánh sáng', 'description' => 'Lighting, bố cục, màu sắc, ngoại cảnh và studio.'],
                    ['title' => 'Hậu kỳ', 'description' => 'Lightroom cơ bản, Lightroom nâng cao và Photoshop.'],
                    ['title' => 'Tổng kết', 'description' => 'Hoàn thiện sản phẩm và định hướng Portfolio.'],
                ],
                [
                    ['name' => 'Khóa nhiếp ảnh thực chiến', 'description' => 'Học theo lộ trình thực hành, từ cầm máy đến hoàn thiện sản phẩm.', 'price_label' => 'Theo lịch khai giảng', 'features' => ['Học theo nhóm nhỏ', 'Thực hành ánh sáng và bố cục', 'Hậu kỳ và Portfolio']],
                    ['name' => 'Workshop chuyên đề', 'description' => 'Một buổi tập trung vào một kỹ năng hoặc bối cảnh thực hành cụ thể.', 'price_label' => 'Theo chuyên đề', 'features' => ['Chủ đề thực tế', 'Thực hành tại chỗ', 'Trao đổi trực tiếp với giảng viên']],
                ],
                [
                    ['question' => 'Chưa có máy ảnh có học được không?', 'answer' => 'Hãy trao đổi trước với THT Academy để được tư vấn thiết bị phù hợp với nội dung buổi học.'],
                    ['question' => 'Khóa học tập trung vào lý thuyết hay thực hành?', 'answer' => 'Chương trình được xây dựng theo hướng cầm máy, thực hành và làm ra sản phẩm.'],
                    ['question' => 'Sau khóa học có sản phẩm để làm Portfolio không?', 'answer' => 'Mục tiêu của chương trình là giúp học viên hoàn thiện sản phẩm và biết cách trình bày năng lực.'],
                ],
                ['nhiếp ảnh', 'academy', 'kỷ yếu'],
            ),
            'khoa-hoc-nhiep-anh' => self::page(
                self::ACADEMY_V2,
                'THT Academy Version 2 – Khóa học nhiếp ảnh thực chiến',
                'Khóa học nhiếp ảnh thực chiến tại THT Academy: cầm máy, ánh sáng, bố cục, hậu kỳ và xây dựng Portfolio qua 12 buổi học.',
                'THT Academy Version 2 – Khóa Học Nhiếp Ảnh Thực Chiến',
                'Khóa học nhiếp ảnh thực chiến tại THT Academy: cầm máy, ánh sáng, bố cục, hậu kỳ và xây dựng Portfolio qua 12 buổi học.',
                'HỌC NHIẾP ẢNH BẰNG CÁCH CẦM MÁY VÀ LÀM RA SẢN PHẨM',
                '12 buổi học, 10 tuần triển khai, nhóm 3–5 học viên để có thời gian thực hành và nhận phản hồi.',
                'THT ACADEMY VERSION 2 · 12 BUỔI THỰC CHIẾN',
                'Lộ trình đi từ máy ảnh, ánh sáng, bố cục đến Lightroom, Photoshop và sản phẩm cuối khóa.',
                [
                    ['title' => '12 buổi học', 'description' => 'Lộ trình liên tục từ nền tảng đến thực hành và hoàn thiện sản phẩm.'],
                    ['title' => '10 tuần triển khai', 'description' => 'Có thời gian làm bài, nhận phản hồi và điều chỉnh qua từng giai đoạn.'],
                    ['title' => '3–5 học viên', 'description' => 'Quy mô nhóm nhỏ để giảng viên theo sát quá trình cầm máy và hậu kỳ.'],
                ],
                [
                    ['title' => 'Máy ảnh và ống kính', 'description' => 'Làm quen thiết bị, cách chọn và cách sử dụng trong bối cảnh thực tế.'],
                    ['title' => 'Exposure và cài đặt máy', 'description' => 'Hiểu sáng tối, tốc độ, khẩu độ, ISO và kiểm soát máy ảnh.'],
                    ['title' => 'Ánh sáng, bố cục, màu', 'description' => 'Thực hành ánh sáng, bố cục, màu sắc và các buổi ngoại cảnh, studio.'],
                    ['title' => 'Lightroom và Photoshop', 'description' => 'Từ Lightroom cơ bản đến quy trình hậu kỳ nâng cao.'],
                    ['title' => 'Tổng kết Portfolio', 'description' => 'Chọn, hoàn thiện và trình bày sản phẩm sau khóa học.'],
                ],
                [
                    ['name' => 'THT Academy Version 2', 'description' => 'Trọn khóa học nhiếp ảnh thực chiến theo chương trình 12 buổi.', 'price' => 12000000, 'price_label' => '12.000.000đ / trọn khóa', 'badge' => 'Trọn khóa', 'features' => ['12 buổi học', '10 tuần triển khai', 'Nhóm 3–5 học viên', 'Thực hành và xây dựng Portfolio']],
                ],
                [
                    ['question' => 'Một khóa học kéo dài bao lâu?', 'answer' => 'Chương trình gồm 12 buổi triển khai trong khoảng 10 tuần.'],
                    ['question' => 'Số lượng học viên mỗi nhóm là bao nhiêu?', 'answer' => 'Mỗi nhóm dự kiến 3–5 học viên để đảm bảo thời gian thực hành và phản hồi.'],
                    ['question' => 'Chương trình có học hậu kỳ không?', 'answer' => 'Có. Lộ trình gồm Lightroom cơ bản, Lightroom nâng cao và Photoshop.'],
                    ['question' => 'Có buổi học thử không?', 'answer' => 'Chương trình có nội dung học thử miễn phí để học viên làm quen trước khi tham gia.'],
                ],
                ['nhiếp ảnh', 'academy', 'kỷ yếu'],
            ),
            'phong-marketing-thue-ngoai' => self::page(
                self::OUTSOURCED_MARKETING,
                'Phòng Marketing thuê ngoài cho doanh nghiệp',
                'Chiến lược, nội dung, thiết kế, video, quảng cáo, website và SEO trong một đội ngũ. Gửi Fanpage/Website để nhận audit và khung ưu tiên 30 ngày.',
                'Phòng Marketing Thuê Ngoài Cho Doanh Nghiệp | THT Media',
                'Chiến lược, nội dung, thiết kế, video, quảng cáo, website và SEO trong một đội ngũ. Gửi Fanpage/Website để nhận audit và khung ưu tiên 30 ngày.',
                'PHÒNG MARKETING THUÊ NGOÀI CHO DOANH NGHIỆP',
                'Một đội Marketing để triển khai đều đặn – không phải tuyển đủ từng vị trí.',
                'DỊCH VỤ MARKETING THUÊ NGOÀI · THT MEDIA',
                'THT Media đồng hành như một bộ phận Marketing mở rộng để cùng xác định mục tiêu, sản xuất, triển khai và theo dõi hiệu quả.',
                [
                    ['title' => 'Mỗi kênh làm một kiểu', 'description' => 'Fanpage, website, quảng cáo và video chưa dùng chung một thông điệp.'],
                    ['title' => 'Một người phải làm tất cả', 'description' => 'Viết bài, thiết kế, quay dựng, quảng cáo và báo cáo khiến phần nào cũng chỉ dừng ở mức cơ bản.'],
                    ['title' => 'Xây nền tảng trước', 'description' => 'Audit, mục tiêu, trụ cột nội dung, sản xuất, quảng cáo và KPI được triển khai theo từng giai đoạn.'],
                ],
                [
                    ['title' => 'Audit và mục tiêu', 'description' => 'Kiểm tra Fanpage, website, quảng cáo, nội dung, tài nguyên và quy trình nhận lead.'],
                    ['title' => 'Chiến lược và hệ thống nội dung', 'description' => 'Xác định khách hàng, thông điệp, trụ cột, ưu đãi, kênh và cách đo lường.'],
                    ['title' => 'Sản xuất và triển khai', 'description' => 'Hoàn thiện nội dung, thiết kế, video, landing page hoặc chiến dịch quảng cáo.'],
                    ['title' => 'Đo lường và tối ưu', 'description' => 'Đánh giá dữ liệu ban đầu, xác định phần hiệu quả và kế hoạch tháng tiếp theo.'],
                ],
                [
                    ['name' => 'Xây nền tảng', 'description' => 'Phù hợp Fanpage hoặc website chưa có hệ thống nội dung ổn định.', 'price' => 10000000, 'price_label' => '10.000.000đ/tháng', 'features' => ['Định hướng thông điệp', 'Trụ cột nội dung', 'Social content', 'Thiết kế và báo cáo']],
                    ['name' => 'Tăng trưởng', 'description' => 'Phù hợp doanh nghiệp đã có sản phẩm và nền tảng cơ bản, cần tiếp cận khách hàng mới.', 'price' => 15000000, 'price_label' => '15.000.000đ/tháng', 'badge' => 'Đề xuất', 'features' => ['Nội dung và media', 'Landing page', 'Quảng cáo', 'Theo dõi lead và tối ưu']],
                    ['name' => 'Phòng Marketing mở rộng', 'description' => 'Phù hợp doanh nghiệp cần một đội phối hợp dài hạn trên nhiều kênh.', 'price_label' => 'Theo scope thực tế', 'features' => ['Chiến lược và social', 'Media và Ads', 'Website – SEO', 'Báo cáo và kế hoạch quý']],
                ],
                [
                    ['question' => 'Ngân sách quảng cáo có nằm trong phí dịch vụ không?', 'answer' => 'Cần tách rõ phí vận hành và ngân sách trả trực tiếp cho nền tảng quảng cáo.'],
                    ['question' => 'Doanh nghiệp đã có nhân sự Marketing thì có thuê được không?', 'answer' => 'Có. THT có thể bổ sung các phần còn thiếu như chiến lược, thiết kế, video, quảng cáo hoặc website.'],
                    ['question' => 'Bao lâu có thể đánh giá hiệu quả?', 'answer' => 'Phụ thuộc kênh, dữ liệu, chu kỳ mua hàng và mục tiêu; hai bên cần thống nhất mốc đánh giá cho từng tầng KPI.'],
                ],
                ['marketing', 'quảng cáo', 'website', 'seo'],
            ),
            'thiet-ke-profile-doanh-nghiep' => self::page(
                self::PROFILE,
                'Thiết kế profile – hồ sơ năng lực doanh nghiệp',
                'Biên tập nội dung, chụp ảnh doanh nghiệp, thiết kế profile, hồ sơ năng lực và file trình chiếu. Gửi profile hiện tại để nhận audit miễn phí.',
                'Thiết Kế Profile Doanh Nghiệp – Hồ Sơ Năng Lực | THT Media',
                'Biên tập nội dung, chụp ảnh doanh nghiệp, thiết kế profile, hồ sơ năng lực và file trình chiếu. Gửi profile hiện tại để nhận audit miễn phí.',
                'THIẾT KẾ PROFILE – HỒ SƠ NĂNG LỰC DOANH NGHIỆP',
                'Thiết kế theo mục đích sử dụng để đối tác hiểu doanh nghiệp trước khi bắt đầu thuyết trình.',
                'COMPANY PROFILE · HỒ SƠ NĂNG LỰC · PRESENTATION',
                'THT Media hỗ trợ từ định hướng nội dung, cấu trúc, biên tập, chụp ảnh doanh nghiệp đến thiết kế và hoàn thiện file.',
                [
                    ['title' => 'Không chỉ dàn trang', 'description' => 'Profile cần trả lời điều đối tác muốn biết, có thứ tự ưu tiên và bằng chứng đủ rõ.'],
                    ['title' => 'Nội dung có cấu trúc', 'description' => 'Lập mục lục, biên tập câu chữ và sắp xếp sản phẩm, năng lực, dự án, chứng nhận theo mục tiêu.'],
                    ['title' => 'Đầu ra đúng mục đích', 'description' => 'Bàn giao PDF, file in, slide hoặc phiên bản ngoại ngữ theo phạm vi dự án.'],
                ],
                [
                    ['title' => 'Audit tài liệu', 'description' => 'Xem website, profile hiện tại, tài liệu gốc và mục tiêu sử dụng.'],
                    ['title' => 'Xây dựng cấu trúc', 'description' => 'Lập mục lục và thứ tự nội dung để năng lực quan trọng được nhìn thấy trước.'],
                    ['title' => 'Biên tập và hình ảnh', 'description' => 'Chuẩn hóa câu chữ, bổ sung hình ảnh hoặc lập shot-list chụp doanh nghiệp.'],
                    ['title' => 'Thiết kế và hoàn thiện', 'description' => 'Xây dựng concept, lưới, màu sắc, typography, biểu đồ và file bàn giao.'],
                ],
                [
                    ['name' => 'Thiết kế từ nội dung có sẵn', 'description' => 'Phù hợp doanh nghiệp đã có nội dung, hình ảnh và cấu trúc tương đối hoàn chỉnh.', 'price' => 300000, 'price_label' => '300.000đ/trang', 'features' => ['Đề xuất concept', 'Chuẩn hóa bố cục', 'Thiết kế', 'Hoàn thiện file']],
                    ['name' => 'Biên tập nội dung và thiết kế', 'description' => 'Phù hợp khi dữ liệu có sẵn nhưng nội dung còn rời rạc hoặc chưa thể hiện điểm mạnh.', 'price_label' => 'Theo phạm vi biên tập', 'features' => ['Audit tài liệu', 'Lập mục lục', 'Viết và biên tập', 'Thiết kế và hoàn thiện']],
                    ['name' => 'Profile trọn gói', 'description' => 'Phù hợp doanh nghiệp cần xây dựng mới hoặc làm lại toàn bộ.', 'price_label' => 'Theo brief thực tế', 'badge' => 'Tư vấn', 'features' => ['Tư vấn chiến lược', 'Copywriting', 'Chụp ảnh doanh nghiệp', 'Thiết kế, in ấn hoặc ngoại ngữ']],
                ],
                [
                    ['question' => 'Chưa có nội dung thì THT có làm được không?', 'answer' => 'Có. Doanh nghiệp cần cung cấp tài liệu gốc, website, hồ sơ pháp lý, sản phẩm, dự án và một đầu mối trả lời thông tin.'],
                    ['question' => 'Không có ảnh đẹp thì sao?', 'answer' => 'THT có thể lập shot-list và chụp ảnh tại văn phòng, nhà máy, showroom hoặc công trình.'],
                    ['question' => 'Profile có thể làm song ngữ không?', 'answer' => 'Có thể. Ngôn ngữ, trách nhiệm biên dịch và kiểm tra thuật ngữ cần được thống nhất trong báo giá.'],
                ],
                ['profile', 'hồ sơ', 'doanh nghiệp', 'tvc'],
            ),
            'to-chuc-su-kien-tron-goi' => self::page(
                self::EVENT_ORGANIZATION,
                'Tổ chức sự kiện trọn gói',
                'THT Media tổ chức sự kiện trọn gói từ ý tưởng, ngân sách đến vận hành hiện trường với một đầu mối chịu trách nhiệm xuyên suốt.',
                'Tổ Chức Sự Kiện Trọn Gói',
                'THT Media tổ chức sự kiện trọn gói từ ý tưởng, ngân sách đến vận hành hiện trường với một đầu mối chịu trách nhiệm xuyên suốt.',
                'TỔ CHỨC SỰ KIỆN TRỌN GÓI',
                'Một đầu mối lo trọn sự kiện. Từ ý tưởng, ngân sách đến vận hành hiện trường.',
                'DỊCH VỤ TỔ CHỨC SỰ KIỆN · THT MEDIA',
                'THT Media đồng hành để chương trình chỉn chu, đúng mục tiêu và đúng tinh thần thương hiệu.',
                [
                    ['title' => 'Concept và mục tiêu', 'description' => 'Ý tưởng cần gắn với thông điệp, đối tượng tham dự và tinh thần thương hiệu.'],
                    ['title' => 'Một master plan', 'description' => 'Kịch bản, sản xuất, kỹ thuật, nhân sự và timeline được kết nối trong một kế hoạch tổng thể.'],
                    ['title' => 'Vận hành hiện trường', 'description' => 'Một đầu mối chịu trách nhiệm xuyên suốt để giảm điểm đứt gãy trong ngày diễn ra.'],
                ],
                [
                    ['title' => 'Nhận brief và mục tiêu', 'description' => 'Làm rõ đối tượng, thời gian, địa điểm, ngân sách và yêu cầu chương trình.'],
                    ['title' => 'Khảo sát và đề xuất', 'description' => 'Khảo sát bối cảnh, xác định điểm cần lưu ý và đưa ra phương án tổng thể.'],
                    ['title' => 'Chốt concept và ngân sách', 'description' => 'Thống nhất chủ đề, scope, timeline và chi phí từng nhóm hạng mục.'],
                    ['title' => 'Sản xuất và chuẩn bị', 'description' => 'Thiết kế, booking nhân sự, hoàn thiện nội dung và checklist kỹ thuật.'],
                    ['title' => 'Tổng duyệt và vận hành', 'description' => 'Kiểm tra cue sheet, hiện trường và điều phối chương trình.'],
                    ['title' => 'Bàn giao và tổng kết', 'description' => 'Tổng hợp tư liệu, báo cáo hạng mục và đánh giá sau sự kiện.'],
                ],
                [
                    ['name' => 'Hoàn thiện và vận hành', 'description' => 'Doanh nghiệp đã có concept hoặc khung chương trình; THT hoàn thiện phần còn thiếu và vận hành hiện trường.', 'price_label' => 'Báo giá theo quy mô', 'features' => ['Hoàn thiện kế hoạch', 'Điều phối hạng mục', 'Vận hành hiện trường']],
                    ['name' => 'Tổ chức đồng bộ', 'description' => 'THT đảm nhiệm từ planning, kịch bản, sản xuất đến điều phối theo một master plan.', 'price_label' => 'Báo giá theo quy mô', 'badge' => 'Phổ biến', 'features' => ['Kịch bản và sản xuất', 'Nhân sự và kỹ thuật', 'Điều phối chương trình']],
                    ['name' => 'Concept riêng', 'description' => 'Phát triển một concept riêng có trải nghiệm và dấu ấn phù hợp với câu chuyện thương hiệu.', 'price_label' => 'Báo giá theo brief', 'features' => ['Ý tưởng và trải nghiệm', 'Thiết kế không gian', 'Kế hoạch triển khai']],
                ],
                [
                    ['question' => 'THT Media có tổ chức sự kiện trọn gói không?', 'answer' => 'Có. THT Media có thể đồng hành từ concept, kịch bản, sản xuất đến nhân sự và vận hành hiện trường.'],
                    ['question' => 'Có thể chỉ thuê một số hạng mục không?', 'answer' => 'Có. THT có thể tham gia những nhóm việc cần bổ sung và điều phối.'],
                    ['question' => 'Nên gửi brief trước bao lâu?', 'answer' => 'Càng sớm càng tốt để khảo sát, xây dựng phương án, booking nguồn lực và tổng duyệt.'],
                ],
                ['sự kiện', 'hội nghị', 'khai trương', 'khánh thành', 'tất niên'],
            ),
        ];
    }

    /** @param list<array<string, mixed>> $grid @param list<array<string, mixed>> $process @param list<array<string, mixed>> $plans @param list<array<string, mixed>> $faqs */
    private static function page(
        string $templateKey,
        string $title,
        string $excerpt,
        string $seoTitle,
        string $seoDescription,
        string $heroTitle,
        string $heroSubtitle,
        string $heroEyebrow,
        string $heroSummary,
        array $grid,
        array $process,
        array $plans,
        array $faqs,
        array $projectTerms,
    ): array {
        return [
            'template_key' => $templateKey,
            'title' => $title,
            'excerpt' => $excerpt,
            'seo_title' => $seoTitle,
            'seo_description' => $seoDescription,
            'hero' => [
                'block_id' => 'hero',
                'eyebrow' => $heroEyebrow,
                'title' => $heroTitle,
                'subtitle' => $heroSubtitle,
                'description' => $heroSummary,
                'cta_label' => 'Nhận tư vấn',
                'cta_url' => '#tu-van',
                'secondary_label' => 'Xem bảng giá',
                'secondary_url' => '#bang-gia',
            ],
            'sections' => [
                ['type' => 'hero', 'data' => ['block_id' => 'hero'] + ['eyebrow' => $heroEyebrow, 'title' => $heroTitle, 'subtitle' => $heroSubtitle, 'description' => $heroSummary, 'cta_label' => 'Nhận tư vấn', 'cta_url' => '#tu-van', 'secondary_label' => 'Xem bảng giá', 'secondary_url' => '#bang-gia']],
                ['type' => 'content_grid', 'data' => ['block_id' => 'gia-tri', 'eyebrow' => 'GIẢI PHÁP TỪ THT MEDIA', 'title' => $grid[0]['title'] ?? 'Giải pháp theo mục tiêu', 'description' => $grid[0]['description'] ?? null, 'items' => $grid]],
                ['type' => 'process', 'data' => ['block_id' => 'quy-trinh', 'eyebrow' => 'QUY TRÌNH TRIỂN KHAI', 'title' => 'Từ mục tiêu đến đầu ra có thể sử dụng', 'items' => $process]],
                ['type' => 'projects', 'data' => ['block_id' => 'du-an', 'eyebrow' => 'DỰ ÁN LIÊN QUAN', 'title' => 'Những dự án tham khảo', 'limit' => 6]],
                ['type' => 'pricing', 'data' => ['block_id' => 'bang-gia', 'eyebrow' => 'PHƯƠNG ÁN ĐẦU TƯ', 'title' => 'Bảng giá và gói dịch vụ', 'description' => 'Phạm vi cuối cùng được chốt theo mục tiêu, quy mô và đầu ra thực tế.']],
                ['type' => 'faqs', 'data' => ['block_id' => 'cau-hoi', 'eyebrow' => 'CÂU HỎI THƯỜNG GẶP', 'title' => 'Trao đổi trước khi bắt đầu', 'items' => $faqs]],
                ['type' => 'lead_form', 'data' => ['block_id' => 'tu-van', 'title' => 'Trao đổi nhu cầu cùng THT Media', 'description' => 'Gửi mục tiêu, phạm vi hoặc lịch dự kiến để nhận đề xuất phù hợp.', 'button_label' => 'Gửi yêu cầu tư vấn']],
            ],
            'plans' => $plans,
            'faq_items' => $faqs,
            'project_terms' => $projectTerms,
        ];
    }
}
