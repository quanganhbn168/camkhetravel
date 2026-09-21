# Kế hoạch triển khai chuẩn frontend Bootstrap cho website doanh nghiệp

> **Dành cho:** đội triển khai

**Mục tiêu:** Chuẩn hoá frontend thành một nền Bootstrap 5 gọn, tái sử dụng được cho website giới thiệu doanh nghiệp, dịch vụ, dự án, giải pháp, sản phẩm và liên hệ. Giữ Laravel, Filament, Curator, các URL còn lại, dữ liệu CMS, bình luận và dữ liệu đánh giá sao; gỡ Landing, Bảng giá và Tracking.

**Kiến trúc:** Một entry chung chỉ chứa hệ giao diện dùng chung; CSS/JS đặc thù được tách theo nhóm trang. Bootstrap phụ trách các tương tác giao diện. Dữ liệu trình bày được chuẩn bị trong controller, Blade chỉ render. Tailwind chỉ còn ở bundle Filament.

**Công nghệ:** Laravel 13, Blade, Vite, pnpm 10, Bootstrap 5, Filament 5, Curator.

**Đặc tả đã duyệt:** `docs/superpowers/specs/2026-09-21-bootstrap-corporate-frontend-standard-design.md`

## Ràng buộc chung

- Không đưa tên dự án, khách hàng, thương hiệu hoặc tên miền vào tên file frontend, CSS custom property, class, biến JavaScript hay Vite entry.
- Quy ước chung: `--color-primary`, `--color-primary-hover`, `--color-ink`, `--color-surface`, `--color-muted`, `--font-sans`, `--font-display`; `.site-header`, `.site-footer`, `.site-container`, `.content-card`, `.page-hero`, `.section-heading`, `.form-status`.
- Xoá trọn Landing, Bảng giá và Tracking theo dependency: route, controller, admin resource, settings, provider, view, model/policy/test và bảng/quan hệ dữ liệu liên quan. Không đụng Curator, bình luận, rating 1–5 hoặc trạng thái kiểm duyệt của nội dung còn lại.
- Không khai báo JSON-LD `AggregateRating` trong đợt frontend này. Dữ liệu bình luận/rating là nguồn đúng cho schema sau này; việc hiện rich result Google không thể được đảm bảo bằng code.
- Mọi thay đổi public đều phải hoạt động ở 1440px và 390px; header, form, ảnh gallery, tab/accordion và modal phải điều khiển được bằng bàn phím.

## Chặng 1 — Chốt hợp đồng asset và regression test

**Files:**
- Create: `tests/Feature/PublicAssetBoundaryTest.php`
- Modify: `tests/Feature/HomeFeaturedServicesTest.php`
- Modify: `vite.config.js`
- Modify: `package.json`
- Track: `pnpm-workspace.yaml`

1. Viết test trước để khẳng định bundle public không import Tailwind hoặc theme cũ; CSS admin vẫn là entry riêng có Tailwind.
2. Thay assertion homepage cũ đang tìm cụm copy đã bị bỏ bằng assertion theo contract hiện hành: dữ liệu featured được render và Bootstrap tabs có `data-bs-toggle="tab"`.
3. Chốt Vite entries theo ranh giới sau:

```js
input: [
    'resources/css/app.css',
    'resources/css/pages/home.css',
    'resources/css/pages/resource-list.css',
    'resources/css/pages/resource-detail.css',
    'resources/css/pages/article.css',
    'resources/js/app.js',
    'resources/js/pages/home.js',
    'resources/js/pages/gallery.js',
    'resources/js/pages/article.js',
    'resources/css/filament/admin/theme.css',
    'resources/js/filament/curator-rich-editor-integration.js',
]
```

4. Giữ `pnpm-workspace.yaml`: đây không phải monorepo; nó chỉ cho pnpm 10 chạy lifecycle build của `esbuild`, dependency Vite cần dùng.

**Kiểm tra:**

```powershell
php artisan test --filter=HomeFeaturedServicesTest
php artisan test --filter=PublicAssetBoundaryTest
pnpm run build
```

**Tiêu chí xong:** lỗi test homepage hiện tại được sửa theo UI mới; Vite có ranh giới public/admin rõ ràng; không có asset entry mang tên dự án.

