# Rà soát và kế hoạch hoàn thiện landing/media Laravel

Ngày: 08/09/2026. Trạng thái: đề xuất để duyệt, chưa triển khai thay đổi.
Phạm vi đã kiểm tra: source và database local `thtmedia-laravel`. Chưa kiểm chứng filesystem, database hoặc cấu hình web server production.

## Kết luận hiện trạng

Landing đã dùng Laravel để chạy trang, nhưng chưa hoàn thiện quản trị media và triển khai theo cùng một quy trình. Không cần viết lại giao diện; cần hoàn tất lớp dữ liệu và vận hành còn thiếu.

| Phần | Hiện trạng đã xác minh |
| --- | --- |
| Trang và route | `LandingPage`, `Service`, controller/presenter, Blade, Vite và `HasSlug` với bảng `slugs` đã có. Local có 15 template và 9 landing page. |
| Nội dung | `landing_content` nằm trong database. Presenter đọc database, không đọc JSON seeder hoặc chạy WordPress khi phục vụ trang. |
| Media TikTok | 111 đường dẫn ảnh ban đầu; 0 giá trị `image_media_id` trong nội dung hiện tại; 0 bản ghi Curator có đường dẫn trong thư mục TikTok. Form cho chọn ảnh thay thế, nhưng ảnh gốc vẫn là đường dẫn ẩn. |
| Media branding | 9 đường dẫn ảnh; 0 `image_media_id` trong nội dung hiện tại; 0 bản ghi Curator có đường dẫn trong thư mục branding. Chưa có bộ form riêng quản lý đầy đủ nội dung branding như TikTok. |
| Thư viện | Local có 1.674 bản ghi Curator và 5 bản ghi Spatie Media Library. Không được coi file không nằm trong Curator là file không sử dụng. |
| Đường dẫn cũ | Quét 347 cột kiểu chuỗi/JSON: `landing_pages.landing_content` có 6 bản ghi và `services.landing_content` có 4 bản ghi chứa `wp-uploads`. |
| File `wp-uploads` | 6 file, 53.515.251 byte; mỗi file đều có bản SHA-256 giống hệt tại `media/library/`. Vẫn có tham chiếu nên chưa xóa. |
| Gallery | Blade hậu trường Academy, hậu trường phim doanh nghiệp và showcase Academy còn dùng `glob()` đọc thư mục. Các ảnh này có thể không được liệt kê trực tiếp trong database. |
| Tham chiếu ngoài | Partial CTA communications còn chứa URL icon `/wp-content/uploads/`; cần kiểm tra khả năng được render và loại bỏ literal cũ. |
| Seeder | TikTok tạo lần đầu và giữ nội dung khi chạy lại. `LandingContentSeeder` chưa có cùng cam kết: có nhánh khởi tạo lại khi sections rỗng và nhánh xóa body/settings cũ. Không dùng seeder tổng như lệnh sửa production tùy tiện. |
| Production | Người dùng đã xác nhận trang lên sau khi nhập database; log tiếp theo là ảnh `/storage/...` trả 403. Chưa có bằng chứng để kết luận do quyền filesystem, symlink hay rule web server. |

## Các file TikTok trong storage có tác dụng gì?

- `storage/app/tiktok-media.zip`: gói 111 ảnh dùng để chuyển từ local lên server, 18.940.297 byte, khoảng 18,1 MiB. Website không giải nén hoặc đọc ZIP khi chạy.
- `storage/app/tiktok-media-manifest.json`: bảng đối chiếu tên file và checksum nguồn/đích phục vụ chuyển đổi, không phải dữ liệu CMS dùng khi render.
- `storage/app/verify-tiktok-media.php`: script kiểm tra một lần, còn phụ thuộc đường dẫn nguồn WordPress local; không phù hợp làm công cụ kiểm tra production lâu dài.
- `storage/app/public/media/landing/pages/tiktok/`: file ảnh thực tế đang phục vụ trang. Xóa ZIP không xóa ảnh, nhưng xóa thư mục này sẽ làm hỏng ảnh hiện tại.

ZIP chuyển dữ liệu không phải vấn đề về kiến trúc tự thân. Vấn đề hiện tại là việc phát hành còn phụ thuộc các gói và thao tác thủ công riêng từng trang, chưa có cơ chế kiểm tra đủ code, dữ liệu và media.

