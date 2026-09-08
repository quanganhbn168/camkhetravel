# Ảnh chia sẻ riêng cho nội dung

Ảnh chia sẻ riêng được ưu tiên trong `og:image` và `twitter:image`, không thay banner, ảnh đại diện hoặc ảnh trong bài.

## Quản trị

- Landing pages, dịch vụ, dự án, bài viết: trường **Ảnh chia sẻ riêng (Open Graph)** cạnh các trường SEO.
- Danh mục dịch vụ, dự án, bài viết: cùng trường trong form danh mục.
- Sự kiện, tin tức và chapter BNI: trường ảnh chia sẻ riêng trong form, dùng collection `seo_image` của hệ thống media BNI đang có. Sự kiện dùng ảnh này cho trang sự kiện, lễ chuyển giao/Pickleball và các trang thư mời/đăng ký của sự kiện.
- Trang chủ và các trang danh sách chung tiếp tục dùng **Ảnh Open Graph mặc định** trong cài đặt website.

Ưu tiên: ảnh chia sẻ riêng → ảnh đại diện/banner hiện có của trang nếu được cung cấp → ảnh Open Graph mặc định của website → logo. Chọn ảnh ngang JPEG/PNG/WebP; gợi ý 1200 × 630, không ép resize/crop khi chọn ảnh trong Curator.

## Dữ liệu và render

- Bảy bảng nội dung/danh mục chính có FK nullable `seo_image_media_id` tới Curator. Xóa media sẽ đặt FK về null để quay lại fallback.
- `HasSeoImage` và `SeoImageField` dùng chung; không lưu bản sao URL/ảnh riêng cho từng trang.
- BNI giữ cơ chế upload Spatie hiện hữu, collection `seo_image` một file cho từng sự kiện/bài viết/chapter. Không thay phân quyền hoặc cơ chế lưu media của module này.
- Partial SEO chung xuất ảnh cho Open Graph và Twitter. Schema ảnh của dịch vụ/dự án/bài viết tiếp tục dùng ảnh nội dung hiện tại.
- Landing builder và các template campaign được bọc bằng `layouts.landing`, sửa lỗi trước đây controller trả partial không có head/SEO.

## Cập nhật production

Sau khi cập nhật code tại đúng thư mục ứng dụng đang phục vụ domain:

```sh
git pull --ff-only
php artisan migrate --force
php artisan optimize:clear
php artisan filament:clear-cached-components
php artisan view:cache
```

Đợt này không đổi dependency hoặc asset Vite. Migration chỉ thêm trường nullable, không seed lại nội dung và không yêu cầu nhập đè database. Sau đó chọn ảnh chia sẻ cho từng nội dung trong admin.

Kiểm tra HTML public có đúng một `og:image` và một `twitter:image`; mở URL ảnh từ bên ngoài để xác nhận trả 200. Cache ảnh/preview của nền tảng chia sẻ có thể cập nhật sau HTML website. Việc push Git không chứng minh production đã chạy migration hoặc đọc được ảnh.

## Kiểm tra local

`ShareImageTest` bao phủ các landing đã xuất bản, bảy loại nội dung/danh mục, lưu/mở lại/bỏ ảnh ở admin, giữ nội dung hiện có, fallback khi xóa media và ảnh riêng cho sự kiện/tin tức BNI. Các test dùng transaction và fake storage cho upload BNI.