## Chặng 2 — Dựng lớp nền dùng chung

**Files:**
- Modify: `resources/css/app.css`
- Create: `resources/css/components/site-shell.css`
- Create: `resources/css/components/site-header.css`
- Create: `resources/css/components/site-footer.css`
- Create: `resources/css/components/forms.css`
- Create: `resources/css/components/content-card.css`
- Modify: `resources/js/app.js`
- Modify: `resources/js/bootstrap.js`

1. Đưa Bootstrap CSS, font và component CSS dùng chung vào `app.css`; chỉ công khai token trung tính.
2. Tách shell, header, footer, form và card ra thành component CSS; không để CSS của từng trang trong bundle chung.
3. Thu hẹp `app.js` về Bootstrap, menu/search shell và phản hồi form liên hệ. Xác nhận không còn caller trước khi gỡ Axios bootstrap.
4. Dùng Bootstrap alert/toast cho trạng thái form; không dùng SweetAlert cho lead form.

**Kiểm tra:**

```powershell
rg -n --glob '!public/build/**' -- "dvtec|dv-|--dv-|pccc-" resources/css resources/js resources/views
rg -n --glob '!public/build/**' -- "axios|window\.axios" resources app tests
pnpm run build
```

**Tiêu chí xong:** shell không phụ thuộc Tailwind/Alpine và class/token chung có thể tái sử dụng cho site doanh nghiệp khác.

## Chặng 3 — Chuẩn hoá layout chung sang Bootstrap

**Files:**
- Modify: `resources/views/frontend/layouts/app.blade.php`
- Modify: `resources/views/frontend/partials/header.blade.php`
- Modify: `resources/views/frontend/partials/footer.blade.php`
- Modify: các partial header/footer public được layout gọi
- Create: `tests/Feature/PublicShellTest.php`

1. Chuyển desktop navigation sang Bootstrap navbar, mobile menu sang offcanvas, dropdown sang component Bootstrap, tìm kiếm sang modal hoặc offcanvas.
2. Thay toàn bộ `x-data`, `x-show`, `x-transition`, `@click` trong shell bằng data attributes Bootstrap và JavaScript nhỏ ở entry chung khi Bootstrap không có component phù hợp.
3. Chuẩn hoá skip link, focus state, `aria-expanded`, label tìm kiếm và vùng thông báo form.

**Test đầu tiên:**

```php
$response = $this->get('/');

$response->assertOk()
    ->assertSee('navbar-toggler', false)
    ->assertSee('offcanvas', false)
    ->assertDontSee('x-data', false);
```

**Kiểm tra:** `php artisan test --filter=PublicShellTest`, `php artisan view:cache`, desktop/mobile browser QA.

**Tiêu chí xong:** một shell nhất quán cho mọi URL public, menu mobile hoạt động không cần Alpine.

## Chặng 4 — Hoàn thiện trang chủ theo data presentation

**Files:**
- Modify: `app/Http/Controllers/HomeController.php`
- Modify: `resources/views/frontend/home.blade.php`
- Modify: `resources/css/pages/home.css`
- Create: `resources/js/pages/home.js`
- Delete after caller check: `resources/views/frontend/partials/home-featured-services.blade.php`
- Modify: `tests/Feature/HomeFeaturedServicesTest.php`

1. Chuyển các mảng trình bày tĩnh hiện nằm trong Blade vào phương thức presentation riêng của `HomeController`; không tạo bảng hoặc thay dữ liệu CMS.
2. Giữ Swiper cho hero, post và testimonial vì đây là slider hiện có đầy đủ navigation/keyboard/responsive; tab dịch vụ dùng Bootstrap nav/tab, FAQ dùng accordion, video dùng modal, card dùng grid responsive.
3. Đổi toàn bộ class trang chủ cũ/mang tên dự án thành `.home-page`, `.home-hero`, `.home-section`, `.solution-card`, `.process-step` và token chung.
4. Xác nhận partial dịch vụ nổi bật cũ không có caller trước khi xoá cùng CSS chỉ phục vụ nó.

**Kiểm tra:**

```powershell
rg -n "home-featured-services" resources app tests
php artisan test --filter=HomeFeaturedServicesTest
php artisan test --filter=PublicShellTest
pnpm run build
```

