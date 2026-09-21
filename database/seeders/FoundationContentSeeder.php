<?php

namespace Database\Seeders;

use App\Models\AboutDepartment;
use App\Models\AboutTeamMember;
use App\Models\Faq;
use App\Models\HeroSlide;
use App\Models\HeroSlideTranslation;
use App\Models\Partner;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Tag;
use App\Models\Testimonial;
use App\Settings\AboutSettings;
use App\Settings\CompanySettings;
use App\Settings\DesignSettings;
use App\Settings\HomepageSettings;
use App\Settings\WebsiteSettings;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/** Seeds the starter catalogue used by a fresh corporate website install. */
final class FoundationContentSeeder extends Seeder
{
    /** @var array<string, int> */
    private array $mediaIds = [];

    /** @var array<string, int> */
    private array $tagIds = [];

    public function run(): void
    {
        $this->mediaIds = $this->seedMedia();
        $this->seedSettings();
        $categories = $this->seedCategories();
        $this->tagIds = $this->seedTags();

        $services = $this->seedServices($categories['services']);
        $projects = $this->seedProjects($categories['projects']);
        $products = $this->seedProducts($categories['products']);
        $posts = $this->seedPosts($categories['posts']);

        $this->seedRelationships($services, $projects, $posts);
        $this->seedHeroSlides();
        $this->seedAboutTeam();
        $this->seedTrustContent();
        $this->seedFaqs($services, $projects, $products);

        $this->command?->info('DVTEC PCCC content seeded.');
    }

    /** @return array<string, int> */
    private function seedMedia(): array
    {
        $files = [
            'hero' => 'installation-team.png',
            'equipment' => 'equipment.png',
            'pump-room' => 'pump-room.png',
            'sprinkler' => 'sprinkler-system.png',
            'engineering' => 'engineering-team.png',
            'warehouse' => 'warehouse.png',
            'facility' => 'facility.png',
            'technician' => 'technician.png',
        ];
        $ids = [];
        $disk = Storage::disk('public');

        foreach ($files as $key => $filename) {
            $source = base_path('resources/content/site/'.$filename);
            $path = 'media/site/'.$filename;

            if (! is_file($source)) {
                continue;
            }

            $sourceSize = filesize($source) ?: 0;

            if (! $disk->exists($path) || $disk->size($path) !== $sourceSize) {
                $disk->put($path, file_get_contents($source));
            }

            $absolutePath = $disk->path($path);
            $dimensions = @getimagesize($absolutePath) ?: [null, null, null, null, 'mime' => 'image/png'];
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $media = Media::query()->updateOrCreate(
                ['disk' => 'public', 'path' => $path],
                [
                    'directory' => 'media/site',
                    'visibility' => 'public',
                    'name' => pathinfo($filename, PATHINFO_FILENAME),
                    'width' => $dimensions[0] ?? null,
                    'height' => $dimensions[1] ?? null,
                    'size' => $sourceSize,
                    'type' => $dimensions['mime'] ?? 'image/png',
                    'ext' => strtolower($extension),
                    'alt' => $this->mediaAlt($key),
                    'title' => $this->mediaAlt($key),
                    'description' => 'Ảnh tư liệu PCCC DVTEC.',
                ],
            );
            $ids[$key] = (int) $media->getKey();
        }

        return $ids;
    }

    private function mediaAlt(string $key): string
    {
        return match ($key) {
            'hero' => 'Đội ngũ DVTEC triển khai hệ thống PCCC',
            'equipment' => 'Thiết bị phòng cháy chữa cháy DVTEC',
            'pump-room' => 'Phòng máy bơm chữa cháy',
            'sprinkler' => 'Hệ thống sprinkler trong nhà xưởng',
            'engineering' => 'Kỹ sư DVTEC khảo sát hệ thống PCCC',
            'warehouse' => 'Hệ thống PCCC trong kho logistics',
            'facility' => 'Nhà máy hiện đại có hệ thống PCCC',
            'technician' => 'Kỹ thuật viên kiểm tra hệ thống PCCC',
            default => 'Ảnh PCCC DVTEC',
        };
    }

