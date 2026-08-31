# THT Media Laravel

Ứng dụng website độc lập chạy hoàn toàn trên Laravel, MySQL và media do Curator quản lý.

## Kiến trúc nội dung

- `service_categories` và `services` là danh mục/dịch vụ chính. Các nội dung legacy có bản chất dịch vụ được lưu tại đây; không dùng `LandingPage` để đại diện cho dịch vụ.
- `posts` và `post_categories` là hệ tin tức/blog.
- `landing_pages` là hệ landing page riêng, dùng `landing_templates`, builder schema và các quan hệ database tới danh mục dịch vụ, dịch vụ, dự án và bài viết.
- `projects` là hệ dự án; liên kết dịch vụ dùng `project_service`, liên kết landing page dùng `landing_page_project`.
- `slugs` là bảng morph duy nhất cho URL public của service, service category, landing page, project và post.
- Ảnh đại diện, gallery và bảng giá dùng media ID của Curator; file nằm trong storage Laravel, không tham chiếu CMS bên ngoài.

## Khởi tạo

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan db:seed
pnpm install
pnpm run build
```

`APP_URL`, kết nối MySQL và disk `public` phải được cấu hình theo môi trường. Nội dung, quan hệ, slug, SEO và media được quản lý từ database Laravel và Filament.

## Media và quản trị

File media nằm trong `storage/app/public` và được phục vụ qua `public/storage`. Media upload và media đã có đều quản lý tại `/admin/media`.

Các khu vực quản trị chính:

- `/admin/services`: danh mục và nội dung dịch vụ.
- `/admin/landing-pages`: landing page, template, builder schema và nội dung liên kết.
- `/admin/posts`: tin tức/blog.
- `/admin/projects`: dự án và dịch vụ liên quan.
- `/admin/service-pricings`: bảng giá và các gói được quản lý riêng theo từng dịch vụ.

## Kiểm tra

```powershell
php artisan test --compact
php artisan route:list
php artisan view:cache
```

Landing page public dùng template và dữ liệu trong `landing_pages`; form, tracking, SEO, sitemap và URL đều chạy bằng Laravel. Không có runtime dependency hoặc fallback sang hệ quản trị nội dung khác.