## Thiết kế đích

1. Tiếp tục dùng Curator làm thư viện media của landing; không tạo một thư viện TikTok riêng. Tận dụng dịch vụ URL hiện có, chỉ chuẩn hóa những phần thực sự thiếu.
2. Ảnh và video lưu cục bộ được tham chiếu bằng ID media. Video TikTok bên ngoài giữ loại nguồn và URL/player ID được kiểm tra hợp lệ; thumbnail vẫn thuộc Curator.
3. Nội dung theo block vẫn có thể nằm trong JSON database. JSON là dữ liệu CMS hợp lệ khi có schema, validation và editor; không cần tạo một bảng cho từng câu hoặc từng block chỉ để gọi là native.
4. Một bộ xử lý media dùng chung cho nội dung landing và service; bỏ cơ chế riêng “đường dẫn gốc + ảnh thay thế” sau khi chuyển đổi xong.
5. Gallery lưu danh sách media và thứ tự trong database; presenter chuẩn bị dữ liệu, Blade chỉ render.
6. Asset giao diện cố định như icon/SVG được version trong source; ảnh/video nội dung được quản lý qua thư viện. Font, màu, header/footer tiếp tục dùng hệ thống chung và cấu hình trang hiện có.
7. Giữ nguyên slug public, nội dung, giá, ưu đãi TikTok và giao diện đang được duyệt. Chuyển đổi media không tự resize ảnh hoặc thay nội dung.

## Thứ tự triển khai đề xuất

### 1. Lập danh mục sử dụng và khóa phạm vi chuyển đổi

- Kiểm kê file, checksum, kích thước, MIME; đối chiếu Curator, Spatie, các quan hệ ID, JSON, HTML/srcset, settings, seed snapshot, CSS và gallery đang quét thư mục.
- Phân biệt file trùng byte, ảnh biến thể/crop và file chưa xác định chỗ dùng. Không suy ra “không được Curator đăng ký” đồng nghĩa với “được xóa”.
- Lưu ánh xạ trước/sau và bản sao dữ liệu cần phục hồi ngoài thư mục public. Công cụ chính thức có chế độ chỉ báo cáo; không giữ script ad hoc trong storage.

### 2. Chuẩn hóa media, làm TikTok và branding trước

- Đăng ký media hiện có vào Curator tại chỗ khi phù hợp, không copy thêm toàn bộ bộ ảnh và không hardcode ID từ database local.
- Tra checksum để tái sử dụng file vật lý đã có. Khi hai bản ghi có metadata/quyền/curations khác nhau, kiểm tra riêng trước khi gộp bản ghi.
- Chuyển nội dung sang ID, bổ sung truy xuất media chung, cập nhật schema và bỏ các trường fallback đường dẫn sau khi xác nhận đủ ảnh.
- Sáu file `wp-uploads` chuyển sang bản chuẩn đang có trong library. Cập nhật cả tham chiếu hiện tại và dữ liệu khởi tạo để chạy lại không sinh đường dẫn cũ.
- Với URL ảnh cũ từng được công khai, xác định nhu cầu redirect/alias trước khi xóa file; không chỉ kiểm tra database hiện tại.

### 3. Hoàn thiện CMS cho các landing còn lại

- Bổ sung editor cho từng vùng nội dung theo thứ tự hiển thị, đặc biệt branding và gallery Academy/phim doanh nghiệp.
- Dùng dữ liệu dịch vụ, dự án, bảng giá và liên hệ hiện có khi chúng chính là nguồn nội dung; chỉ giữ dữ liệu riêng khi thuộc chiến dịch.
- Chuyển gallery đang `glob()` sang danh sách media có thứ tự trong database; bỏ việc Blade truy cập filesystem.
- Kiểm tra từng trường editor được presenter/view sử dụng thực sự; sửa xong lưu và mở lại phải giữ dữ liệu.
- Giữ hoạt động của 5 media Spatie hiện hữu. Việc chuyển toàn bộ module BNI sang Curator không tự động nằm trong đợt này.

### 4. Chuẩn hóa phát hành lên production