    private function seedSettings(): void
    {
        $website = app(WebsiteSettings::class);
        $website->site_name = 'DVTEC';
        $website->tagline = 'Giải pháp PCCC toàn diện cho công trình';
        $website->company_name = 'DVTEC Trading & Construction';
        $website->contact_email = 'hello@dvtec.vn';
        $website->hotline = '0987 123 114';
        $website->contact_phone = '0987 123 114';
        $website->address = 'Hà Nội, Việt Nam';
        $website->facebook_url = '';
        $website->zalo_url = '';
        $website->youtube_url = '';
        $website->seo_title = 'DVTEC | Giải pháp PCCC toàn diện cho công trình';
        $website->seo_description = 'DVTEC tư vấn, thiết kế, thi công, bảo trì và cung cấp thiết bị phòng cháy chữa cháy đồng bộ cho công trình.';
        $website->seo_keywords = 'DVTEC, PCCC, phòng cháy chữa cháy, thi công hệ thống PCCC';
        $website->logo_media_id = null;
        $website->seo_image_media_id = $this->mediaId('facility');
        $website->about_image_media_id = $this->mediaId('facility');
        $website->contact_image_media_id = $this->mediaId('engineering');
        $website->banner_media_id = $this->mediaId('hero');
        $website->footer_background_media_id = $this->mediaId('technician');
        $website->phones = [['label' => 'Hotline', 'number' => '0987 123 114']];
        $website->branches = [[
            'name' => 'Văn phòng DVTEC',
            'address' => 'Hà Nội, Việt Nam',
            'is_active' => true,
        ]];
        $website->save();

        $homepage = app(HomepageSettings::class);
        $homepage->about_eyebrow = ['vi' => 'VỀ DVTEC'];
        $homepage->about_title = ['vi' => 'Giải pháp PCCC thực tế cho công trình an toàn hơn.'];
        $homepage->about_content = ['vi' => 'DVTEC kết nối tư vấn, thiết kế, thi công và bảo trì trong một quy trình rõ ràng, đồng bộ và đúng tiêu chuẩn.'];
        $homepage->stats = [
            ['value' => '120+', 'label' => 'công trình đã triển khai'],
            ['value' => '500+', 'label' => 'khách hàng doanh nghiệp'],
            ['value' => '10+', 'label' => 'năm kinh nghiệm'],
            ['value' => '24/7', 'label' => 'hỗ trợ kỹ thuật'],
        ];
        $homepage->commitments = ['vi' => "Khảo sát đúng hiện trạng\nThiết kế theo tiêu chuẩn\nBàn giao rõ ràng và đồng hành dài hạn"];
        $homepage->capabilities = ['vi' => "Tư vấn & khảo sát\nThiết kế hệ thống PCCC\nThi công - lắp đặt\nBảo trì - bảo dưỡng"];
        $homepage->consultation_title = ['vi' => 'Cần tư vấn giải pháp PCCC cho công trình?'];
        $homepage->consultation_content = ['vi' => 'Gửi thông tin công trình để đội ngũ DVTEC khảo sát và đề xuất phương án phù hợp.'];
        $homepage->faq_title = ['vi' => 'Câu hỏi thường gặp'];
        $homepage->faq_description = ['vi' => 'Thông tin cần biết trước khi bắt đầu triển khai hệ thống PCCC.'];
        $homepage->save();

        $company = app(CompanySettings::class);
        $company->tax_code = '';
        $company->representative = 'Đội ngũ DVTEC';
        $company->founded_year = 2024;
        $company->business_license = '';
        $company->save();

        $design = app(DesignSettings::class);
        $design->color_primary = '#e52327';
        $design->color_primary_hover = '#c9161a';
        $design->color_ink = '#061925';
        $design->color_surface = '#f3f7fa';
        $design->color_muted = '#dce8ef';
        $design->font_size_base = '1rem';
        $design->font_size_body = '1rem';
        $design->font_size_small = '0.875rem';
        $design->font_size_h1 = 'clamp(2.25rem, 4.5vw, 4rem)';
        $design->font_size_h2 = 'clamp(1.75rem, 3vw, 2.75rem)';
        $design->font_size_h3 = '1.25rem';
        $design->font_size_stat = 'clamp(2.25rem, 4vw, 3.75rem)';
        $design->save();

        $about = app(AboutSettings::class);
        $about->default_image_media_id = $this->mediaId('facility');
        $about->page_stats = [
            ['value' => '120+', 'label' => 'công trình đã triển khai'],
            ['value' => '500+', 'label' => 'khách hàng doanh nghiệp'],
            ['value' => '10+', 'label' => 'năm kinh nghiệm'],
            ['value' => '30+', 'label' => 'tỉnh thành phục vụ'],
        ];
        $about->page_label = ['vi' => 'VỀ CHÚNG TÔI'];
        $about->page_title = ['vi' => 'Đồng hành cùng doanh nghiệp kiến tạo môi trường an toàn hơn'];
        $about->page_intro = ['vi' => 'DVTEC cung cấp giải pháp PCCC đồng bộ từ khảo sát, thiết kế đến thi công, nghiệm thu và bảo trì định kỳ.'];
        $about->story_title = ['vi' => 'Từ tâm huyết đến sứ mệnh vì một Việt Nam an toàn hơn'];
        $about->story = ['vi' => '<p>Chúng tôi tin rằng một hệ thống PCCC tốt phải bắt đầu từ việc hiểu đúng công trình và kết thúc bằng khả năng vận hành ổn định.</p><p>DVTEC tập hợp đội ngũ kỹ thuật, quy trình minh bạch và vật tư phù hợp để đồng hành lâu dài cùng chủ đầu tư.</p>'];
        $about->story_image_media_id = $this->mediaId('engineering');
        $about->history = ['vi' => 'Hành trình kiến tạo giá trị bền vững'];
        $about->history_title = ['vi' => 'Dấu mốc phát triển'];
        $about->history_description = ['vi' => 'Từng bước chuẩn hóa năng lực, mở rộng đội ngũ và phục vụ nhiều loại công trình.'];
        $about->history_timeline = [
            ['year' => '2024', 'title' => 'Khởi tạo DVTEC', 'description' => 'Đặt nền móng cho đội ngũ kỹ thuật PCCC.', 'media_id' => $this->mediaId('facility')],
            ['year' => '2025', 'title' => 'Mở rộng dịch vụ', 'description' => 'Đồng bộ tư vấn, thiết kế, thi công và bảo trì.', 'media_id' => $this->mediaId('pump-room')],
            ['year' => '2026+', 'title' => 'Đồng hành dài hạn', 'description' => 'Tập trung chất lượng vận hành và hỗ trợ kỹ thuật.', 'media_id' => $this->mediaId('technician')],
        ];
        $about->mission = ['vi' => 'Mang đến giải pháp PCCC toàn diện, hiệu quả và phù hợp cho từng công trình.'];
        $about->vision = ['vi' => 'Trở thành đơn vị PCCC đáng tin cậy trong các công trình công nghiệp và dân dụng.'];
        $about->core_values = ['vi' => 'An toàn là nền tảng\nMinh bạch trong triển khai\nĐồng hành đến khi vận hành ổn định'];
        $about->core_values_image_media_id = $this->mediaId('warehouse');
        $about->principles_title = ['vi' => 'Năng lực tạo nên niềm tin'];
        $about->services_title = ['vi' => 'Giải pháp đồng bộ cho mọi công trình'];
        $about->services_link_label = ['vi' => 'Xem dịch vụ'];
        $about->stats_title = ['vi' => 'Những con số khẳng định năng lực'];
        $about->team_title = ['vi' => 'Những con người tạo nên DVTEC'];
        $about->team_description = ['vi' => 'Đội ngũ kỹ thuật giàu kinh nghiệm, tận tâm với từng hạng mục.'];
        $about->team_image_media_id = $this->mediaId('engineering');
        $about->office_title = ['vi' => 'Văn phòng DVTEC'];
        $about->office_description = ['vi' => 'Sẵn sàng tiếp nhận yêu cầu và cùng anh/chị làm rõ bài toán PCCC.'];
        $about->office_image_media_id = $this->mediaId('facility');
        $about->office_gallery = [$this->mediaId('facility'), $this->mediaId('warehouse')];
        $about->cta_title = ['vi' => 'Cùng DVTEC kiến tạo môi trường làm việc an toàn hơn'];
        $about->cta_button_label = ['vi' => 'Nhận tư vấn ngay'];
        $about->save();
    }

