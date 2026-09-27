# Dùng lại frontend khi clone

## Trang chủ và quản trị

Trang chủ dùng chung Bootstrap 5 và `layouts.master`. Ảnh, ba nhóm nhu cầu, dịch vụ liên kết, nhóm xe, tuyến phục vụ, quy trình và lời mời tư vấn được quản lý tại **Cài đặt website → Trang chủ**. Hero có ảnh mobile riêng trong **Hero slides**. Phản hồi minh họa và chỉ số chưa đánh dấu xác minh không công bố trên trang chủ.

Khi cập nhật một database đang sử dụng, chạy `php artisan migrate --force`, sau đó `php artisan homepage:install-design`. Lệnh thứ hai nhập ảnh WebP đã có trong repo vào Curator và chỉ áp dụng thiết kế v1 một lần. Nó giữ ảnh/nội dung riêng đang có và không ghi đè các lần chỉnh sửa sau khi đã nhập. Không chạy lại toàn bộ `DatabaseSeeder` trên dữ liệu khách hàng để áp dụng giao diện. Với database mới, quy trình seed ban đầu có sẵn bước nhập thiết kế.

Ảnh runtime nằm trong `storage/app/public/media/homepage/v1/`; cần liên kết `public/storage` như các ảnh Curator khác. File nguồn nằm ở `public/images/homepage-redesign/v1/`. Khi triển khai máy khác phải chạy bước nhập dữ liệu và build, chỉ kéo Git chưa cập nhật CMS hay tạo build.

Form tư vấn dùng `contact.store` và lưu `ContactRequest`, phân biệt chuyến đi riêng/đối tác/xe cưới. Chọn xe hoặc đổi nhu cầu giữ tên, điện thoại đang nhập trong bộ nhớ của trang; không lưu thông tin này vào localStorage. Đây là yêu cầu tư vấn, chưa phải đặt xe hoặc thanh toán.

## Những chỗ cần đổi cho một website mới

1. **Tên, logo, liên hệ, menu và nội dung:** sửa trong quản trị hiện có. Manifest lấy tên website từ quản trị, không có tên khách hàng cố định trong file manifest.
2. **Màu và font:** sửa `resources/css/brand.css`. `--site-primary` là màu chính, `--site-accent` là màu nhấn; dùng mã hex 6 ký tự. Nếu chọn một font mới chưa có, nạp font trong `resources/css/fonts.css`.
3. **Bố cục riêng:** sửa Blade và CSS tương ứng trong `resources/css/pages/`. Header, footer và các thành phần dùng chung nằm trong `resources/css/components/`.
4. **Tài sản nhận diện:** thay favicon, biểu tượng ứng dụng trong `public/` và ảnh/logo theo khách hàng. Ảnh `images/no-image.svg` dùng xám trung tính để tái sử dụng cho mọi thương hiệu.

Chạy `pnpm install --frozen-lockfile` khi mới clone, sau đó `pnpm build`. Khi phát triển dùng `pnpm dev` như bình thường. Cần triển khai cả mã nguồn và `public/build` vừa tạo theo quy trình của máy chủ; Git đang bỏ qua thư mục build.

## Hợp đồng CSS dùng chung

- `brand.css`: nơi duy nhất đặt màu thương hiệu, font và thông số dùng chung. Vite tự sinh màu hover, màu đậm, nền nhạt, viền, RGB của Bootstrap và màu chữ tương phản từ hai màu đầu vào. Không chỉnh giá trị được sinh trong `public/build`.
- `theme.css`: ánh xạ biến global vào Bootstrap và trạng thái của component. Không đặt lại bảng màu cho từng khách hàng ở đây.
- `components/*.css`: giao diện của thành phần dùng chung.
- `pages/*.css`: bố cục, kích thước và responsive của từng trang; vẫn dùng `var(--site-...)` hoặc `var(--bs-...)` cho màu/font. Không khai báo lại `--site-primary` và không chép mã màu thương hiệu vào CSS trang.
- Font chữ có ba vai trò: `--site-font-body`, `--site-font-heading`, `--site-font-script`. Đổi vai trò ở global; cỡ chữ đặc thù của hero/card vẫn có thể đặt tại CSS trang. Cỡ chữ chung, khoảng cách section và bo góc đã có biến trong `brand.css`.
- RGB Bootstrap dùng dấu phẩy: viết `rgba(var(--bs-primary-rgb), .15)`, không dùng cú pháp trộn `rgb(var(--bs-primary-rgb) / .15)`.
- Trắng/đen, trạng thái thành công/lỗi, màu dịch vụ ngoài như Zalo và nội dung ảnh là các màu độc lập có chủ đích. Theme quản trị Filament độc lập với frontend.

Không có bước biên dịch SCSS của dự án. Bootstrap dùng CSS có sẵn. Bộ sinh bảng màu nhỏ nằm ở `build/frontend-brand.mjs`, chạy trong bước PostCSS sẵn có của Vite, không cài thêm thư viện và không chạy JavaScript đổi màu trên trình duyệt.

## Kiểm tra trước khi giao

```sh
pnpm test:brand
pnpm build
```

Kiểm tra thêm các test Laravel bằng database kiểm thử riêng và xem trang chủ, trang con, trạng thái hover/focus, desktop/mobile. Bộ test màu thử nhiều màu chính sáng/tối và kiểm tra RGB, màu nhấn độc lập, tương phản chữ tối thiểu 4.5:1.

Đổi màu/font là thay cấu hình rồi build lại; không phải sửa lần lượt từng trang. Giao diện, dữ liệu CMS và tài sản nhận diện của khách hàng vẫn là phần cần cấu hình khi clone, không được ghi đè dữ liệu thật bằng reseed.
