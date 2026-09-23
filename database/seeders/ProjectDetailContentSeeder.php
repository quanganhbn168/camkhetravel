<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

/** Dữ liệu minh họa cho khung chi tiết dự án, chưa phải hồ sơ nghiệm thu thực tế. */
final class ProjectDetailContentSeeder extends Seeder
{
    public function run(): void
    {
        $project = Project::where('title', 'Dự án công trình mẫu')->first();
        if (! $project || $project->details !== null) {
            return;
        }
        $this->call(MediaSeeder::class);
        $base = self::data();
        $cards = fn (array $items) => array_map(fn ($item) => [
            'title' => $item['title'] ?? $item['text'],
            'description' => isset($item['title']) ? $item['text'] : null,
            'icon' => $item['icon'], 'media_id' => null,
        ], $items);
        $images = array_values(array_filter(array_map(fn ($image) => MediaSeeder::id(pathinfo($image['file'], PATHINFO_FILENAME)), $base['images'])));
        $relatedIds = [];
        foreach (['Trung tâm thương mại', 'Khách sạn', 'Nhà máy sản xuất', 'Kho vận Logistics'] as $index => $title) {
            $related = Project::firstOrCreate(['title' => $title.' — dự án minh họa'], [
                'project_category_id' => $project->project_category_id,
                'status' => 'published', 'published_at' => now(),
                'curator_media_id' => MediaSeeder::id(['facility', 'engineering-team', 'facility', 'warehouse'][$index]),
                'excerpt' => 'Dự án minh họa bố cục, chưa phải hồ sơ công trình thực tế.',
                'details' => ['notice' => 'Dự án minh họa — cập nhật hồ sơ thực tế trước khi sử dụng.'],
            ]);
            $relatedIds[] = $related->id;
        }
        if ($project->excerpt === 'Hồ sơ dự án mẫu để bắt đầu quản trị nội dung.') {
            $project->excerpt = 'Thi công hệ thống PCCC đồng bộ, đáp ứng yêu cầu kỹ thuật và hỗ trợ vận hành sản xuất an toàn.';
        }
        if (blank($project->body) || $project->body === '<p>Hồ sơ dự án mẫu để bắt đầu quản trị nội dung.</p>') {
            $project->body = '<p>Nhà máy sản xuất là công trình có nhiều phân khu vận hành, cần hệ thống PCCC đồng bộ, phù hợp hiện trạng và đáp ứng yêu cầu an toàn trong suốt quá trình sản xuất.</p>';
        }
        if (blank($project->client_name)) {
            $project->client_name = 'Công ty TNHH ABC Việt Nam';
        }
        if (blank($project->industry)) {
            $project->industry = 'Nhà xưởng sản xuất';
        }
        if (empty($project->gallery)) {
            $project->gallery = $images;
        }
        $project->details = [
            'notice' => 'Nội dung minh họa — khung trình bày dự án, hình ảnh và thông số sẽ được thay bằng hồ sơ thực tế.',
            'hero' => ['banner_media_id' => MediaSeeder::id('facility'), 'location' => 'Bắc Ninh', 'area' => '8.000 m² (1 trệt, 2 tầng)'],
            'overview' => ['label' => 'Tổng quan dự án', 'title' => 'Thông tin chung', 'period' => '03/2024 – 07/2024', 'facts' => [
                ['label' => 'Hạng mục', 'value' => $base['facts']['Hạng mục']],
                ['label' => 'Hồ sơ áp dụng', 'value' => $base['facts']['Hồ sơ áp dụng']],
            ]],
            'challenges' => ['label' => 'Yêu cầu & bài toán', 'title' => 'Thách thức của dự án', 'description' => 'Nhiều khu vực sản xuất với đặc thù nguy cơ cháy khác nhau, yêu cầu cao về tiến độ, an toàn và không ảnh hưởng đến hoạt động sản xuất.', 'items' => $cards($base['challenges'])],
            'solution' => ['label' => 'Giải pháp triển khai', 'title' => 'Giải pháp toàn diện từ DVTEC', 'description' => 'Phối hợp nhiều giải pháp kỹ thuật phù hợp đặc thù nhà máy, từ phát hiện sớm, chữa cháy đến kiểm soát khói và hướng dẫn thoát nạn.', 'items' => array_map(fn ($text) => ['text' => $text], $base['solutions']), 'media_id' => MediaSeeder::id('pump-room'), 'caption' => 'Hệ thống bơm chữa cháy', 'cta_label' => 'Liên hệ tư vấn giải pháp'],
            'scope' => ['label' => 'Hạng mục thi công', 'title' => 'Các hạng mục chính', 'items' => $cards($base['scopes'])],
            'construction' => ['label' => 'Hình ảnh thực tế', 'title' => 'Một số hình ảnh trong quá trình thi công', 'images' => $images],
            'results' => ['label' => 'Kết quả đạt được', 'title' => 'Hiệu quả sau bàn giao', 'description' => 'Mục tiêu của giải pháp là hệ thống vận hành ổn định, thuận tiện kiểm tra và bảo trì, góp phần bảo vệ con người, tài sản và hoạt động sản xuất.', 'items' => $cards($base['results'])],
            'testimonial' => ['quote' => '“Đội ngũ triển khai phối hợp chặt chẽ, trao đổi rõ ràng và đồng hành trong quá trình bàn giao, hướng dẫn vận hành hệ thống.”', 'name' => 'Đại diện chủ đầu tư', 'role' => 'Nội dung nhận xét minh họa'],
            'related' => ['label' => 'Dự án liên quan', 'title' => 'Có thể bạn quan tâm', 'ids' => $relatedIds],
        ];
        $project->save();
    }