    /** @return array{services: array<string, ServiceCategory>, projects: array<string, ProjectCategory>, posts: array<string, PostCategory>, products: array<string, ProductCategory>} */
    private function seedCategories(): array
    {
        return [
            'services' => $this->categories(ServiceCategory::class, [
                'consultation' => ['name' => 'Tư vấn & khảo sát', 'description' => 'Khảo sát hiện trạng và xác định yêu cầu PCCC cho công trình.', 'sort_order' => 10, 'is_featured' => true, 'is_home' => true],
                'design' => ['name' => 'Thiết kế hệ thống PCCC', 'description' => 'Thiết kế giải pháp đồng bộ theo tiêu chuẩn và đặc thù công trình.', 'sort_order' => 20, 'is_featured' => true, 'is_home' => true],
                'installation' => ['name' => 'Thi công & lắp đặt', 'description' => 'Triển khai hệ thống PCCC đúng hồ sơ, đúng tiến độ và an toàn.', 'sort_order' => 30, 'is_featured' => true, 'is_home' => true],
                'maintenance' => ['name' => 'Bảo trì & kiểm tra', 'description' => 'Kiểm tra, bảo trì định kỳ để hệ thống luôn sẵn sàng hoạt động.', 'sort_order' => 40, 'is_featured' => false, 'is_home' => true],
            ]),
            'projects' => $this->categories(ProjectCategory::class, [
                'factory' => ['name' => 'Nhà xưởng', 'description' => 'Công trình sản xuất và nhà máy công nghiệp.', 'sort_order' => 10],
                'warehouse' => ['name' => 'Kho logistics', 'description' => 'Kho bãi, trung tâm logistics và lưu trữ hàng hóa.', 'sort_order' => 20],
                'office' => ['name' => 'Văn phòng', 'description' => 'Tòa nhà văn phòng và công trình thương mại.', 'sort_order' => 30],
                'other' => ['name' => 'Công trình khác', 'description' => 'Các loại công trình cần giải pháp PCCC riêng.', 'sort_order' => 40],
            ]),
            'posts' => $this->categories(PostCategory::class, [
                'knowledge' => ['name' => 'Kiến thức PCCC', 'description' => 'Kiến thức thực tế về hệ thống và quy chuẩn PCCC.', 'sort_order' => 10],
                'news' => ['name' => 'Tin tức DVTEC', 'description' => 'Cập nhật hoạt động và góc nhìn kỹ thuật từ DVTEC.', 'sort_order' => 20],
            ]),
            'products' => $this->categories(ProductCategory::class, [
                'alarm' => ['name' => 'Thiết bị báo cháy', 'description' => 'Tủ trung tâm, đầu báo và thiết bị cảnh báo cháy.', 'sort_order' => 10],
                'water' => ['name' => 'Thiết bị chữa cháy', 'description' => 'Máy bơm, van, bình và thiết bị chữa cháy.', 'sort_order' => 20],
                'sprinkler' => ['name' => 'Sprinkler & phụ kiện', 'description' => 'Đầu phun sprinkler và phụ kiện hệ thống.', 'sort_order' => 30],
                'safety' => ['name' => 'Thiết bị an toàn', 'description' => 'Thiết bị chỉ dẫn thoát nạn và hỗ trợ an toàn.', 'sort_order' => 40],
            ]),
        ];
    }

    /** @param class-string<Model> $model @param array<string, array<string, mixed>> $definitions @return array<string, Model> */
    private function categories(string $model, array $definitions): array
    {
        $result = [];

        foreach ($definitions as $key => $definition) {
            $name = (string) $definition['name'];
            $item = $model::query()->updateOrCreate(
                ['name' => $name],
                $definition + [
                    'is_active' => true,
                    'seo_title' => $name.' | DVTEC',
                    'seo_description' => $definition['description'],
                    'seo_image_media_id' => $this->mediaId('facility'),
                ],
            );
            $result[$key] = $item;
        }

        return $result;
    }

    /** @return array<string, int> */
    private function seedTags(): array
    {
        $ids = [];

        foreach (['PCCC', 'Nhà xưởng', 'Sprinkler', 'Thi công', 'Bảo trì', 'Thiết bị PCCC'] as $name) {
            $tag = Tag::query()->updateOrCreate(['name' => $name], ['is_active' => true]);
            $ids[$name] = (int) $tag->getKey();
        }

        return $ids;
    }

