# Giải pháp độc lập và tổ chức nội dung website

## Mục tiêu đã thống nhất

- Trang chủ lấy ảnh Dịch vụ từ chính danh mục, hiển thị rõ các dịch vụ thuộc danh mục với liên kết có mũi tên Font Awesome và hover/focus.
- Giải pháp là nội dung độc lập, không thuộc Dịch vụ, không có danh mục giải pháp.
- Liên kết Giải pháp luôn tới phần Giải pháp; tuyệt đối không dùng route Dịch vụ.
- Sắp xếp nhóm Nội dung website trong Filament dễ tìm, nội dung và danh mục tương ứng đứng cạnh nhau.

## Hiện trạng đã kiểm tra

- Database đang kết nối là `dvtec`: có `services`, `service_categories`; chưa có `solutions` hoặc `solution_categories`.
- Chưa có model, resource hay create migration cho Giải pháp.
- `HomeController::solutions()` trả mảng viết cứng. Ảnh Giải pháp đang lấy từ Dịch vụ hoặc nguồn khác.
- `/giai-phap` đã tồn tại, dùng `SolutionsController` và hồ sơ trang hệ thống; chưa có danh sách dữ liệu Giải pháp.
- Một số navigationSort trong Nội dung website trùng nhau. Yêu cầu tư vấn đang nằm nhầm trong nhóm nội dung.

## Dịch vụ trên trang chủ

- Giữ tiêu chí chọn danh mục và dịch vụ hiện hành, không mở rộng sang bản nháp hay dữ liệu chưa được chọn hiện trang chủ.
- Eager load ảnh danh mục; lấy ảnh từ `ServiceCategory.curatorMedia`, không dùng ảnh dịch vụ thay thế.
- Có ảnh thì hiện, không có thì bỏ ảnh, không sinh ảnh hoặc ảnh dự phòng.
- Mỗi dịch vụ là một liên kết cả dòng, tiêu đề rõ, kèm `fa-solid fa-arrow-right` trang trí có `aria-hidden`.
- Hover và focus-visible có trạng thái tương đương; chuyển động ngắn, tôn trọng reduced-motion.
- CSS riêng cho danh sách dịch vụ, không sửa toàn bộ `.check-list` trên website.

## Module Giải pháp

### Dữ liệu và quản trị

- Thêm create migration `solutions`, model Solution và resource Filament tương ứng. Không tạo SolutionCategory hoặc quan hệ tới ServiceCategory.
- Các trường: tiêu đề, nhãn ngắn dùng cho tab trang chủ (không bắt buộc), mô tả ngắn, nội dung, các điểm nổi bật dạng danh sách, ảnh đại diện, banner, ảnh chia sẻ riêng, tiêu đề SEO, mô tả SEO, trạng thái công khai, chọn hiện trang chủ, thứ tự, timestamps.
- Slug dùng cơ chế nội dung hiện có trong dự án; không thêm bảng slug mới, không gán slug động cho trang hệ thống `/giai-phap`.
- Ảnh đều liên kết Curator. Ảnh đại diện, banner và ảnh chia sẻ là ba vai trò độc lập, không mượn ảnh Dịch vụ.
- Form chuẩn 2:1: nội dung và RichEditor bên trái, trạng thái/thứ tự/ảnh bên phải; SEO bên dưới phần nội dung. Không thêm icon hay helptext trang trí cho section.
- Bảng có tiêu đề copyable, ảnh, thứ tự, toggle công khai và hiện trang chủ; không có cột slug.
- Tích hợp quyền theo cách module hiện tại sử dụng Filament Shield; không tự mở quyền cho tất cả vai trò.

### Giao diện và route

