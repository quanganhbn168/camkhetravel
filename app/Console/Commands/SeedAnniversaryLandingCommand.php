<?php

namespace App\Console\Commands;

use App\Models\LandingPage;
use App\Models\PricingPlan;
use App\Models\Project;
use App\Support\Landing\LandingTemplateRegistry;
use App\Support\Localization\LocalizedUrl;
use Awcodes\Curator\Models\Media;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class SeedAnniversaryLandingCommand extends Command
{
    protected $signature = 'landing:seed-anniversary
        {--image=D:\\THTMedia\\slide003.jpg : Ảnh chương trình do THT Media cung cấp}';

    protected $description = 'Tạo hoặc cập nhật landing mẫu Tri ân khách hàng kỷ niệm 8 năm';

    public function handle(): int
    {
        $sourcePath = (string) $this->option('image');

        if (! is_file($sourcePath) || ! is_readable($sourcePath)) {
            $this->error('Không đọc được ảnh chương trình: '.$sourcePath);

            return self::FAILURE;
        }

        $imageInfo = getimagesize($sourcePath);

        if ($imageInfo === false || ($imageInfo['mime'] ?? null) !== 'image/jpeg') {
            $this->error('Ảnh chương trình phải là file JPEG hợp lệ.');

            return self::FAILURE;
        }

        $relativePath = 'landing-pages/tri-an-khach-hang-8-nam/slide003.jpg';
        $contents = file_get_contents($sourcePath);

        if ($contents === false || ! Storage::disk('public')->put($relativePath, $contents)) {
            throw new RuntimeException('Không thể sao chép ảnh chương trình vào public storage.');
        }

        [$width, $height] = $imageInfo;
        $media = Media::query()->updateOrCreate(
            ['disk' => 'public', 'path' => $relativePath],
            [
                'directory' => 'landing-pages/tri-an-khach-hang-8-nam',
                'visibility' => 'public',
                'name' => 'slide003',
                'width' => $width,
                'height' => $height,
                'size' => filesize($sourcePath),
                'type' => 'image/jpeg',
                'ext' => 'jpg',
                'alt' => 'Chương trình Tri ân khách hàng kỷ niệm 8 năm thành lập THT Media',
                'title' => 'Tri ân khách hàng - Kỷ niệm 8 năm thành lập',
                'pretty_name' => 'Tri ân khách hàng 8 năm THT Media',
            ],
        );

        $startsAt = CarbonImmutable::create(2026, 8, 15, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $endsAt = CarbonImmutable::create(2026, 9, 30, 23, 59, 59, 'Asia/Ho_Chi_Minh');

        $landing = DB::transaction(function () use ($media, $startsAt, $endsAt): LandingPage {
            $landing = LandingPage::query()
                ->whereHas('slugs', fn ($query) => $query->where('slug', 'tri-an-khach-hang-8-nam'))
                ->first()
                ?? LandingPage::query()->where('title', 'Tri ân khách hàng - Kỷ niệm 8 năm thành lập')->first()
                ?? new LandingPage;

            $landing->fill([
                'title' => 'Tri ân khách hàng - Kỷ niệm 8 năm thành lập',
                'slug' => 'tri-an-khach-hang-8-nam',
                'excerpt' => 'Website khó tìm, Fanpage không biết quản trị, chạy quảng cáo? Đừng lo, đã có THT Media.',
                'body' => '<p>Chương trình hỗ trợ khách hàng nhân dịp THT Media kỷ niệm 8 năm thành lập.</p>',
                'curator_media_id' => $media->id,
                'status' => 'published',
                'is_featured' => true,
                'seo_title' => 'Tri ân khách hàng - Kỷ niệm 8 năm THT Media',
                'seo_description' => 'Chương trình hỗ trợ Website và Fanpage từ ngày 15/08/2026 đến 30/09/2026 nhân kỷ niệm 8 năm thành lập THT Media.',
                'published_at' => now(),
                'layout_mode' => 'custom_template',
                'template_key' => LandingTemplateRegistry::ANNIVERSARY,
                'template_settings' => LandingTemplateRegistry::defaultSettings(LandingTemplateRegistry::ANNIVERSARY),
                'theme_settings' => LandingTemplateRegistry::palette(LandingTemplateRegistry::ANNIVERSARY),
                'campaign_starts_at' => $startsAt,
                'campaign_ends_at' => $endsAt,
                'expired_behavior' => 'show_message',
                'expired_message' => 'Chương trình tri ân đã kết thúc. Anh/chị vẫn có thể để lại thông tin để được THT Media tư vấn.',
                'show_header' => false,
                'show_footer' => false,
                'tracking_enabled' => true,
                'sections' => $this->sections($media, $startsAt, $endsAt),
            ]);
            $landing->save();

            PricingPlan::query()->updateOrCreate(
                ['landing_page_id' => $landing->id, 'name' => 'Hỗ trợ Website'],
                [
                    'badge' => 'Tri ân 8 năm',
                    'description' => 'Hỗ trợ Website trong thời gian chương trình.',
                    'price' => 0,
                    'price_label' => 'Miễn phí trong chương trình',
                    'features' => [
                        'Kiểm tra, bảo trì Website miễn phí',
                        'Chỉnh sửa Website miễn phí',
                        'Tặng Google Maps',
                    ],
                    'is_featured' => true,
                    'is_active' => true,
                    'sort_order' => 1,
                ],
            );

            PricingPlan::query()->updateOrCreate(
                ['landing_page_id' => $landing->id, 'name' => 'Hỗ trợ Fanpage'],
                [
                    'badge' => 'Tri ân 8 năm',
                    'description' => 'Hỗ trợ Fanpage trong thời gian chương trình.',
                    'price' => 0,
                    'price_label' => 'Miễn phí trong chương trình',
                    'features' => [
                        'Kiểm tra, tạo dựng Fanpage miễn phí',
                        'Tạo ảnh đại diện, ảnh bìa miễn phí',
                        'Lên kế hoạch bài đăng 15 ngày miễn phí',
                    ],
                    'is_featured' => false,
                    'is_active' => true,
                    'sort_order' => 2,
                ],
            );

            $projectIds = Project::query()
                ->published()
                ->where(function ($query): void {
                    $query->where('title', 'like', '%Fanpage%')
                        ->orWhere('excerpt', 'like', '%Fanpage%');
                })
                ->orderBy('id')
                ->limit(6)
                ->pluck('id');

            $landing->projects()->sync($projectIds);

            return $landing->fresh(['pricingPlans', 'projects', 'slugs']);
        });

        $this->info('Đã tạo/cập nhật landing: '.LocalizedUrl::slug($landing->slug));
        $this->line('Ảnh runtime: storage/app/public/'.$relativePath);
        $this->line('Gói hỗ trợ: '.$landing->pricingPlans->count().'; dự án liên kết: '.$landing->projects->count());

        return self::SUCCESS;
    }

    /** @return list<array{type: string, data: array<string, mixed>}> */
    private function sections(Media $media, CarbonImmutable $startsAt, CarbonImmutable $endsAt): array
    {
        return [
            [
                'type' => 'hero',
                'data' => [
                    'block_id' => 'hero-tri-an-8-nam',
                    'eyebrow' => 'THT Media · Kỷ niệm 8 năm thành lập',
                    'title' => 'Tri ân khách hàng',
                    'subtitle' => 'Website khó tìm, Fanpage không biết quản trị, chạy quảng cáo? Đừng lo, đã có THT Media.',
                    'media_id' => $media->id,
                    'cta_label' => 'Đăng ký nhận hỗ trợ',
                    'cta_url' => '#tu-van',
                    'secondary_label' => 'Xem quyền lợi',
                    'secondary_url' => '#uu-dai',
                    'note' => 'Liên hệ: 0375.433.678 / 0973.494.999',
                ],
            ],
            [
                'type' => 'countdown',
                'data' => [
                    'block_id' => 'countdown-tri-an',
                    'eyebrow' => 'Thời gian hỗ trợ',
                    'title' => 'Chương trình kết thúc sau',
                    'starts_at' => $startsAt->format('Y-m-d H:i:s'),
                    'ends_at' => $endsAt->format('Y-m-d H:i:s'),
                ],
            ],
            [
                'type' => 'benefits',
                'data' => [
                    'block_id' => 'uu-dai',
                    'eyebrow' => 'Đồng hành để tăng khách hàng',
                    'title' => 'Quyền lợi tri ân dành cho khách hàng',
                    'description' => 'Các hạng mục dưới đây được giữ nguyên theo nội dung chương trình THT Media công bố.',
                    'items' => [
                        [
                            'title' => 'Website',
                            'description' => 'Kiểm tra và hỗ trợ nền tảng Website.',
                            'features' => [
                                'Kiểm tra, bảo trì Website miễn phí',
                                'Chỉnh sửa Website miễn phí',
                                'Tặng Google Maps',
                            ],
                        ],
                        [
                            'title' => 'Fanpage',
                            'description' => 'Kiểm tra và hỗ trợ xây dựng nội dung Fanpage.',
                            'features' => [
                                'Kiểm tra, tạo dựng Fanpage miễn phí',
                                'Tạo ảnh đại diện, ảnh bìa miễn phí',
                                'Lên kế hoạch bài đăng 15 ngày miễn phí',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'type' => 'pricing',
                'data' => [
                    'block_id' => 'goi-ho-tro',
                    'eyebrow' => 'Minh bạch phạm vi',
                    'title' => 'Hai gói hỗ trợ trong chương trình',
                    'description' => 'Thời gian áp dụng từ 15/08/2026 đến hết 30/09/2026.',
                ],
            ],
            [
                'type' => 'projects',
                'data' => [
                    'block_id' => 'du-an-lien-quan',
                    'eyebrow' => 'Năng lực triển khai',
                    'title' => 'Các dự án Fanpage liên quan',
                    'description' => 'Nội dung lấy trực tiếp từ các dự án đã có trong CMS THT Media.',
                    'limit' => 6,
                ],
            ],
            [
                'type' => 'lead_form',
                'data' => [
                    'block_id' => 'tu-van',
                    'title' => 'Đăng ký nhận hỗ trợ',
                    'description' => 'Để lại số điện thoại để THT Media liên hệ và xác nhận hạng mục phù hợp.',
                    'button_label' => 'Gửi đăng ký chương trình',
                ],
            ],
            [
                'type' => 'cta',
                'data' => [
                    'block_id' => 'cta-cuoi-trang',
                    'eyebrow' => 'Đừng lo, đã có THT Media',
                    'title' => 'Cùng bắt đầu từ nhu cầu thực tế của anh/chị',
                    'description' => 'Liên hệ 0375.433.678 hoặc 0973.494.999 để được hỗ trợ.',
                    'cta_label' => 'Đăng ký ngay',
                    'cta_url' => '#tu-van',
                ],
            ],
        ];
    }
}