    private function mediaId(string $key): ?int
    {
        return $this->mediaIds[$key] ?? null;
    }

    /** @param array<string, ServiceCategory> $categories @return array<string, Service> */
    private function seedServices(array $categories): array
    {
        $definitions = [
            'survey' => [
                'title' => 'Tư vấn & khảo sát hiện trạng PCCC',
                'category' => 'consultation',
                'excerpt' => 'Khảo sát đúng hiện trạng, xác định rủi ro và đề xuất phương án PCCC phù hợp cho từng công trình.',
                'body' => '<h2>Khảo sát để hiểu đúng công trình</h2><p>DVTEC khảo sát hiện trạng, nhu cầu sử dụng và các yêu cầu pháp lý trước khi lập phương án. Mọi đề xuất đều bắt đầu từ dữ liệu thực tế, dễ kiểm tra và dễ triển khai.</p>',
                'media' => 'engineering',
                'tags' => ['PCCC', 'Nhà xưởng'],
                'featured' => true,
            ],
            'design' => [
                'title' => 'Thiết kế hệ thống PCCC',
                'category' => 'design',
                'excerpt' => 'Thiết kế hệ thống báo cháy, chữa cháy, sprinkler và hạ tầng liên quan theo tiêu chuẩn hiện hành.',
                'body' => '<h2>Thiết kế đồng bộ, rõ hồ sơ</h2><p>Đội ngũ kỹ thuật DVTEC phối hợp bản vẽ, thuyết minh và dự toán để chủ đầu tư dễ thẩm duyệt, thi công và vận hành.</p>',
                'media' => 'sprinkler',
                'tags' => ['PCCC', 'Sprinkler'],
                'featured' => true,
            ],
            'installation' => [
                'title' => 'Thi công & lắp đặt hệ thống PCCC',
                'category' => 'installation',
                'excerpt' => 'Thi công hệ thống PCCC đồng bộ, đúng hồ sơ, đúng tiêu chuẩn và an toàn trong từng giai đoạn.',
                'body' => '<h2>Triển khai chắc từ bản vẽ đến công trình</h2><p>DVTEC tổ chức thi công theo kế hoạch, kiểm soát vật tư, nghiệm thu từng hạng mục và bàn giao đầy đủ hồ sơ vận hành.</p>',
                'media' => 'pump-room',
                'tags' => ['PCCC', 'Thi công'],
                'featured' => true,
            ],
            'maintenance' => [
                'title' => 'Bảo trì & kiểm tra định kỳ PCCC',
                'category' => 'maintenance',
                'excerpt' => 'Kiểm tra, bảo trì và đánh giá định kỳ để hệ thống luôn sẵn sàng khi cần thiết.',
                'body' => '<h2>Giữ hệ thống luôn ở trạng thái sẵn sàng</h2><p>DVTEC kiểm tra thiết bị, thử vận hành, ghi nhận sai lệch và đề xuất kế hoạch khắc phục theo mức độ ưu tiên.</p>',
                'media' => 'technician',
                'tags' => ['PCCC', 'Bảo trì'],
                'featured' => false,
            ],
        ];
        $services = [];

        foreach ($definitions as $key => $definition) {
            $media = $definition['media'];
            $service = Service::query()->updateOrCreate(
                ['title' => $definition['title']],
                [
                    'service_category_id' => $categories[$definition['category']]->getKey(),
                    'curator_media_id' => $this->mediaId($media),
                    'gallery' => $this->gallery([$media, 'facility', 'technician']),
                    'backstage_gallery' => $this->gallery(['engineering', 'pump-room', $media]),
                    'process_title' => 'Quy trình triển khai rõ ràng',
                    'process_description' => 'Từ khảo sát đến bàn giao, từng bước đều có đầu mối và tiêu chí kiểm tra.',
                    'process_items' => [
                        ['title' => 'Tiếp nhận yêu cầu', 'description' => 'Làm rõ loại công trình và nhu cầu cụ thể.'],
                        ['title' => 'Khảo sát & đánh giá', 'description' => 'Ghi nhận hiện trạng, rủi ro và phạm vi triển khai.', 'media_id' => $this->mediaId('engineering')],
                        ['title' => 'Đề xuất giải pháp', 'description' => 'Đưa ra phương án phù hợp tiêu chuẩn và ngân sách.'],
                        ['title' => 'Triển khai thực hiện', 'description' => 'Phối hợp thi công, kiểm soát chất lượng và tiến độ.', 'media_id' => $this->mediaId('pump-room')],
                        ['title' => 'Kiểm tra & bàn giao', 'description' => 'Nghiệm thu, hướng dẫn vận hành và hoàn thiện hồ sơ.'],
                        ['title' => 'Bảo trì đồng hành', 'description' => 'Theo dõi định kỳ để hệ thống luôn sẵn sàng.'],
                    ],
                    'benefit_title' => 'Giải pháp phù hợp từng công trình',
                    'benefit_description' => 'Tập trung vào an toàn, khả năng vận hành và hiệu quả đầu tư dài hạn.',
                    'benefit_items' => [
                        ['title' => 'Đúng tiêu chuẩn', 'description' => 'Bám sát yêu cầu kỹ thuật và quy chuẩn hiện hành.', 'media_id' => $this->mediaId('sprinkler')],
                        ['title' => 'Đồng bộ thiết bị', 'description' => 'Phối hợp thiết bị, đường ống và điều khiển trong một hệ thống.'],
                        ['title' => 'Dễ kiểm tra', 'description' => 'Hồ sơ và mốc nghiệm thu rõ ràng, dễ theo dõi.'],
                    ],
                    'projects_title' => 'Công trình đã đồng hành',
                    'stats_title' => 'Năng lực PCCC DVTEC',
                    'stats_description' => 'Kinh nghiệm được tích lũy từ các công trình thực tế.',
                    'stats_items' => [
                        ['value' => '120+', 'label' => 'công trình đã triển khai'],
                        ['value' => '500+', 'label' => 'khách hàng doanh nghiệp'],
                        ['value' => '10+', 'label' => 'năm kinh nghiệm'],
                        ['value' => '24/7', 'label' => 'hỗ trợ kỹ thuật'],
                    ],
                    'commitment_title' => 'Đồng hành đến khi vận hành ổn định',
                    'commitment_description' => 'Đội ngũ DVTEC theo sát chất lượng và hỗ trợ sau bàn giao.',
                    'commitment_items' => [
                        ['title' => 'Kỹ sư chuyên môn cao', 'description' => 'Hiểu đặc thù từng loại công trình.'],
                        ['title' => 'Phản hồi nhanh', 'description' => 'Có đầu mối hỗ trợ rõ ràng trong quá trình triển khai.'],
                        ['title' => 'Hồ sơ minh bạch', 'description' => 'Bàn giao tài liệu đầy đủ và dễ sử dụng.'],
                    ],
                    'title' => $definition['title'],
                    'excerpt' => $definition['excerpt'],
                    'body' => $definition['body'],
                    'status' => 'published',
                    'is_featured' => $definition['featured'],
                    'is_home' => true,
                    'sort_order' => (count($services) + 1) * 10,
                    'seo_title' => $definition['title'].' | DVTEC',
                    'seo_description' => $definition['excerpt'],
                    'seo_image_media_id' => $this->mediaId($media),
                    'published_at' => now()->subDays(10 - count($services)),
                ],
            );
            $this->syncTags($service, $definition['tags']);
            $services[$key] = $service;
        }

        return $services;
    }