- `/giai-phap` giữ route `solutions.index` và hồ sơ trang hệ thống hiện có, bổ sung danh sách Giải pháp công khai.
- Chi tiết dùng `/giai-phap/{solution}` với route riêng `solutions.show`; route này phải được khai báo và kiểm tra không xung đột với route slug chung.
- Trang chủ lấy các Solution công khai, được chọn hiện trang chủ, sắp xếp theo thứ tự và id.
- Giữ bố cục tab Giải pháp hiện tại; mỗi tab đại diện một Solution, dùng ảnh/mô tả/điểm nổi bật của chính bản ghi đó. Điểm nổi bật là nội dung văn bản, không giả làm dịch vụ có liên kết.
- Liên kết xem chi tiết tới `solutions.show`; liên kết xem tất cả tới `solutions.index`.
- Khi chưa có Solution phù hợp, không hiển thị khối rỗng hoặc dùng mảng viết cứng thay thế. Trang danh sách vẫn có trạng thái chưa có nội dung.
- Bản nháp không được truy cập công khai; URL không hợp lệ trả 404.
- Controller chuẩn bị dữ liệu, Blade chỉ hiển thị; SEO/canonical/OG theo chủ thể Giải pháp, không dùng chủ thể Dịch vụ.

## Menu Filament

Thứ tự trong nhóm Nội dung website, mỗi mục có navigationSort riêng:

1. Bài giới thiệu.
2. Dịch vụ.
3. Danh mục dịch vụ (đổi nhãn Nhóm dịch vụ để nhất quán).
4. Giải pháp.
5. Dự án.
6. Danh mục dự án.
7. Sản phẩm.
8. Danh mục sản phẩm.
9. Bài viết.
10. Danh mục bài viết (đổi nhãn Chuyên mục bài viết).
11. Thẻ nội dung.
12. Câu hỏi thường gặp.

Chuyển Yêu cầu tư vấn sang nhóm Khách hàng, cạnh Bình luận. Giữ các nhóm Trang chủ, SEO & media, Hệ thống và Cài đặt website; không nhân đôi các mục cấu hình trang.

## Migration, seeder và an toàn dữ liệu

- Chỉ thêm create migration cho bảng mới; nếu cần sửa schema cũ thì sửa trực tiếp create migration theo quy ước dự án, không thêm migration vá cột.
- Seeder Giải pháp dùng nội dung năm giải pháp đang viết cứng để chuẩn bị dữ liệu cơ bản cho fresh seed; dùng media có thật từ MediaSeeder, không bịa đường dẫn ảnh.
- Seeder chạy lại không ghi đè nội dung người dùng đã sửa. Bỏ mảng viết cứng và chuỗi ảnh dự phòng khỏi runtime frontend.
- Không chạy migrate:fresh trên database hiện tại. Khi triển khai, chỉ chạy migration tạo bảng mới và seeder Giải pháp có phạm vi rõ ràng sau khi kiểm tra trạng thái migration; không seed lại toàn website.
- Không Git, không tạo nhánh, không tạo sao lưu trong công việc này khi chưa được yêu cầu.

## Kiểm chứng dự kiến

- Test Dịch vụ lấy đúng ảnh danh mục dù dịch vụ có ảnh khác; không lấy ảnh dịch vụ khi danh mục thiếu ảnh.
- Test liên kết dịch vụ, trạng thái hiển thị, truy vấn không làm lộ bản nháp.
- Test CRUD Giải pháp, toggle, lọc trang chủ, thứ tự, quyền, route danh sách/chi tiết và 404 bản nháp.
- Test SEO/canonical/OG của Giải pháp độc lập; tất cả liên kết Giải pháp không dẫn sang Dịch vụ.
- Test menu đúng nhóm, thứ tự riêng, nhãn nhất quán; tiêu đề copyable và không có cột slug.
- Test migration/seed bằng database test riêng; xác nhận seed lặp không ghi đè chỉnh sửa.
- Build frontend, chạy regression hiện có; kiểm tra trình duyệt desktop/mobile, hover/focus và form Filament.

## Trạng thái

Đây là thiết kế chờ duyệt trước khi viết kế hoạch triển khai. Chưa thay đổi code ứng dụng hoặc dữ liệu.
