# Checklist hoàn thiện hệ sinh thái BNI

Ngày đối chiếu: 31/08/2026
Checkout: `D:\laragon\www\thtmedia-laravel`
URL kiểm tra: `https://thtmedia-laravel.test`

Checklist này là tiêu chí nghiệm thu. Chỉ đánh dấu hoàn thành sau khi code, migration, quyền, HTTP, test và giao diện tương ứng đã được kiểm tra.

## Website sự kiện BNI

- [x] Website riêng tại `/le-chuyen-giao`, giữ nhận diện đỏ/trắng BNI.
- [x] Nội dung sự kiện, chapter, lịch trình, hoạt động, video, tin tức và ảnh lấy từ CMS.
- [x] Có điều hướng riêng trên desktop và thanh điều hướng riêng trên điện thoại.
- [x] Không làm thay đổi PWA/giao diện của phần hệ sinh thái THT ngoài BNI.

## PWA và giao diện điện thoại

- [x] Manifest riêng, icon riêng và scope riêng cho `/le-chuyen-giao`.
- [x] Service Worker chỉ đăng ký trong phần BNI, không chặn POST/RSVP/upload/bình luận.
- [x] Thanh điều hướng điện thoại có Tổng quan, lịch trình/RSVP, thư mời, hình ảnh và cài app.
- [x] Form trên điện thoại có kích thước nhập liệu phù hợp, hỗ trợ safe-area.

## Thư mời

- [x] Thư mời chung hiển thị mặc định “Anh/Chị chủ doanh nghiệp”.
- [x] Mẫu nội dung chung được quản lý tập trung trong BNI Admin.
- [x] Thư mời riêng có tên khách theo từng record và đầu mối liên hệ theo Chapter.
- [x] Chapter Admin chỉ tạo/xem/sửa thư mời thuộc Chapter của mình.
- [x] RSVP thư mời chung và thư mời riêng được lưu vào database.

## Landing page Pickleball

- [x] Countdown lấy thời gian bắt đầu từ sự kiện trong CMS.
- [x] Cơ cấu giải thưởng có danh sách quản trị, sắp xếp và trạng thái nhấn mạnh.
- [x] Thể lệ giải đấu dùng nội dung Rich Editor trong quản trị.
- [x] RSVP có họ tên, điện thoại, email, Chapter, đội, trình độ và ghi chú.
- [x] Lịch thi đấu/kết quả lấy từ lịch trình sự kiện.
- [x] Không tự tạo giá trị giải thưởng hoặc thể lệ chưa được Ban tổ chức xác nhận.

## Thư viện ảnh cộng đồng

- [x] Admin và Chapter Admin có thể đăng ảnh; Chapter Admin bị giới hạn đúng Chapter.
- [x] Quản trị có upload nhiều ảnh dạng grid và hành động xóa tất cả trước khi lưu.
- [x] Cá nhân có thể gửi tối đa 8 ảnh/lần từ frontend.
- [x] Ảnh cá nhân ở trạng thái chờ duyệt và không hiển thị công khai trước khi duyệt.
- [x] Có trang thư viện, bộ lọc sự kiện/Chapter và trang xem riêng cho từng ảnh.
- [x] Khách có thể gửi bình luận; chỉ bình luận đã duyệt mới hiển thị.
- [x] BNI Admin và Chapter Admin có màn hình kiểm duyệt bình luận ảnh tương ứng quyền.

## Quản trị và phân quyền

- [x] BNI Admin quản lý toàn bộ sự kiện, Chapter, thư mời, RSVP, bài viết, ảnh, bình luận và hội viên.
- [x] Chapter Admin chỉ truy cập nội dung thuộc Chapter: liên hệ, thư mời, RSVP, bài viết, ảnh và bình luận.
- [x] Dashboard BNI hiển thị số khách mời, RSVP, ảnh chờ duyệt và bình luận chờ duyệt.
- [x] Form Filament 5 dùng đúng Schema/Layout/Action namespace và bố cục dự án.

## Kiểm tra kỹ thuật và giao diện

- [x] Migration chạy thành công trên database hiện tại.
- [x] PHP lint, Blade cache, Vite build và `git diff --check` đạt.
- [x] Test tập trung BNI đạt.
- [x] Toàn bộ test suite đạt hoặc lỗi ngoài phạm vi được ghi rõ.
- [x] HTTP HTTPS Laragon trả về đúng nội dung các route BNI chính.
- [x] Đã kiểm tra trực quan desktop bằng Chrome: bảng giá, trang BNI, thư mời, admin BNI và admin bảng giá đều render đúng, không tràn ngang hoặc có ảnh hỏng.
- [ ] Kiểm tra trực quan riêng ở viewport điện thoại thật; CSS responsive và PWA đã có test tự động nhưng phiên Chrome hiện tại không hỗ trợ đổi viewport.
- [x] Payload Git được rà an toàn, không đưa runtime media hoặc thay đổi ngoài phạm vi vào commit.

## Bằng chứng đối chiếu

- Trạng thái hiện tại: đã duyệt trực quan desktop; còn một vòng đối chiếu riêng trên viewport điện thoại thật.
- Migration `2026_08_31_000100_complete_bni_gallery_workflow` đã chạy thành công trên database Laragon hiện tại.
- Nhóm test BNI: **20 test, 159 assertion**, tất cả đạt.
- Toàn bộ dự án: **70 test, 542 assertion**, tất cả đạt.
- `php -l`, Pint, Blade cache, Vite build, `node --check public/bni-sw.js` và `git diff --check` đều đạt.
- HTTPS trả `200` và đúng marker nội dung tại `/le-chuyen-giao`, `/le-chuyen-giao/pickleball`, `/le-chuyen-giao/thu-vien-anh`, `/bni-manifest` và `/bni-admin/login`.
- Runtime upload nằm trong `storage/app/public/media/bni` và đã bị Git ignore. Không có secret/credential trong tập file BNI được rà.
- Chrome xác nhận 4 chapter và 4 bài BNI được đọc từ database. Database hiện có **0 ảnh gallery đã duyệt** và chưa gắn media cho sự kiện/chapter; giao diện hiển thị trạng thái thiếu dữ liệu thay vì dùng ảnh giả.