    /** @param array<string, ProjectCategory> $categories @return array<string, Project> */
    private function seedProjects(array $categories): array
    {
        $definitions = [
            'abc-factory' => [
                'title' => 'Nhà máy sản xuất điện tử ABC',
                'category' => 'factory',
                'client_name' => 'Công ty TNHH ABC Việt Nam',
                'industry' => 'Nhà máy sản xuất điện tử',
                'excerpt' => 'Thi công hệ thống PCCC đồng bộ cho nhà máy sản xuất điện tử tại khu công nghiệp.',
                'media' => 'facility',
                'gallery' => ['facility', 'warehouse', 'pump-room', 'sprinkler'],
                'completed_at' => '2025-07-01',
                'featured' => true,
                'tags' => ['PCCC', 'Nhà xưởng', 'Thi công'],
            ],
            'logistics' => [
                'title' => 'Kho logistics DVTEC Hưng Yên',
                'category' => 'warehouse',
                'client_name' => 'Chủ đầu tư logistics Hưng Yên',
                'industry' => 'Kho bãi và logistics',
                'excerpt' => 'Giải pháp sprinkler, bơm chữa cháy và báo cháy cho kho hàng quy mô lớn.',
                'media' => 'warehouse',
                'gallery' => ['warehouse', 'sprinkler', 'pump-room'],
                'completed_at' => '2025-10-15',
                'featured' => true,
                'tags' => ['PCCC', 'Nhà xưởng', 'Sprinkler'],
            ],
            'office' => [
                'title' => 'Tòa nhà văn phòng trung tâm',
                'category' => 'office',
                'client_name' => 'Chủ đầu tư DVTEC Office',
                'industry' => 'Văn phòng và thương mại',
                'excerpt' => 'Thiết kế và triển khai hệ thống PCCC phù hợp cho tòa nhà văn phòng nhiều tầng.',
                'media' => 'engineering',
                'gallery' => ['engineering', 'facility', 'technician'],
                'completed_at' => '2026-01-20',
                'featured' => false,
                'tags' => ['PCCC', 'Thi công'],
            ],
            'pump-room' => [
                'title' => 'Phòng máy bơm chữa cháy nhà xưởng',
                'category' => 'factory',
                'client_name' => 'Nhà máy công nghiệp miền Bắc',
                'industry' => 'Sản xuất công nghiệp',
                'excerpt' => 'Lắp đặt phòng máy bơm, đường ống và các thiết bị điều khiển cho hệ thống chữa cháy.',
                'media' => 'pump-room',
                'gallery' => ['pump-room', 'sprinkler', 'technician'],
                'completed_at' => '2026-03-12',
                'featured' => false,
                'tags' => ['PCCC', 'Thi công', 'Bảo trì'],
            ],
        ];
        $projects = [];

        foreach ($definitions as $key => $definition) {
            $body = '<h2>Tổng quan dự án</h2><p>'.$definition['excerpt'].' DVTEC phối hợp cùng chủ đầu tư để thống nhất phạm vi, tiêu chuẩn và kế hoạch nghiệm thu.</p><h2>Giải pháp triển khai</h2><ul><li>Khảo sát và cập nhật hồ sơ hiện trạng.</li><li>Thiết kế, cung cấp và lắp đặt hệ thống phù hợp.</li><li>Kiểm tra, nghiệm thu và hướng dẫn vận hành.</li></ul>';
            $project = Project::query()->updateOrCreate(
                ['title' => $definition['title']],
                [
                    'project_category_id' => $categories[$definition['category']]->getKey(),
                    'curator_media_id' => $this->mediaId($definition['media']),
                    'gallery' => $this->gallery($definition['gallery']),
                    'client_name' => $definition['client_name'],
                    'industry' => $definition['industry'],
                    'excerpt' => $definition['excerpt'],
                    'body' => $body,
                    'completed_at' => $definition['completed_at'],
                    'status' => 'published',
                    'is_featured' => $definition['featured'],
                    'sort_order' => (count($projects) + 1) * 10,
                    'seo_title' => $definition['title'].' | DVTEC',
                    'seo_description' => $definition['excerpt'],
                    'seo_image_media_id' => $this->mediaId($definition['media']),
                    'published_at' => now()->subDays(8 - count($projects)),
                ],
            );
            $this->syncTags($project, $definition['tags']);
            $projects[$key] = $project;
        }

        return $projects;
    }

