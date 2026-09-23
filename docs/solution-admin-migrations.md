# Quản trị danh mục và giải pháp DVTEC

## Phạm vi

Hai cấp dữ liệu: `solution_categories` → `solutions.solution_category_id`. Danh mục là lĩnh vực kỹ thuật; giải pháp là bài toán kỹ thuật áp dụng cho một công trình. Không có `parent_id`, không có danh mục con. Ví dụ: danh mục **PCCC**, giải pháp **Giải pháp PCCC cho nhà xưởng, nhà máy**.

Lượt này tập trung quản trị Filament, model, phân quyền và migrations. Giữ nguyên `/giai-phap` và `/giai-phap/{solution}` để không làm gãy các liên kết, SEO và trang chủ đang dùng. Chưa triển khai trang danh mục công khai hay giao diện frontend mới; không thay cấu trúc URL trong lượt này.

## Cập nhật database đang sử dụng

Sao lưu database trước. Không xóa bảng và không xóa file migration đã chạy. Không dùng `migrate:fresh` trên database đang có dữ liệu.

```bash
git pull --ff-only origin main
php artisan down
php artisan migrate --force
php artisan db:seed --class=SolutionModuleSeeder --force
php artisan optimize:clear
php artisan up
```

Chạy lần lượt từng lệnh; nếu `migrate` hoặc seeder báo lỗi thì dừng, không tiếp tục `up` trước khi xử lý. Chạy lệnh từ thư mục dự án với đúng `.env` và bộ `vendor` hiện tại. Không cần cập nhật Composer/Node để sử dụng các thay đổi quản trị này; không có thay đổi dependency. Migration và seeder trên là thao tác chạy tại môi trường triển khai, không tự được chạy chỉ vì push Git.

Migrations mới giữ baseline `2026_09_22_120000_create_solutions_table.php`, tạo `solution_categories`, thêm khóa ngoại bắt buộc vào `solutions`, sau đó bỏ cột `short_title`. Bảng `slugs` dùng chung tiếp tục là nguồn slug; không thêm một cột slug thứ hai vào hai bảng nội dung.

Nếu đang có giải pháp cũ, migration giữ nguyên ID, tiêu đề, nội dung, media, thứ tự, các cờ và slug; đưa chúng vào danh mục **Chưa phân loại** đang ẩn. Các giải pháp này tạm thời không xuất hiện trên trang chủ, danh sách, chi tiết và sitemap cho đến khi chuyển sang danh mục công khai. Không tự suy đoán loại kỹ thuật cho nội dung cũ. Cột `short_title` được bỏ; rollback chỉ khôi phục cột rỗng, không khôi phục các giá trị tên ngắn đã bỏ. Dùng bản sao lưu nếu cần phục hồi các giá trị đó.

## Sử dụng admin

- **Nội dung website → Danh mục giải pháp**: thêm/sửa/xóa; tên, slug, mô tả, nội dung, ảnh, banner, SEO, công khai, thứ tự và số giải pháp.
- **Nội dung website → Giải pháp**: bắt buộc chọn danh mục; tiêu đề phải nói rõ kỹ thuật và đối tượng công trình; nội dung, hạng mục nổi bật, ảnh, banner, SEO, công khai và hiện trang chủ. Có lọc theo danh mục/công khai/trang chủ.
- Chỉ xóa được danh mục rỗng. Chuyển các giải pháp sang danh mục khác hoặc xóa từng giải pháp trước. Không cascade-delete nội dung. Model và foreign key cùng chặn xóa nhầm danh mục đang có bài.

`sort_order` do admin nhập hoặc kéo thả. Mặc định 0, số nhỏ đứng trước; không dùng `AssignNextOrderObserver` cho `Solution` và `SolutionCategory`. Các model khác giữ nguyên cơ chế hiện tại. Không coi `sort_order` là số thứ tự bắt buộc liên tục hay duy nhất.

