# Thiết kế trang hệ thống theo route và website đơn ngôn ngữ

**Ngày:** 2026-09-22

**Trạng thái:** Chờ anh duyệt

## Mục tiêu

DVTEC là website chỉ sử dụng tiếng Việt. URL của các trang hệ thống do named route của Laravel sở hữu; mỗi controller tiếp tục chịu trách nhiệm lấy dữ liệu nghiệp vụ và chuẩn bị dữ liệu hiển thị cho trang tương ứng.

Hệ thống không tạo model `Page`, bảng `pages`, controller trang dùng chung hoặc cơ chế điều phối route từ cơ sở dữ liệu. Một cấu hình cố định chỉ lưu hồ sơ trình bày cần thiết của từng route đã thống nhất: tiêu đề SEO, mô tả SEO, ảnh chia sẻ và banner trang.

## Tiêu chí hoàn thành

- Named route của Laravel là nguồn sự thật duy nhất đối với URL của trang hệ thống.
- Xóa `LocalizedUrl`, `LanguageCatalog`, `SetFrontendLocale` và các cấu trúc dữ liệu đa ngôn ngữ của website.
- Website công khai không còn bộ chọn ngôn ngữ, route có tiền tố ngôn ngữ, cơ chế fallback ngôn ngữ hoặc lớp bọc nội dung dịch.
- Bảng `slugs` chỉ lưu một slug duy nhất trên toàn hệ thống cho mỗi bản ghi và không còn cột `locale`.
- Sáu hồ sơ route gồm: Trang chủ, Giới thiệu, Dịch vụ, Giải pháp, Liên hệ và Dự án.
- Trang chủ tiếp tục dùng Hero Slide và không hiển thị thêm banner trang riêng.
- Năm trang bên trong có thể chọn banner riêng và fallback về banner chung của website.
- Mỗi hồ sơ route có thể chọn ảnh Open Graph riêng; ảnh này độc lập với banner và ảnh nội dung hiển thị trên trang.
- Menu tham chiếu trang hệ thống bằng route name.
- Chỉ tạo migration mới, không chỉnh sửa lịch sử migration hiện có.
- `php artisan migrate:fresh --seed` tạo được website tiếng Việt hoạt động với settings, menu, hồ sơ route và dữ liệu nghiệp vụ mẫu cơ bản hiện có.

## Ngoài phạm vi

- Không làm page builder hoặc cho quản trị viên tự tạo trang tùy ý.
- Không cho sửa đường dẫn của trang hệ thống từ cơ sở dữ liệu.
- Không giữ cơ chế chuyển ngôn ngữ hoặc lớp trừu tượng chuẩn bị cho đa ngôn ngữ về sau.
- Không thay nội dung do controller quản lý bằng nội dung trang dạng JSON.
- Không thay Hero Slide của Trang chủ bằng một banner đơn.
- Không thiết kế lại giao diện sáu trang ngoài việc nối hồ sơ route, SEO, ảnh chia sẻ và banner.
- Không xóa các bản dịch tiếng Việt mà Filament, Curator, Laravel validation hoặc thư viện quản trị cần sử dụng.

## Quyền sở hữu URL

Các named route sau là chủ sở hữu chính thức của sáu trang đã thống nhất:

| Route name | URI | Controller | Trường hồ sơ |
| --- | --- | --- | --- |
| `home` | `/` | `HomeController` | Tiêu đề SEO, mô tả SEO, ảnh chia sẻ |
| `about` | `/gioi-thieu` | `AboutController` | Tiêu đề SEO, mô tả SEO, ảnh chia sẻ, banner |
| `services.index` | `/dich-vu` | `ServiceController@index` | Tiêu đề SEO, mô tả SEO, ảnh chia sẻ, banner |
| `solutions.index` | `/giai-phap` | `SolutionsController` | Tiêu đề SEO, mô tả SEO, ảnh chia sẻ, banner |
| `contact` | `/lien-he` | `ContactController@index` | Tiêu đề SEO, mô tả SEO, ảnh chia sẻ, banner |
| `projects.index` | `/du-an` | `ProjectController@index` | Tiêu đề SEO, mô tả SEO, ảnh chia sẻ, banner |