    /** @param array<string, ProductCategory> $categories @return array<string, Product> */
    private function seedProducts(array $categories): array
    {
        $definitions = [
            'control-panel' => [
                'title' => 'Tủ trung tâm báo cháy địa chỉ DVTEC-FA-001',
                'sku' => 'DVTEC-FA-001',
                'category' => 'alarm',
                'media' => 'equipment',
                'excerpt' => 'Tủ trung tâm báo cháy địa chỉ cho hệ thống quy mô vừa và lớn.',
                'tags' => ['PCCC', 'Thiết bị PCCC'],
            ],
            'smoke-detector' => [
                'title' => 'Đầu báo khói địa chỉ DVTEC-FA-002',
                'sku' => 'DVTEC-FA-002',
                'category' => 'alarm',
                'media' => 'equipment',
                'excerpt' => 'Đầu báo khói địa chỉ giúp phát hiện sớm và quản lý theo từng khu vực.',
                'tags' => ['PCCC', 'Thiết bị PCCC'],
            ],
            'fire-pump' => [
                'title' => 'Máy bơm chữa cháy DVTEC-WF-001',
                'sku' => 'DVTEC-WF-001',
                'category' => 'water',
                'media' => 'pump-room',
                'excerpt' => 'Cụm máy bơm chữa cháy đồng bộ cho hệ thống cấp nước và sprinkler.',
                'tags' => ['PCCC', 'Thiết bị PCCC'],
            ],
            'sprinkler-head' => [
                'title' => 'Đầu phun sprinkler DVTEC-SP-001',
                'sku' => 'DVTEC-SP-001',
                'category' => 'sprinkler',
                'media' => 'sprinkler',
                'excerpt' => 'Đầu phun sprinkler và phụ kiện cho hệ thống chữa cháy tự động.',
                'tags' => ['PCCC', 'Sprinkler'],
            ],
            'extinguisher' => [
                'title' => 'Bình chữa cháy MFZ4 DVTEC-FE-001',
                'sku' => 'DVTEC-FE-001',
                'category' => 'water',
                'media' => 'equipment',
                'excerpt' => 'Bình chữa cháy xách tay phù hợp cho văn phòng, nhà xưởng và khu kỹ thuật.',
                'tags' => ['PCCC', 'Thiết bị PCCC'],
            ],
            'exit-light' => [
                'title' => 'Đèn exit & đèn sự cố DVTEC-SF-001',
                'sku' => 'DVTEC-SF-001',
                'category' => 'safety',
                'media' => 'technician',
                'excerpt' => 'Thiết bị chỉ dẫn thoát nạn và chiếu sáng sự cố cho lối thoát an toàn.',
                'tags' => ['PCCC', 'Thiết bị PCCC'],
            ],
        ];
        $products = [];

        foreach ($definitions as $key => $definition) {
            $body = '<h2>Thông tin thiết bị</h2><p>'.$definition['excerpt'].'</p><h2>Tư vấn lựa chọn</h2><p>DVTEC sẽ kiểm tra nhu cầu, tiêu chuẩn áp dụng và điều kiện lắp đặt để đề xuất cấu hình phù hợp.</p>';
            $product = Product::query()->updateOrCreate(
                ['sku' => $definition['sku']],
                [
                    'product_category_id' => $categories[$definition['category']]->getKey(),
                    'curator_media_id' => $this->mediaId($definition['media']),
                    'gallery' => $this->gallery([$definition['media'], 'facility']),
                    'title' => $definition['title'],
                    'excerpt' => $definition['excerpt'],
                    'body' => $body,
                    'status' => 'published',
                    'is_featured' => in_array($key, ['control-panel', 'fire-pump'], true),
                    'sort_order' => (count($products) + 1) * 10,
                    'seo_title' => $definition['title'].' | DVTEC',
                    'seo_description' => $definition['excerpt'],
                    'seo_image_media_id' => $this->mediaId($definition['media']),
                    'published_at' => now()->subDays(5 - count($products)),
                ],
            );
            $this->syncTags($product, $definition['tags']);
            $products[$key] = $product;
        }

        return $products;
    }