    private static function data(): array
    {
        return [
            'images' => [
                ['file' => 'facility.png', 'label' => 'Toàn cảnh công trình'],
                ['file' => 'sprinkler-system.png', 'label' => 'Hệ thống chữa cháy Sprinkler'],
                ['file' => 'pump-room.png', 'label' => 'Hệ thống bơm chữa cháy'],
                ['file' => 'equipment.png', 'label' => 'Thiết bị phòng cháy chữa cháy'],
                ['file' => 'installation-team.png', 'label' => 'Triển khai tại công trình'],
            ],
            'facts' => [
                'Chủ đầu tư' => 'Công ty TNHH ABC Việt Nam',
                'Địa điểm' => 'KCN VSIP, Bắc Ninh',
                'Loại công trình' => 'Nhà xưởng sản xuất',
                'Quy mô' => '8.000 m² (1 trệt, 2 tầng)',
                'Thời gian thực hiện' => '03/2024 – 07/2024',
                'Hạng mục' => 'Thiết kế, cung cấp thiết bị, thi công và bàn giao',
                'Hồ sơ áp dụng' => 'Theo thiết kế và yêu cầu kỹ thuật của công trình',
            ],
            'challenges' => [
                ['icon' => 'fa-industry', 'text' => 'Diện tích lớn, nhiều phân khu'],
                ['icon' => 'fa-clipboard-check', 'text' => 'Yêu cầu kỹ thuật khắt khe'],
                ['icon' => 'fa-stopwatch', 'text' => 'Tiến độ triển khai gấp'],
                ['icon' => 'fa-gears', 'text' => 'Đảm bảo hoạt động sản xuất liên tục'],
            ],
            'solutions' => ['Hệ thống báo cháy tự động (địa chỉ)', 'Hệ thống chữa cháy Sprinkler', 'Hệ thống chữa cháy bằng bơm và họng nước', 'Hệ thống cứu hỏa vách tường', 'Hệ thống hút khói, tăng áp cầu thang', 'Tích hợp hệ thống quản lý trung tâm (BMS)'],
            'scopes' => [
                ['icon' => 'fa-fire-flame-curved', 'text' => 'Báo cháy tự động'],
                ['icon' => 'fa-shower', 'text' => 'Chữa cháy Sprinkler'],
                ['icon' => 'fa-life-ring', 'text' => 'Họng nước chữa cháy'],
                ['icon' => 'fa-faucet-drip', 'text' => 'Bơm chữa cháy'],
                ['icon' => 'fa-fan', 'text' => 'Tăng áp – hút khói'],
                ['icon' => 'fa-sliders', 'text' => 'Tủ điện & điều khiển'],
            ],
            'results' => [
                ['icon' => 'fa-shield-halved', 'title' => 'Đồng bộ', 'text' => 'Các hạng mục hệ thống'],
                ['icon' => 'fa-gear', 'title' => 'Ổn định', 'text' => 'Vận hành hiệu quả'],
                ['icon' => 'fa-users', 'title' => 'An toàn', 'text' => 'Cho con người và tài sản'],
                ['icon' => 'fa-chart-column', 'title' => 'Tối ưu', 'text' => 'Chi phí vận hành'],
            ],
        ];
    }
}