Các route sản phẩm, blog, tìm kiếm, danh mục, chi tiết, gửi biểu mẫu, robots, sitemap và favicon vẫn là route Laravel khai báo rõ ràng. Đợt thay đổi này không biến chúng thành hồ sơ route.

`SolutionsController` là controller chuyên biệt thông thường. Controller tổng hợp trang Giải pháp từ danh mục dịch vụ, dịch vụ đã xuất bản và dữ liệu CMS liên quan đang có. Controller không đọc nội dung Page dùng chung và không sao chép bản ghi dịch vụ.

## Danh mục route cố định

Một danh mục được quản lý trong code định nghĩa sáu khóa hồ sơ, nhãn hiển thị, route name, SEO mặc định, khả năng hỗ trợ banner, tần suất sitemap, độ ưu tiên sitemap và root slug dành riêng.

Danh mục này là cấu hình kỹ thuật, không phải model nội dung. Quản trị viên không thể thêm hoặc xóa định nghĩa route. Các thành phần cùng sử dụng một danh mục gồm:

- form settings;
- bộ phân giải hồ sơ route;
- bộ chọn nguồn của menu;
- bộ tạo sitemap;
- kiểm tra slug dành riêng;
- seeder và test.

Cách này tránh việc danh sách route bị lệch giữa nhiều mảng viết cứng nhưng vẫn giữ named route là chủ sở hữu URL.

## Settings hồ sơ route

`WebsiteSettings` có thêm mảng `public_pages`, dùng route name làm khóa. Mỗi mục cố định có cấu trúc:

```php
[
    'seo_title' => '',
    'seo_description' => '',
    'seo_image_media_id' => null,
    'banner_media_id' => null,
]
```

Hồ sơ `home` không có hoặc không sử dụng `banner_media_id` vì Hero Slide tiếp tục sở hữu phần hero của Trang chủ.

Trang quản trị settings tạo các section hoặc tab cố định từ danh mục route. Không dùng repeater và không cho phép tạo khóa tùy ý. Trường media sử dụng ID của Curator và chỉ chấp nhận bản ghi ảnh.

Một bộ phân giải chuyên trách nhận route name hợp lệ và trả về metadata đã chuẩn hóa cùng URL media đã được phân giải. Controller yêu cầu đúng hồ sơ của mình và vẫn chịu trách nhiệm với mọi dữ liệu khác.

Thứ tự fallback SEO:

1. Tiêu đề SEO, mô tả SEO và ảnh chia sẻ của hồ sơ route.
2. Tiêu đề hoặc mô tả dự phòng đang có tại controller, nếu có.
3. Tiêu đề SEO, mô tả SEO và ảnh chia sẻ mặc định của website.

Thứ tự fallback banner:

1. Banner riêng của một trong năm route trang bên trong.
2. `banner_media_id` dùng chung hiện có của website.
3. Nền CSS hiện có khi cả hai cấp đều không có ảnh.

Ảnh chia sẻ không fallback sang banner trang. Nó chỉ fallback theo chuỗi ảnh SEO.

## Luồng dữ liệu controller và view

Mỗi controller của trang hệ thống lấy hồ sơ theo route của mình, chuẩn bị dữ liệu nghiệp vụ hiện có, tạo SEO qua `FrontendSeoBuilder`, sau đó truyền `pageBannerUrl` cho view nếu route hỗ trợ banner.