    /** @param array<string, PostCategory> $categories @return array<string, Post> */
    private function seedPosts(array $categories): array
    {
        $definitions = [
            'pccc-2026' => [
                'title' => 'Những điểm cần biết khi triển khai hệ thống PCCC',
                'category' => 'knowledge',
                'media' => 'sprinkler',
                'excerpt' => 'Các bước quan trọng để bắt đầu một hệ thống PCCC phù hợp, dễ kiểm tra và vận hành ổn định.',
                'tags' => ['PCCC', 'Sprinkler'],
            ],
            'maintenance' => [
                'title' => 'Vì sao cần kiểm tra PCCC định kỳ?',
                'category' => 'knowledge',
                'media' => 'technician',
                'excerpt' => 'Kiểm tra định kỳ giúp phát hiện sớm sai lệch và duy trì khả năng hoạt động của hệ thống.',
                'tags' => ['PCCC', 'Bảo trì'],
            ],
            'from-drawing' => [
                'title' => 'Từ bản vẽ đến công trình vận hành ổn định',
                'category' => 'news',
                'media' => 'facility',
                'excerpt' => 'Một quy trình phối hợp rõ ràng giúp hồ sơ, thi công và nghiệm thu đi cùng một mục tiêu an toàn.',
                'tags' => ['PCCC', 'Thi công', 'Nhà xưởng'],
            ],
        ];
        $posts = [];

        foreach ($definitions as $key => $definition) {
            $body = '<h2>'.e($definition['title']).'</h2><p>'.$definition['excerpt'].'</p><h2>Góc nhìn từ thực tế</h2><p>Việc khảo sát đúng hiện trạng, thống nhất tiêu chuẩn và ghi nhận hồ sơ theo từng mốc giúp chủ đầu tư chủ động hơn trong suốt vòng đời hệ thống.</p><table><thead><tr><th>Hạng mục</th><th>Mục tiêu</th></tr></thead><tbody><tr><td>Khảo sát</td><td>Hiểu đúng công trình</td></tr><tr><td>Thi công</td><td>Đúng hồ sơ và tiến độ</td></tr><tr><td>Bảo trì</td><td>Sẵn sàng khi cần</td></tr></tbody></table>';
            $post = Post::query()->updateOrCreate(
                ['title' => $definition['title']],
                [
                    'curator_media_id' => $this->mediaId($definition['media']),
                    'title' => $definition['title'],
                    'excerpt' => $definition['excerpt'],
                    'body' => $body,
                    'status' => 'published',
                    'is_featured' => $key === 'pccc-2026',
                    'seo_title' => $definition['title'].' | DVTEC',
                    'seo_description' => $definition['excerpt'],
                    'seo_image_media_id' => $this->mediaId($definition['media']),
                    'published_at' => now()->subDays(3 - count($posts)),
                ],
            );
            $post->categories()->sync([$categories[$definition['category']]->getKey() => ['sort_order' => 10]]);
            $this->syncTags($post, $definition['tags']);
            $posts[$key] = $post;
        }

        return $posts;
    }

    /** @param array<string, Service> $services @param array<string, Project> $projects @param array<string, Post> $posts */
    private function seedRelationships(array $services, array $projects, array $posts): void
    {
        $services['installation']->backstageProjects()->sync([
            $projects['abc-factory']->getKey(),
            $projects['logistics']->getKey(),
        ]);
        $services['design']->backstageProjects()->sync([
            $projects['office']->getKey(),
            $projects['pump-room']->getKey(),
        ]);
        $services['maintenance']->backstageProjects()->sync([
            $projects['abc-factory']->getKey(),
            $projects['pump-room']->getKey(),
        ]);

        $projects['abc-factory']->relatedPosts()->sync([
            $posts['pccc-2026']->getKey(),
            $posts['from-drawing']->getKey(),
        ]);
        $projects['logistics']->relatedPosts()->sync([$posts['maintenance']->getKey()]);
    }

    private function seedHeroSlides(): void
    {
        $slides = [
            [
                'media' => 'hero',
                'eyebrow' => 'GIẢI PHÁP PCCC TOÀN DIỆN',
                'title' => 'An toàn là nền tảng cho mọi công trình',
                'description' => 'Tư vấn, thiết kế, thi công và bảo trì hệ thống PCCC đồng bộ, rõ ràng và phù hợp thực tế.',
                'primary_label' => 'Khảo sát công trình',
                'primary_url' => '/lien-he',
                'secondary_label' => 'Xem dịch vụ',
                'secondary_url' => '/dich-vu',
            ],
            [
                'media' => 'facility',
                'eyebrow' => 'DVTEC TRADING & CONSTRUCTION',
                'title' => 'Giải pháp PCCC vững bền cho tương lai',
                'description' => 'Đồng hành cùng chủ đầu tư từ bản vẽ đầu tiên đến khi hệ thống vận hành ổn định.',
                'primary_label' => 'Nhận tư vấn ngay',
                'primary_url' => '/lien-he',
                'secondary_label' => 'Xem dự án',
                'secondary_url' => '/du-an',
            ],
        ];

        foreach ($slides as $sortOrder => $definition) {
            $slide = HeroSlide::query()->updateOrCreate(
                ['sort_order' => ($sortOrder + 1) * 10],
                [
                    'curator_media_id' => $this->mediaId($definition['media']),
                    'eyebrow' => $definition['eyebrow'],
                    'title' => $definition['title'],
                    'description' => $definition['description'],
                    'primary_label' => $definition['primary_label'],
                    'primary_url' => $definition['primary_url'],
                    'secondary_label' => $definition['secondary_label'],
                    'secondary_url' => $definition['secondary_url'],
                    'is_active' => true,
                ],
            );
            HeroSlideTranslation::query()->updateOrCreate(
                ['hero_slide_id' => $slide->getKey(), 'locale' => 'vi'],
                collect($definition)->except('media')->all(),
            );
        }
    }

