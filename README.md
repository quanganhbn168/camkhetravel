# THT Media Laravel

Ứng dụng Laravel độc lập để thay thế website WordPress `thtmedia.com.vn`.

## Ranh giới an toàn

- WordPress được giữ ở project riêng `thtmedia.com.vn` để làm nguồn đối chiếu và phương án rollback; Laravel chạy độc lập tại project `thtmedia-laravel`.
- Laravel dùng database riêng `thtmedia_com_vn_laravel`.
- Kết nối `wordpress` dùng user MySQL chỉ có quyền `SELECT` trên `thtmedia_com_vn`.
- Không đổi DNS/document root trước khi hoàn tất đối chiếu URL, SEO và giao diện.
- Staging/local luôn trả `noindex, nofollow` và `robots.txt` chặn crawler.

## Khởi tạo

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate
php artisan filament:install --panels
```

Điền kết nối Laravel và WordPress trong `.env`. `APP_URL` phải đúng domain của từng môi trường; không sửa cứng domain trong source.

## Nhập WordPress

```powershell
php artisan wordpress:import
```

Importer có thể chạy lại. Bản ghi được upsert theo `source=wordpress` và `source_id`; dữ liệu nguồn không bị ghi ngược. Sau import, command mặc định sẽ:

1. copy media WordPress và ảnh ngoài đang dùng về storage Laravel;
2. rewrite URL trong nội dung sang media local;
3. materialize bộ SEO hiệu lực trung lập tên miền vào database.

Các tùy chọn kỹ thuật:

```powershell
php artisan wordpress:import --skip-media
php artisan wordpress:import --skip-localize
php artisan wordpress:import --skip-seo
```

`--skip-media` chỉ bỏ qua upsert bản ghi attachment từ WordPress. `--skip-localize` chỉ nên dùng khi chủ động chấp nhận dữ liệu vừa import còn URL nguồn; pipeline bình thường không nên bỏ bước này.

Importer giữ:

- post, page, landing, service, portfolio, partner, testimonial và video;
- slug, canonical path, trạng thái, ngày xuất bản, body/shortcode và toàn bộ post meta;
- taxonomy, term, quan hệ nội dung, menu và media metadata;
- Rank Math title, description, canonical, robots, focus keyword, OG/Twitter, schema và template toàn site.

## Media và SEO hiệu lực

File ảnh không lưu dạng BLOB trong MySQL. Database lưu `disk`, `file_path`, checksum SHA-256, kích thước, trạng thái local và alt SEO; file nằm trong `storage/app/public` và được phục vụ qua `public/storage`.

```powershell
php artisan storage:link
php artisan wordpress:localize-media --dry-run
php artisan wordpress:localize-media
php artisan wordpress:localize-media --verify

php artisan seo:materialize --dry-run
php artisan seo:materialize
php artisan seo:audit
```

SEO hiệu lực trong `content_items.effective_seo` không chứa hostname. Canonical, social image và JSON-LD được ghép từ `APP_URL` lúc render, nên cùng database chạy đúng trên local/staging/production. Canonical tuyệt đối không được ghi cứng theo domain `.test`.

Media đã nhập và media upload mới đều quản lý tại `/admin/media-assets`; form bài viết cho phép tìm/chọn ảnh đại diện và xem trước trực tiếp.

## Kiểm tra

```powershell
php artisan test --compact
php artisan route:list
php artisan view:cache
```

CMS: `/admin`. Tạo tài khoản quản trị riêng cho từng môi trường bằng `php artisan make:filament-user`.

## Điều kiện trước cutover

1. Mọi URL đang index phải trả đúng `200`, hoặc `301` có chủ đích.
2. Title, description, canonical, robots, Open Graph, schema và sitemap phải khớp bản WordPress.
3. Quyết định nội dung post `source_id=6920` đang trùng canonical `/chay-quang-cao-facebook/` với landing `source_id=4760`; hiện route và sitemap giữ landing 4760 làm bản thắng, không tạo URL trùng thứ hai.
4. Chuyển đổi shortcode/ACF thành Blade/component và kiểm tra giao diện responsive.
5. Chuyển/cố định media, biểu mẫu và tracking.
6. Chạy crawl so sánh WordPress và Laravel; chỉ sau đó mới đổi document root.