- `HomeController` tiếp tục quản lý Hero Slide, settings trang chủ, dịch vụ, dự án, bài viết, FAQ và schema.
- `AboutController` tiếp tục quản lý `AboutSettings`, media, lịch sử, dịch vụ, số liệu và video.
- `ServiceController@index` tiếp tục quản lý danh sách dịch vụ, danh mục, quy trình, dự án, sắp xếp và phân trang.
- `SolutionsController` tổng hợp trang tổng quan từ dữ liệu nghiệp vụ dịch vụ đang có.
- `ContactController@index` tiếp tục quản lý thông tin liên hệ, bản đồ, biểu mẫu và chi nhánh.
- `ProjectController@index` tiếp tục quản lý danh sách dự án, danh mục, đối tác, sắp xếp và phân trang.

View chỉ hiển thị URL banner đã được controller truyền vào, không tự truy vấn settings hoặc media.

## Xóa `LocalizedUrl`

Toàn bộ nơi sử dụng `LocalizedUrl` được chuyển đổi trước khi xóa class.

- Liên kết trang hệ thống dùng `route('name')`.
- Nội dung động nằm ở root một đoạn dùng `route('slug.show', ['slug' => $model->slug])`.
- Dự án, sản phẩm, bài viết và danh mục dùng named route hiện có với tham số slug rõ ràng.
- Action POST dùng named route hiện có với ID đúng theo hợp đồng của route.
- Link xem trước trong quản trị, canonical, breadcrumb, card, header, footer, biểu mẫu và sitemap dùng cùng một hợp đồng named route.

URL generator của Laravel được cấu hình để lấy origin từ `APP_URL` khi tạo URL tuyệt đối. Việc này giữ host của canonical và sitemap ổn định ngay cả khi request gửi vào bằng `Host` khác.

Chỉ xóa class sau khi tìm kiếm toàn repository xác nhận không còn import, Blade `@use`, lời gọi tĩnh hoặc test tham chiếu tới nó.

## Xóa lớp đa ngôn ngữ của website

Website được chuyển thành đơn ngôn ngữ ở cấp cấu trúc, không chỉ là cấu hình một ngôn ngữ đang hoạt động.

Xóa:

- `LanguageCatalog`;
- `SetFrontendLocale`;
- `config/locales.php`;
- đăng ký language catalog trong provider;
- middleware locale khỏi public routes;
- nhánh xử lý theo locale trong `HasSlug`, `SlugObserver`, `PublicSlugController`, `FrontendSeoBuilder` và `AboutController`;
- lời gọi `__('site.*')` trên frontend và các từ điển thuộc ứng dụng tại `lang/en`, `lang/ko`, `lang/zh`, `lang/vi/site.php`;
- test chỉ dùng để kiểm tra chọn ngôn ngữ hoặc route có tiền tố locale.

Giữ:

- locale bắt buộc của Laravel được cố định là `vi`;
- thuộc tính ngôn ngữ HTML cố định là `vi`;
- Open Graph locale cố định là `vi_VN`;
- file dịch tiếng Việt của thư viện dùng cho Filament và Curator;
- cơ chế dịch của framework phục vụ validation và thông báo quản trị bằng tiếng Việt.

Các thuộc tính nội dung của `AboutSettings` đổi từ mảng theo locale thành chuỗi. Các giá trị `title` và `description` trong timeline cũng đổi từ mảng theo locale thành chuỗi. Đường dẫn field trên form đổi từ dạng `page_title.vi` thành `page_title`.

## Cấu trúc slug và đường dẫn dành riêng

Chỉ thêm một migration cơ sở dữ liệu Laravel mới; giữ nguyên `2026_09_11_004940_create_slugs_table.php`.

Migration mới thực hiện theo thứ tự:

