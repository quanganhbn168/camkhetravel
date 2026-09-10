# DVTEC

Website doanh nghiệp cơ bản chạy độc lập trên Laravel 13, MySQL, Filament và Curator. Nội dung public, media, SEO và cài đặt website đều thuộc về database Laravel; không có BNI, WordPress hay lớp CMS bên ngoài.

## Phạm vi nội dung

- `services` và `service_categories`: dịch vụ và nhóm dịch vụ.
- `projects` và `project_categories`: dự án và nhóm dự án.
- `posts` và `post_categories`: tin tức/blog.
- `landing_pages`: landing page dùng builder block lấy từ database, không dùng template/campaign hard-code.
- `slugs`: nguồn duy nhất cho URL public của nội dung.
- Ảnh, gallery, video và ảnh SEO dùng media ID của Curator trong Laravel storage.

## Khởi tạo

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan db:seed
pnpm install --frozen-lockfile
pnpm run build
```

Cấu hình `APP_URL`, MySQL, `FILESYSTEM_DISK=public` và token Glide của Curator theo môi trường. Database mới được seed với ngôn ngữ tiếng Việt, menu cơ bản và tài khoản local/testing `admin@dvtec.test` với mật khẩu `password`; đổi thông tin này trước khi dùng thật.

## Media và quản trị

Media được quản lý tại `/admin/media`. Các khu vực chính trong Filament:

- `/admin/services`, `/admin/service-categories`: dịch vụ và danh mục.
- `/admin/projects`, `/admin/project-categories`: dự án và danh mục.
- `/admin/posts`, `/admin/post-categories`: tin tức và danh mục.
- `/admin/landing-pages`: landing page và builder block database.
- `/admin/settings`: nhận diện, trang chủ, doanh nghiệp, giới thiệu, liên hệ, SEO và giao diện.

Các resource dùng cấu trúc chuẩn `Pages`, `Schemas` và `Tables`; file upload chỉ đi qua Curator. Tracking nội bộ, event collector, dashboard so sánh và dữ liệu hành vi đã được loại bỏ. Nếu cần tích hợp dịch vụ bên ngoài, các đoạn mã đầy đủ vẫn có thể được dán thủ công trong phần cài đặt Tracking, không dùng ID tự sinh.

## Kiểm tra

```powershell
php artisan test --no-ansi
php artisan route:list
php artisan view:cache
pnpm run build
```

Khi cần tạo lại một database local rỗng, chạy `php artisan migrate:fresh --seed` sau khi đã chọn đúng database; lệnh này sẽ xóa toàn bộ dữ liệu của database đó.
