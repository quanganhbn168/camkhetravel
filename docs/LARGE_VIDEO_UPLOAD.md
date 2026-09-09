# Tăng giới hạn upload video gần 1 GB

Hướng dẫn cho thtmedia-laravel, kiểm tra mã nguồn ngày 09/09/2026. Đây là hướng dẫn; chưa áp dụng các thay đổi upload vào code hay server.

## Hiện trạng đã kiểm tra

- Curator (thư viện media và CuratorPicker): mặc định 5000 KB, chưa có override trong ứng dụng.
- Livewire: mặc định 12288 KB và thời hạn upload 5 phút; chưa có config/livewire.php.
- PHP CLI local: upload_max_filesize=2G, post_max_size=2G, max_input_time=60. File đang nạp: D:/laragon/bin/php/php-8.3.20-nts-Win32-vs16-x64/php.ini. PHP phục vụ web và PHP trên server có thể dùng cấu hình khác.
- Chưa xác minh Nginx, PHP-FPM hoặc CDN của server.

Mốc minh họa: cho phép 1 file tối đa 1536 MiB, dư cho video gần 1 GB. Upload từng video; tổng request vẫn phải nằm dưới 2 GiB.

## 1. Code Laravel: Livewire

Từ thư mục dự án, xuất cấu hình (không thêm --force nếu file đã có):

```bash
php artisan vendor:publish --tag=livewire:config
```

Trong `config/livewire.php`, tìm `temporary_file_upload` và sửa hai khóa sau, giữ nguyên các khóa khác:

```php
'rules' => ['required', 'file', 'max:1572864'], // KB: 1536 MiB
'max_upload_time' => 60, // phút
```

## 2. Code Laravel: Curator

Trong `app/Providers/AppServiceProvider.php`, thêm imports:

```php
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Facades\Curator;
```

Thêm vào `boot()`, giữ các xử lý đang có:

```php
Curator::configure()->maxSize(1572864);
CuratorPicker::configureUsing(
    fn (CuratorPicker $picker) => $picker->maxSize(1572864),
);
```

Dòng đầu áp dụng cho upload trong Thư viện media; dòng thứ hai áp dụng cho popup upload trong các trường chọn media. Các giới hạn riêng trên từng trường vẫn có thể ghi đè. Giới hạn này áp dụng cho các định dạng đã được phép trong media; không thay đổi acceptedFileTypes. Không sửa file trong vendor. FileUpload khác ngoài Curator cần kiểm tra giới hạn riêng.

## 3. PHP phục vụ website

Trên aaPanel, mở cấu hình của đúng phiên bản PHP mà website đang sử dụng (xem phiên bản PHP được gán cho website trước). Chỉnh php.ini của PHP-FPM đó:

```ini
upload_max_filesize = 1536M
post_max_size = 2G
max_input_time = 3600
max_execution_time = 3600
```

Nếu cấu hình hiện tại đã cao hơn thì giữ giá trị cao hơn. Sau khi lưu, restart/reload đúng dịch vụ PHP. Trên Laragon local, sửa php.ini của phiên bản PHP đang chạy và restart dịch vụ web/PHP. `php --ini` chỉ xác nhận cấu hình CLI, không chứng minh cấu hình FPM.

`post_max_size` phải lớn hơn kích thước một file vì còn dữ liệu multipart. `max_input_time` liên quan thời gian nhận dữ liệu. Không cần tăng RAM lên bằng kích thước video chỉ để lưu video theo luồng; kiểm tra riêng nếu có bước xử lý ngốn bộ nhớ.

Nguồn: [PHP upload](https://www.php.net/manual/en/features.file-upload.post-method.php), [PHP cấu hình](https://www.php.net/manual/en/ini.core.php), [giới hạn thời gian upload](https://www.php.net/manual/en/features.file-upload.common-pitfalls.php).

## 4. Nginx, nếu server dùng Nginx

Trong aaPanel, mở cấu hình Nginx của đúng website. Trong block `server { ... }`, cập nhật directive hiện có hoặc thêm nếu chưa có:

```nginx
client_max_body_size 2g;
client_body_timeout 3600s;
```

Kiểm tra các block location upload không có giới hạn nhỏ hơn. Kiểm tra cấu hình trước khi reload Nginx, ví dụ `nginx -t` bằng đúng binary của server. `client_body_timeout` là thời gian giữa hai lần đọc dữ liệu, không phải tổng thời gian upload. Nếu báo 504 trong giai đoạn PHP xử lý sau upload, kiểm tra thêm `fastcgi_read_timeout` và PHP-FPM `request_terminate_timeout` theo log, không chỉ tăng giới hạn dung lượng.

Nguồn: [Nginx client_max_body_size và client_body_timeout](https://nginx.org/en/docs/http/ngx_http_core_module.html#client_max_body_size).

## 5. Áp dụng và kiểm tra

Sau khi đưa thay đổi code lên server:

```bash
php artisan config:cache
```

Đăng nhập lại/tải lại trang admin để lấy URL upload mới. Upload một video nhỏ trước, sau đó video gần 1 GB. Kiểm tra file xuất hiện trong thư viện, dung lượng lưu đúng và phát được. Đĩa chứa file tạm của Nginx/PHP/Livewire và thư viện phải còn đủ dung lượng; một file có thể tồn tại ở nhiều chỗ trong lúc chuyển.

Nếu có Cloudflare proxy, giới hạn request của gói vẫn áp dụng. Free/Pro giới hạn 100 MB nên video gần 1 GB không thể gửi trong một request qua proxy đó, dù PHP/Nginx đã tăng. Khi ấy cần thiết kế upload chia nhỏ hoặc upload trực tiếp vào object storage rồi lưu media vào CMS. Chỉ đưa file lên FileZilla chưa tự tạo bản ghi trong thư viện Curator.

Nguồn: [Cloudflare lỗi 413 và giới hạn upload](https://developers.cloudflare.com/support/troubleshooting/http-status-codes/4xx-client-error/error-413/).

- Báo quá số KB: kiểm tra Curator/Livewire.
- 413: kiểm tra Nginx/proxy/CDN và log nguồn trả lỗi.
- 422: đọc lỗi validation; có thể do file quá lớn, sai loại hoặc PHP không nhận đủ upload.
- Hết hạn/401 sau chờ lâu: kiểm tra URL tạm Livewire và thời hạn upload.
- 500/504: đọc log PHP-FPM/Laravel/Nginx để xác định timeout, ổ đĩa hoặc bước xử lý.
