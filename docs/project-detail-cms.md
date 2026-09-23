# Quản trị nội dung chi tiết dự án

Vào **Nội dung website → Dự án → Chỉnh sửa**. Bố cục ngoài website giữ cố định; nội dung từng khối được lưu theo từng dự án trong `projects.details`, cùng các trường hồ sơ và gallery hiện có.

- Cột trái: hồ sơ, tổng quan, thách thức, giải pháp, hạng mục, tiêu đề ảnh thi công, kết quả, nhận xét, dự án liên quan và SEO.
- Cột phải: phân loại, trạng thái, ảnh đại diện, banner, gallery, ảnh thi công, ảnh giải pháp và ảnh nhận xét.
- Danh sách có thể thêm, xóa và kéo thả đổi thứ tự. Các thẻ có thể chọn icon hoặc ảnh thay icon.
- Banner riêng, không dùng ảnh SEO thay thế. Khối chưa có dữ liệu không dùng nội dung mẫu dự phòng.
- Dự án liên quan chỉ hiển thị các dự án đã chọn và đã xuất bản, tối đa 4 dự án.

## Nâng cấp server đang có dữ liệu

Sau khi cập nhật mã nguồn và dependencies, chạy:

```sh
php artisan migrate --force
pnpm run build
php artisan optimize:clear
```

Không chạy `migrate:fresh` trên server đang có dữ liệu.

Nếu cần đưa bộ nội dung minh họa vào bản ghi **Dự án công trình mẫu** đang có:

```sh
php artisan db:seed --class=ProjectDetailContentSeeder --force
```

Seeder chỉ chạy khi bản ghi mẫu tồn tại và `details` còn null. Không ghi đè nội dung chi tiết đã lưu trong quản trị. Seeder này tạo thêm 4 bản ghi dự án minh họa đã xuất bản để trình bày khối liên quan; không chạy nếu server không cần dữ liệu minh họa. Các ảnh được lấy từ bộ ảnh mẫu trong repository và đăng ký vào Curator.

DatabaseSeeder đã gọi ProjectSeeder, và ProjectSeeder gọi ProjectDetailContentSeeder; database mới được seed đầy đủ bằng luồng chung.