    private function seedAboutTeam(): void
    {
        $department = AboutDepartment::query()->updateOrCreate(
            ['name' => 'Đội ngũ kỹ thuật'],
            [
                'description' => 'Kỹ sư và kỹ thuật viên đồng hành trong từng công trình PCCC.',
                'sort_order' => 10,
                'is_active' => true,
            ],
        );
        $members = [
            ['name' => 'Nguyễn Văn Hùng', 'position' => 'Giám đốc kỹ thuật', 'media' => 'engineering'],
            ['name' => 'Trần Thị Mai', 'position' => 'Kỹ sư thiết kế PCCC', 'media' => 'technician'],
            ['name' => 'Lê Đức Thịnh', 'position' => 'Trưởng nhóm thi công', 'media' => 'hero'],
        ];

        foreach ($members as $sortOrder => $member) {
            AboutTeamMember::query()->updateOrCreate(
                ['about_department_id' => $department->getKey(), 'name' => $member['name']],
                [
                    'media_id' => $this->mediaId($member['media']),
                    'position' => $member['position'],
                    'sort_order' => ($sortOrder + 1) * 10,
                    'is_active' => true,
                ],
            );
        }
    }

    private function seedTrustContent(): void
    {
        foreach (['Samsung Electronics', 'Viettel', 'Vinhomes', 'AEON'] as $sortOrder => $name) {
            Partner::query()->updateOrCreate(
                ['name' => $name],
                [
                    'is_active' => true,
                    'sort_order' => ($sortOrder + 1) * 10,
                ],
            );
        }

        Testimonial::query()->updateOrCreate(
            ['client_name' => 'Đại diện chủ đầu tư ABC'],
            [
                'client_role' => 'Giám đốc vận hành',
                'company_name' => 'Nhà máy sản xuất điện tử ABC',
                'quote' => 'DVTEC phối hợp rõ ràng từ khảo sát đến nghiệm thu, giúp chúng tôi chủ động hơn trong kế hoạch vận hành nhà máy.',
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 10,
                'curator_media_id' => $this->mediaId('engineering'),
            ],
        );
    }

    /** @param array<string, Service> $services @param array<string, Project> $projects @param array<string, Product> $products */
    private function seedFaqs(array $services, array $projects, array $products): void
    {
        $homepageFaqs = [
            ['question' => 'DVTEC có khảo sát công trình trước khi tư vấn không?', 'answer' => 'Có. Chúng tôi khảo sát hiện trạng và nhu cầu sử dụng trước khi đề xuất giải pháp PCCC.'],
            ['question' => 'Thời gian triển khai một hệ thống PCCC mất bao lâu?', 'answer' => 'Thời gian phụ thuộc quy mô, hồ sơ và điều kiện công trình. DVTEC sẽ lập tiến độ cụ thể sau khi khảo sát.'],
            ['question' => 'DVTEC có hỗ trợ bảo trì sau khi bàn giao không?', 'answer' => 'Có. Gói bảo trì định kỳ giúp kiểm tra thiết bị và duy trì khả năng sẵn sàng của hệ thống.'],
            ['question' => 'Tôi cần chuẩn bị gì để nhận tư vấn?', 'answer' => 'Anh/chị chỉ cần gửi thông tin công trình, mục đích sử dụng và bản vẽ hiện có nếu thuận tiện.'],
        ];

        foreach ($homepageFaqs as $sortOrder => $faq) {
            Faq::query()->updateOrCreate(
                ['faqable_type' => null, 'faqable_id' => null, 'group' => 'homepage', 'question' => $faq['question']],
                $faq + ['sort_order' => ($sortOrder + 1) * 10, 'is_active' => true],
            );
        }

        $this->attachFaqs($services['installation'], [
            ['question' => 'DVTEC thi công những hệ thống PCCC nào?', 'answer' => 'Chúng tôi triển khai báo cháy, sprinkler, bơm chữa cháy, họng nước và các hạng mục liên quan theo hồ sơ được duyệt.'],
            ['question' => 'Sau khi thi công có được hướng dẫn vận hành không?', 'answer' => 'Có. Hồ sơ bàn giao và hướng dẫn vận hành là một phần của quy trình nghiệm thu.'],
        ]);
        $this->attachFaqs($projects['abc-factory'], [
            ['question' => 'Dự án nhà máy ABC đã xử lý những hạng mục nào?', 'answer' => 'Phạm vi gồm báo cháy tự động, sprinkler, phòng máy bơm và kiểm tra bàn giao cho khu sản xuất.'],
        ]);
        $this->attachFaqs($products['control-panel'], [
            ['question' => 'Tủ trung tâm báo cháy phù hợp với công trình nào?', 'answer' => 'Thiết bị phù hợp cho hệ thống có nhiều khu vực cần giám sát tập trung và mở rộng theo giai đoạn.'],
        ]);
    }

    /** @param array<int, array{question: string, answer: string}> $items */
    private function attachFaqs(Model $model, array $items): void
    {
        foreach ($items as $sortOrder => $faq) {
            $model->faqs()->updateOrCreate(
                ['question' => $faq['question']],
                [
                    'group' => 'default',
                    'answer' => $faq['answer'],
                    'sort_order' => ($sortOrder + 1) * 10,
                    'is_active' => true,
                ],
            );
        }
    }

    /** @param array<int, string> $keys @return array<int, int> */
    private function gallery(array $keys): array
    {
        return collect($keys)
            ->map(fn (string $key): ?int => $this->mediaId($key))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /** @param array<int, string> $names */
    private function syncTags(Model $model, array $names): void
    {
        $model->tags()->sync(collect($names)
            ->filter(fn (string $name): bool => isset($this->tagIds[$name]))
            ->mapWithKeys(fn (string $name, int $sortOrder): array => [
                $this->tagIds[$name] => ['sort_order' => ($sortOrder + 1) * 10],
            ])
            ->all());
    }
}