- Một quy trình được ghi trong repository: kiểm tra phiên bản code, dependency, migration, nâng cấp dữ liệu landing, media, quyền admin, cache và HTTP.
- Dữ liệu nâng cấp có phiên bản, chạy lại an toàn, không ghi đè phần người quản trị đã sửa. Tách khởi tạo mới khỏi sửa đổi dữ liệu đang tồn tại.
- Media được đồng bộ với manifest/checksum; dùng khóa media ổn định hoặc ánh xạ ID khi chuyển giữa hai database. Không yêu cầu upload đè toàn bộ database production để thêm một landing.
- Nếu phải dùng file đóng gói để chuyển server, tạo gói theo cùng quy trình phát hành và đặt ngoài runtime; tránh mỗi trang một ZIP làm thủ công.
- Kiểm tra `public/storage`, file tồn tại, quyền đọc/traverse, cấu hình web server và HTTP ảnh/video. Có ảnh trong database không đủ để kết luận triển khai thành công.
- Thay script verify phụ thuộc thư mục WordPress bằng kiểm tra file/checksum/reference độc lập với nguồn cũ.

### 5. Dọn dữ liệu sau khi kiểm chứng

- Báo cáo checksum trước đó: 967 nhóm trùng, 2.262 file trong nhóm, 1.295 bản sao dư về mặt byte, tối đa khoảng 523 MiB. Đây chưa phải danh sách được phép xóa toàn bộ.
- Chỉ xóa bản sao sau khi ánh xạ đã áp dụng, mọi nơi sử dụng đã chuyển và kiểm tra HTTP đạt. Không xóa thumbnail/crop chỉ vì tên gần giống.
- Xóa `wp-uploads` sau khi hết phụ thuộc. Xóa ZIP, manifest tạm và script cũ sau khi quy trình thay thế đã có và việc chuyển media được xác nhận.
- Có báo cáo số file/dung lượng thực tế đã dọn và cách phục hồi. Local và production được kiểm chứng độc lập.

## Điều kiện nghiệm thu

- TikTok và branding chọn/sửa toàn bộ ảnh trong Curator; không còn ảnh gốc ẩn ngoài thư viện làm fallback thường xuyên.
- Tất cả gallery trong phạm vi có editor và thứ tự trong database; Blade không quét thư mục để lấy nội dung.
- Các landing trong phạm vi không còn phụ thuộc `wp-uploads`, hotlink WordPress hoặc script nguồn WordPress khi chạy và khi triển khai.
- Các URL public, form tư vấn, slug duy nhất, bảng giá, bộ lọc, video và nội dung ưu đãi giữ đúng hành vi.
- Chuyển đổi chạy hai lần không nhân đôi media, không mất nội dung, không ghi đè chỉnh sửa CMS; có bài kiểm tra các tình huống này.
- Build bằng pnpm và kiểm tra desktop/mobile đạt. Trên production, URL ảnh trả 200 và video phục vụ thành công cả request thường/range phù hợp.
- Hoàn tất trên một bản database staging không cần nhập đè toàn bộ database local; xác nhận lại quyền admin của tài khoản thực tế.
- Không kết luận hoàn thành production chỉ từ commit/push hoặc kết quả local.

## Bằng chứng trong source

- `app/Support/Landing/LandingPresenter.php`: đọc nội dung database, ánh xạ đường dẫn và gọi resolver riêng TikTok.
- `app/Support/Landing/TiktokLandingContent.php`: ảnh ID có fallback sang trường đường dẫn.
- `app/Filament/Resources/LandingPages/Schemas/TiktokLandingSchema.php`: trường ảnh gốc Hidden và ảnh thay thế Curator.
- `app/Support/Landing/LandingRegistry.php`: tự ghép đường dẫn tương đối vào media/landing/pages.
- `resources/views/frontend/landing/parts/academy/bts.blade.php`, `academy/showcase.blade.php`, `corporate-film/bts.blade.php`: truy cập filesystem khi render.
- `database/seeders/LandingContentSeeder.php`: các nhánh khởi tạo/normalize cần tách khỏi triển khai nội dung an toàn.
- `docs/TIKTOK_LANDING.md`: ghi nhận gói media chuyển thủ công; phần lệnh build còn thiếu bước cài dependency.

Đề xuất duyệt phạm vi: hoàn thiện quản trị và quy trình media cho hệ thống landing hiện hữu theo 5 bước trên; giữ giao diện, không mở rộng sang viết lại toàn bộ website hoặc BNI.