**Tiêu chí xong:** không còn mảng nội dung presentation trong Blade homepage; carousel, tab, FAQ và modal hoạt động bằng Bootstrap.

## Chặng 5 — Chuẩn hoá trang giới thiệu và các danh sách nội dung

**Files:**
- Create: `resources/css/pages/resource-list.css`
- Modify: `resources/views/frontend/about.blade.php`
- Modify: `resources/views/frontend/contact.blade.php`
- Modify: `resources/views/frontend/services/index.blade.php`
- Modify: `resources/views/frontend/projects/index.blade.php`
- Modify: `resources/views/frontend/products/index.blade.php`
- Modify: `resources/views/frontend/posts/index.blade.php`
- Modify: `resources/views/frontend/search/index.blade.php`
- Modify: các card/listing partial mà các trang trên gọi
- Create: `tests/Feature/PublicListingLayoutTest.php`

1. Dùng page hero, section heading, responsive grid, Bootstrap pagination, badge và empty state thống nhất.
2. Chuyển lịch sử/nhóm nội dung giới thiệu sang pills/tab Bootstrap; bỏ Alpine khỏi các khu vực này.
3. Giữ nguyên filter/query/URL đang có; chỉ đổi render và hành vi frontend.
4. Form liên hệ tiếp tục POST về endpoint hiện hữu, kèm trạng thái Bootstrap và lỗi validation có thể đọc được.

**Test đầu tiên:**

```php
$this->get(route('services.index'))
    ->assertOk()
    ->assertSee('page-hero', false)
    ->assertSee('content-card', false);
```

**Kiểm tra:** test listing, submit validation route hiện có, browser QA responsive của từng index.

**Tiêu chí xong:** các trang danh sách cùng một ngôn ngữ layout, không thêm route hoặc mô hình CMS song song.

## Chặng 6 — Trang chi tiết, gallery, bài viết, bình luận và rating

**Files:**
- Create: `resources/css/pages/resource-detail.css`
- Create: `resources/css/pages/article.css`
- Create: `resources/js/pages/gallery.js`
- Create: `resources/js/pages/article.js`
- Modify: `resources/views/frontend/services/show.blade.php`
- Modify: `resources/views/frontend/projects/show.blade.php`
- Modify: `resources/views/frontend/products/show.blade.php`
- Modify: `resources/views/frontend/posts/show.blade.php`
- Modify: service/project/product detail partials và comment form partial đang được gọi
- Create: `tests/Feature/PublicDetailInteractionTest.php`
- Modify: comment/rating feature tests only when markup assertions need updating

1. Tạo bố cục 8–4 hoặc 9–3 bằng Bootstrap grid tuỳ loại nội dung; dùng breadcrumb, cover, metadata, CTA và related card thống nhất.
2. Thay GLightbox bằng Bootstrap modal/gallery page-local; ảnh Curator, URL ảnh và alt text hiện có được giữ nguyên.
3. Thay article ToC/copy/share Alpine bằng JS nhỏ trong `article.js`; chỉ tải ở bài viết.
4. Giữ comment form, POST endpoint, trạng thái pending/approved và input rating 1–5. Hiển thị summary rating từ đúng `approvedComments`, không dùng rating chưa duyệt.

**Test đầu tiên:**

```php
$response = $this->get($service->url);

$response->assertOk()
    ->assertSee('rating-summary', false)
    ->assertSee('comment-form', false)
    ->assertSee('gallery-modal', false);
```

**Kiểm tra:** test comment/rating hiện có, keyboard modal test thủ công, 390px gallery/article QA.

**Tiêu chí xong:** gallery và ToC không cần GLightbox/Alpine; bình luận và rating vẫn cùng luồng dữ liệu/kiểm duyệt như trước.

## Chặng 7 — Gỡ dependency và di sản sau khi không còn caller

**Files:**
- Modify: `package.json`
- Modify: `pnpm-lock.yaml`
- Delete after zero-call verification: `resources/scss/frontend.scss`
- Delete after zero-call verification: CSS theme/legacy chỉ phục vụ public cũ, gồm PCCC/home legacy đã được thay thế
- Delete after zero-call verification: JS initialization cũ chỉ phục vụ `alpinejs`, `aos`, `glightbox`, `sweetalert2`, `swiper`, `axios`