`SolutionModuleSeeder` tạo 6 danh mục và bổ sung quyền hai resource cho `super_admin`. Chạy lại không ghi đè nội dung/danh mục đã sửa và không thu hồi quyền các module khác. Không tự tạo hoặc công khai giải pháp mẫu. `SolutionSeeder` cũ chỉ còn là lệnh tương thích để seed danh mục, không tái tạo các bài demo cũ.

6 danh mục khởi tạo: PCCC; HVAC & Thông gió; Điện công nghiệp; Điện nhẹ & Camera; Hạ tầng kỹ thuật; Cơ khí, trần & vách. `seed_key` chỉ là khóa nội bộ giúp seed lặp an toàn, không phải trường nhập của admin. Slug danh mục seed có tiền tố `giai-phap-` vì registry slug hiện tại duy nhất trên toàn website.

## Tương thích frontend

`Solution::published()` chỉ trả về giải pháp và danh mục đều công khai. Controller chi tiết cũng kiểm tra điều này. Model giữ accessor chỉ đọc `short_title` trả về `title` để template trang chủ cũ vẫn hoạt động sau khi bỏ cột, không lưu hoặc quản trị một tên loại công trình riêng nữa. Không thay markup/CSS/JavaScript trong lượt này.

## Kiểm tra

Chỉ chạy các lệnh kiểm thử trên database riêng (ví dụ `dvtec_testing`), tuyệt đối không dùng database production. Test migration thực thi DDL, tạo/xóa schema bằng `DatabaseMigrations`, nên chạy ở process riêng, không dùng transaction của `RefreshDatabase`.

Khối Bash bên dưới yêu cầu đã export `APP_ENV=testing` và `DB_DATABASE=dvtec_testing` trong môi trường test riêng. Kiểm tra cả host và thông tin kết nối trỏ tới database dùng để kiểm thử. Guard đặt trước tất cả lệnh kiểm thử vì bản thân test migration cũng có thể xóa bảng. Subshell `set -eu` dừng ngay khi điều kiện hoặc một lệnh thất bại, kể cả khi dán khối này vào terminal không bật `set -e`.

```bash
(
    set -eu
    test "${APP_ENV:-}" = testing
    test "${DB_DATABASE:-}" = dvtec_testing

    # Loại cấu hình cache cũ trước khi boot với kết nối của môi trường test.
    APP_TESTING_HTTP=false php artisan config:clear
    APP_TESTING_HTTP=false vendor/bin/phpunit tests/Integration/SolutionMigrationTest.php

    # Chỉ reset database test: migration settings cũ không có down().
    APP_TESTING_HTTP=false php artisan migrate:fresh --force
    APP_TESTING_HTTP=false php artisan db:seed --force
    APP_TESTING_HTTP=true vendor/bin/phpunit --filter 'Solution(Admin|CategoryAdmin|Feature)Test|ContentNavigationTest'
    APP_TESTING_HTTP=true vendor/bin/phpunit
)
```

Không dán nguyên khối Bash này vào Windows PowerShell. Với PowerShell, kiểm tra môi trường test trước mọi lệnh, đặt `$env:APP_TESTING_HTTP = 'false'` hoặc `'true'` trước lệnh tương ứng, và kiểm tra `$LASTEXITCODE` sau từng lệnh để dừng khi thất bại. Bộ test HTTP sử dụng `Tests\TestCase` và fixtures của repo; suite toàn bộ cần assets đã build. Workflow `Solution admin and migration checks` tự tạo MySQL test riêng, chạy migration mới/cũ/rollback, bộ test module, rồi suite PHP toàn bộ. Workflow kiểm tra frontend/Sass hiện hữu không bị thay đổi.

Rollback cấu trúc trong môi trường test/đã có sao lưu: rollback migration liên kết `solutions` trước, migration tạo danh mục sau. Mất phân loại/thiết lập danh mục là hệ quả của rollback; nội dung giải pháp và slug của giải pháp vẫn còn. Sau khi rollback phải dùng code tương ứng với schema cũ.
