<?php

// Explicit opt-in; this script must NEVER seed a development/production database.
require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
set_exception_handler(static function (Throwable $exception): never {
    fwrite(STDERR, (string) $exception.PHP_EOL);
    exit(1);
});
if (! $app->environment('testing') || getenv('DVTEC_BROWSER_FIXTURES') !== '1') {
    throw new RuntimeException('Browser fixtures require APP_ENV=testing and DVTEC_BROWSER_FIXTURES=1.');
}

Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => Database\Seeders\WebsiteSeeder::class, '--force' => true]);
$body = '<h2>Khảo sát công trình</h2><p>Nội dung kiểm thử giao diện, không phải hồ sơ khách hàng.</p><h2>Phương án triển khai</h2><p>Kiểm tra khả năng đọc và bố cục trên thiết bị di động.</p><h3>Thông tin bổ sung</h3><p>Thông tin kiểm thử.</p>';
$image = imagecreatetruecolor(1200, 700);
imagefill($image, 0, 0, imagecolorallocate($image, 16, 35, 62));
imagefilledrectangle($image, 700, 100, 1150, 640, imagecolorallocate($image, 215, 25, 32));
imagestring($image, 5, 40, 40, 'DVTEC - BROWSER TEST FIXTURE', imagecolorallocate($image, 255, 255, 255));
ob_start(); imagejpeg($image, null, 85); $bytes = ob_get_clean(); imagedestroy($image);
Illuminate\Support\Facades\Storage::disk('public')->put('qa-browser/preview.jpg', $bytes);
$media = Awcodes\Curator\Models\Media::create([
    'disk' => 'public', 'directory' => 'qa-browser', 'visibility' => 'public',
    'name' => 'preview', 'title' => 'Ảnh kiểm thử', 'path' => 'qa-browser/preview.jpg',
    'type' => 'image/jpeg', 'ext' => 'jpg', 'width' => 1200, 'height' => 700, 'size' => strlen($bytes),
]);
$serviceCategory = App\Models\ServiceCategory::create(['name' => 'Thi công PCCC QA', 'is_active' => true, 'is_featured' => true, 'is_home' => true]);
$categoryTwo = App\Models\ServiceCategory::create(['name' => 'Bảo trì PCCC QA', 'is_active' => true, 'is_featured' => true, 'is_home' => true]);
$service = App\Models\Service::create(['service_category_id' => $serviceCategory->id, 'title' => 'Dịch vụ kiểm thử PCCC', 'status' => 'published', 'published_at' => now()->subMinute(), 'is_home' => true, 'body' => $body, 'curator_media_id' => $media->id]);
App\Models\Service::create(['service_category_id' => $categoryTwo->id, 'title' => 'Bảo trì kiểm thử', 'status' => 'published', 'is_home' => true, 'body' => $body, 'curator_media_id' => $media->id]);
$productCategory = App\Models\ProductCategory::create(['name' => 'Thiết bị QA', 'is_active' => true]);
$product = App\Models\Product::create(['product_category_id' => $productCategory->id, 'title' => 'Sản phẩm kiểm thử', 'status' => 'published', 'body' => $body, 'curator_media_id' => $media->id]);
$projectCategory = App\Models\ProjectCategory::create(['name' => 'Dự án QA', 'is_active' => true]);
$project = App\Models\Project::create(['project_category_id' => $projectCategory->id, 'title' => 'Dự án kiểm thử', 'status' => 'published', 'body' => $body, 'curator_media_id' => $media->id]);
$post = App\Models\Post::create(['title' => 'Bài viết kiểm thử', 'status' => 'published', 'published_at' => now()->subMinute(), 'body' => $body, 'curator_media_id' => $media->id]);
$intro = App\Models\Intro::create(['title' => 'Giới thiệu kiểm thử', 'kind' => 'article', 'is_active' => true, 'content' => $body, 'published_at' => now()->subMinute()]);
$landing = App\Models\LandingPage::create(['title' => 'Landing kiểm thử', 'status' => 'published', 'show_header' => true, 'show_footer' => true, 'sections' => [['type' => 'hero', 'data' => ['title' => 'Landing kiểm thử']], ['type' => 'lead_form', 'data' => ['title' => 'Nhận tư vấn']]]]);
App\Models\Faq::create(['question' => 'Câu hỏi kiểm thử thứ nhất?', 'answer' => 'Nội dung trả lời kiểm thử thứ nhất.', 'group' => 'homepage', 'is_active' => true]);
App\Models\Faq::create(['question' => 'Câu hỏi kiểm thử thứ hai?', 'answer' => 'Nội dung trả lời kiểm thử thứ hai.', 'group' => 'homepage', 'is_active' => true]);
App\Models\HeroSlide::create(['title' => 'Giải pháp PCCC kiểm thử', 'description' => 'Dữ liệu kiểm tra bố cục, không phải thông tin kinh doanh.', 'is_active' => true, 'curator_media_id' => $media->id, 'primary_label' => 'Nhận tư vấn', 'primary_url' => '/#tu-van']);
$website = app(App\Settings\WebsiteSettings::class);
$website->about_image_media_id = $media->id;
$website->banner_media_id = $media->id;
$website->contact_image_media_id = $media->id;
$website->save();
$about = app(App\Settings\AboutSettings::class);
$about->page_title = ['vi' => 'Giới thiệu DVTEC kiểm thử'];
$about->page_intro = ['vi' => 'Dữ liệu kiểm thử riêng, không phải thông tin kinh doanh.'];
$about->story_title = ['vi' => 'Câu chuyện kiểm thử'];
$about->story = ['vi' => '<p>Nội dung kiểm tra bố cục giới thiệu.</p>'];
$about->default_image_media_id = $media->id;
$about->save();
$menu = App\Models\Menu::where('location', 'header')->firstOrFail();
$parent = $menu->items()->where('label', 'Dịch vụ')->firstOrFail();
App\Models\MenuItem::create(['menu_id' => $menu->id, 'parent_id' => $parent->id, 'label' => 'Hạng mục kiểm thử', 'url' => 'services.index', 'linked_source_type' => 'native_route', 'position' => 1, 'target' => '_self']);
$routes = ['/', '/gioi-thieu', '/dich-vu', '/du-an', '/san-pham', '/blog', '/bang-gia', '/lien-he', '/tim-kiem?q=pccc', '/'.$service->slug, '/san-pham/'.$product->slug, '/du-an/'.$project->slug, '/blog/'.$post->slug, '/bai-gioi-thieu/'.$intro->slug, '/'.$landing->slug];
if (! is_dir(base_path('test-results'))) mkdir(base_path('test-results'), 0755, true);
file_put_contents(base_path('test-results/browser-routes.json'), json_encode($routes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo 'Browser fixtures ready: '.count($routes)." routes.\n";