1. Kiểm tra các bản ghi slug hiện có.
2. Ưu tiên bản ghi `vi` nếu một đối tượng đang có nhiều slug theo locale.
3. Giữ lại một bản ghi xác định được nếu đối tượng không có slug `vi`.
4. Xử lý có quy tắc các slug trùng nhau giữa các locale trước khi tạo unique key mới.
5. Xóa các unique index phụ thuộc locale.
6. Xóa cột `locale`.
7. Tạo unique index cho `slug`.
8. Tạo unique index cho `sluggable_type, sluggable_id`.
9. Chuyển settings dạng đa ngôn ngữ thành giá trị tiếng Việt dạng scalar.
10. Khởi tạo setting `website.public_pages` nếu chưa tồn tại.

Trước khi triển khai production, phải kiểm tra migration với bản sao dữ liệu production để phát hiện slug trùng giữa các locale. Migration không được âm thầm xóa slug tiếng Việt được ưu tiên.

Sau migration, `HasSlug` chỉ lấy một slug liên kết, `SlugObserver` chỉ tạo hoặc cập nhật một slug và `PublicSlugController` chỉ phân giải theo `slug`.

Danh mục route cung cấp các đường dẫn một đoạn được dành riêng. Tối thiểu gồm toàn bộ root path công khai có thể bị `/{slug}` che khuất: `gioi-thieu`, `dich-vu`, `giai-phap`, `lien-he`, `du-an`, `san-pham`, `blog` và `tim-kiem`. Observer tự thêm hậu tố số khi slug sinh ra trùng đường dẫn dành riêng hoặc slug đã tồn tại.

## Hoạt động của menu

Menu item trỏ tới trang hệ thống tiếp tục dùng `linked_source_type = native_route`; cột `url` lưu route name. Hồ sơ route không tham gia tạo link.

Bộ chọn nguồn menu lấy danh sách trang hệ thống từ danh mục route cố định và có `solutions.index`. Route name không tồn tại hoặc không hợp lệ trả về `#`, không tự đoán URL.

Menu item động tiếp tục lưu ID model được liên kết và tạo link bằng named route tương ứng. Xóa nhánh `native_page` và `pageLink()` không còn sử dụng.

Menu header mặc định được seed theo thứ tự:

1. Trang chủ
2. Giới thiệu
3. Dịch vụ
4. Giải pháp
5. Dự án
6. Sản phẩm
7. Kiến thức
8. Liên hệ

## SEO, Open Graph và sitemap

`FrontendSeoBuilder` không còn phụ thuộc `LanguageCatalog` hoặc `LocalizedUrl`.

- Canonical và breadcrumb dùng named route.
- `og:locale` là chuỗi cố định `vi_VN`.
- Ngôn ngữ tài liệu là chuỗi cố định `vi`.
- Ảnh chia sẻ của hồ sơ được dùng cho cả `og:image` và `twitter:image`.
- Cơ chế ảnh chia sẻ của chi tiết dịch vụ, dự án, sản phẩm, bài viết và danh mục được giữ nguyên.
- Metadata của hồ sơ route chỉ ghi đè route hệ thống hoặc listing tương ứng.
- Sitemap dùng named route và bổ sung `solutions.index` với tần suất và độ ưu tiên đã cấu hình.
- Không đưa route tìm kiếm và route POST của biểu mẫu vào sitemap.

## Hợp đồng của seeder

Seeder là Laravel seeder thông thường và được `DatabaseSeeder` gọi rõ ràng theo đúng thứ tự phụ thuộc.

Các seeder settings ghi đầy đủ mọi thuộc tính đã khai báo để cài đặt mới không lỗi vì thiếu setting:

- `WebsiteSettingsSeeder` tạo thông tin nhận diện doanh nghiệp, giá trị website mặc định, media rỗng và đầy đủ sáu hồ sơ `public_pages`.
- `HomepageSettingsSeeder` tạo nội dung tiếng Việt cơ bản có thể sử dụng cho Trang chủ.
- `CompanySettingsSeeder` tạo dữ liệu doanh nghiệp mẫu an toàn.
- `AboutSettingsSeeder` tạo các trường tiếng Việt dạng scalar cùng cấu trúc timeline và media đúng định dạng.
- `MenuSeeder` tạo menu header bằng route name, gồm cả `solutions.index`.
- Các seeder media, danh mục, dịch vụ, dự án, sản phẩm, bài viết và Hero Slide hiện có tiếp tục cung cấp dữ liệu mẫu cơ bản.