1. Chạy caller audit trước từng lần xoá. Không xoá asset Curator/Filament.
2. Sau khi tất cả interaction được Bootstrap hoặc page-local JS thay thế, gỡ `axios`, `alpinejs`, `aos`, `glightbox`, `sweetalert2`, `sass` khỏi dependency bằng pnpm. Giữ `swiper` cho slider.
3. Giữ `bootstrap`, `@popperjs/core`, `swiper`, `tailwindcss` (Filament), Vite và các dependency quản trị còn caller.
4. Xác minh source public không còn Tailwind class/directive; Tailwind chỉ tồn tại trong `resources/css/filament/admin/theme.css` và chuỗi build admin.

**Kiểm tra:**

```powershell
rg -n --glob '!public/build/**' -- "from ['\"](alpinejs|aos|glightbox|sweetalert2|swiper|axios)['\"]|@import ['\"]tailwindcss|@tailwind" resources app
pnpm run build
php artisan test
php artisan view:cache
git diff --check
git status --short
```

**Tiêu chí xong:** public bundle không còn dependency cũ; admin vẫn build; lockfile và workspace metadata nhất quán.

## Chặng 0 — Gỡ Landing, Bảng giá và Tracking

**Files:**
- Delete: Landing, Pricing, Tracking controllers/models/resources/policies/support/views/tests đã map bằng caller audit.
- Modify: `routes/web.php`, `bootstrap/providers.php`, `config/settings.php`, `app/Providers/FrontendServiceProvider.php`, `app/Filament/Pages/ManageSettings.php`, SEO/menu/dashboard/service callers.
- Create: migration xoá bảng/foreign key Landing và Bảng giá theo thứ tự phụ thuộc.
- Create: `tests/Feature/RemovedSubsystemsTest.php`.

1. Viết test đỏ xác nhận route `/bang-gia`, các route bình luận Landing, lớp Tracking settings/provider và entry Landing/Pricing không còn được đăng ký.
2. Xoá triển khai theo dependency, cập nhật route/menu/SEO/dashboard/service callers còn sống; giữ comments/rating của Service/Project/Post.
3. Migration xoá dữ liệu và schema Landing/Bảng giá chỉ chạy khi deploy `php artisan migrate`; local hiện không có Landing hay PricingPlan.
4. Không làm “fallback” render cũ: URL/tính năng đã bỏ phải trả 404 hoặc không tồn tại route, không redirect sang nội dung khác.

**Tiêu chí xong:** admin không còn nhóm Landing, Bảng giá hay Tracking; public không còn `/bang-gia` hay landing slug; source không còn provider/script injection tương ứng.

## Kiểm thử toàn đợt và bàn giao

1. Chạy `php artisan test` trên database testing; xử lý hoặc ghi rõ những failure không liên quan còn lại.
2. Chạy `pnpm run build`, kiểm tra manifest đủ public/admin entries và so sánh kích thước bundle trước/sau.
3. Chạy `php artisan view:cache`, sau đó browser QA trang chủ, about, services, service detail, projects, product detail, posts, article, search và contact ở 1440px/390px.
4. Dò toàn bộ tên mang dấu dự án trong `resources/css`, `resources/js`, `resources/views`, `vite.config.js` và tests; mọi ngoại lệ là dữ liệu CMS hiển thị cho người dùng, không phải tên implementation.
5. Chỉ stage các file của đợt này, commit theo nhóm logic, push nhánh remote đã có, rồi báo commit và SHA remote chính xác.

## Điểm cần reviewer xác nhận trước khi code

- Mục tiêu là chuẩn hoá frontend hiện hữu, không cắt tính năng CMS/backend.
- `pnpm-workspace.yaml` được giữ và đưa vào Git vì Vite cần `esbuild` install script; tên file là convention của pnpm, không là branding dự án.
- Rating/bình luận vẫn được giữ hoàn toàn; schema sao Google là hạng mục SEO kế tiếp, sau khi frontend đã ổn định.
- Quy ước neutral naming được áp dụng cả file mới lẫn phần code cũ được chạm tới.