Seeder không tạo mảng locale, cột locale, biến thể bản dịch hoặc bản ghi ngôn ngữ. Kết quả phải xác định và ổn định khi chạy `migrate:fresh --seed` hoặc khởi tạo cơ sở dữ liệu test.

## An toàn migration và triển khai

Worktree hiện có các thay đổi chưa hoàn tất không thuộc tính năng này. Khi triển khai chỉ stage những file thuộc phạm vi đã duyệt; không sửa lại hoặc hoàn nguyên công việc của anh.

Thứ tự triển khai production:

1. Sao lưu cơ sở dữ liệu production.
2. Kiểm tra số lượng slug theo locale và các giá trị slug trùng nhau hiện có.
3. Triển khai code chứa migration tương thích và phần đơn ngôn ngữ mới.
4. Chạy `php artisan migrate --force`.
5. Xóa cache settings, config, route và view.
6. Kiểm tra canonical, menu, banner trang hệ thống, thẻ SEO và một số slug động đại diện.

Không chạy các seeder dữ liệu nền lên production trong quy trình triển khai thông thường vì có thể ghi đè nội dung quản trị. `migrate:fresh --seed` chỉ dùng cho cài đặt mới, local và test; không dùng để nâng cấp production.

## Chiến lược kiểm thử

Kiểm thử tự động phải chứng minh:

- cả sáu named route hoạt động và tạo canonical bằng origin cấu hình tại `APP_URL`;
- `/giai-phap` được render qua `SolutionsController`;
- menu item của trang hệ thống tạo URL bằng route name;
- menu item có route name không hợp lệ trả về `#`;
- mỗi đối tượng động chỉ có một slug và cơ sở dữ liệu từ chối slug trùng trên toàn hệ thống;
- slug sinh tự động tránh toàn bộ root path dành riêng;
- `PublicSlugController` phân giải được mọi loại nội dung động được hỗ trợ mà không lọc locale;
- URL cũ có tiền tố locale không tồn tại;
- giá trị About settings và timeline là scalar sau migration;
- năm trang bên trong hiển thị banner riêng và fallback đúng về banner chung;
- Trang chủ tiếp tục dùng Hero Slide và không hiển thị banner của trang bên trong;
- ảnh OG của hồ sơ độc lập với banner và fallback về ảnh chia sẻ chung của website;
- `og:locale` là `vi_VN` và `<html lang>` là `vi`;
- sitemap có sáu route đã thống nhất cùng nội dung hợp lệ hiện có và không có URL trùng;
- tìm kiếm toàn repository không còn `LocalizedUrl`, `LanguageCatalog`, `SetFrontendLocale`, code slug theo locale hoặc từ điển ứng dụng không phải tiếng Việt;
- `php artisan migrate:fresh --seed` chạy thành công và tạo đủ settings thiết yếu, menu, hồ sơ route cùng dữ liệu nghiệp vụ mẫu;
- Blade compile, test tính năng liên quan, toàn bộ test suite, frontend build và `git diff --check` đều đạt.

## Ranh giới nghiệm thu

Tính năng chỉ hoàn thành khi ứng dụng không có Page model hoặc bảng pages, route hệ thống sở hữu URL, lớp đa ngôn ngữ thuộc ứng dụng đã được xóa, một migration mới chuyển đổi an toàn dữ liệu hiện có, fresh seed tái tạo được website cơ bản, và kiểm tra trình duyệt xác nhận menu theo route, banner của năm trang trong, Hero Slide Trang chủ, canonical cùng ảnh Open Graph hoạt động đúng.
