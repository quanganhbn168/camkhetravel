# Kế hoạch triển khai Giải pháp và nội dung website

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Hiển thị ảnh danh mục Dịch vụ đúng nguồn, quản trị Giải pháp độc lập và tổ chức lại menu nội dung Filament.

**Architecture:** Dịch vụ giữ mô hình hiện tại. Solution có bảng riêng, dùng Curator và cơ chế slug hiện có; controller cung cấp dữ liệu cho trang chủ, danh sách và chi tiết. Không tạo danh mục giải pháp, không trộn dữ liệu hoặc URL Dịch vụ.

**Tech Stack:** Laravel, Filament, Curator, Spatie Permission/Shield, Blade, SCSS, Bootstrap, Font Awesome, PHPUnit/Livewire, pnpm.

**Spec:** `docs/superpowers/specs/2026-09-22-giai-phap-va-noi-dung-website-design.md` (đã được người dùng duyệt).

## Global Constraints

- Không Git, không tạo nhánh, không tạo sao lưu trong công việc này khi chưa được yêu cầu.
- Không chạy migrate:fresh trên database hiện tại.
- Chỉ thêm create migration cho bảng mới; nếu cần sửa schema cũ thì sửa trực tiếp create migration theo quy ước dự án, không thêm migration vá cột.
- Form chuẩn 2:1: nội dung và RichEditor bên trái, trạng thái/thứ tự/ảnh bên phải; SEO bên dưới phần nội dung.
- Giải pháp là nội dung độc lập, không thuộc Dịch vụ, không có danh mục giải pháp.
- Test database là `dvtec_testing` theo phpunit.xml; phải kiểm tra cấu hình thực tế trước mọi test dùng RefreshDatabase.
- Không tự gán quyền Giải pháp cho vai trò thường, không chạy lại toàn bộ ShieldSeeder trên dữ liệu hiện tại vì nó dùng syncPermissions.

## Review Focus

1. Danh mục không có ảnh nhưng dịch vụ có ảnh: không lấy ảnh dịch vụ thay thế — task 1.
2. Bản nháp có slug hợp lệ: vẫn 404 ngoài frontend và không vào sitemap — task 2/3.
3. Seeder chạy lại sau người dùng sửa tiêu đề: không tạo trùng, không ghi đè — task 2.
4. Vai trò không có quyền: không nhìn thấy hoặc sửa Solution qua URL admin trực tiếp — task 4.
5. Tab ít/nhiều và tiêu đề dài trên điện thoại: không tràn ngang trang, bàn phím vẫn truy cập liên kết — task 5.

## Task 1 — Ảnh danh mục và liên kết Dịch vụ

**Files:** sửa `app/Http/Controllers/Frontend/HomeController.php`, `resources/views/frontend/home.blade.php`, `resources/scss/pages/home.scss`, `tests/Feature/HomeFeaturedServicesTest.php`.

**Interfaces:** đầu vào ServiceCategory.curatorMedia và category.services; đầu ra home_image_url/home_image_alt giữ nguyên tên cho Blade.

- [ ] Thêm test trong HomeFeaturedServicesTest: gán hai media khác nhau cho danh mục/dịch vụ, assertViewHas xác nhận ảnh của danh mục; xóa liên kết media danh mục rồi assert null dù dịch vụ vẫn có ảnh. Dùng media fixture hiện có của test suite, không gọi ảnh bên ngoài.
- [ ] Chạy `php artisan test --filter=HomeFeaturedServicesTest`; xác nhận test mới thất bại vì ảnh đang lấy từ dịch vụ.
- [ ] Eager load `curatorMedia` cùng `slugs`; bỏ chọn imageService và gán:

```php
$category->setAttribute('home_image_url', $category->image_url);
$category->setAttribute('home_image_alt', $category->name);
```

- [ ] Thay danh sách dịch vụ bằng class riêng `home-service-links`; giữ route slug hiện có và giới hạn danh sách hiện hành:

```blade
<a href="{{ route('slug.show', ['slug' => $service->slug]) }}">
    <span>{{ $service->title }}</span>
    <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
</a>
```

- [ ] CSS link display:flex, justify-content:space-between, padding đủ bấm; hover/focus-visible đổi nền/chữ, icon translateX(4px). Media query prefers-reduced-motion tắt transition. Nếu thiếu ảnh, không render anchor ảnh rỗng.
- [ ] Test assertSee URL dịch vụ và class icon; chạy lại test cùng `FrontendShellStandardTest`, `HomeHeroSlidePresentationTest` để chắc không ảnh hưởng slide.

## Task 2 — Dữ liệu và seed Giải pháp

**Files:** tạo `database/migrations/2026_09_22_120000_create_solutions_table.php`, `app/Models/Solution.php`, `database/seeders/SolutionSeeder.php`, `tests/Feature/SolutionDataTest.php`; sửa `database/seeders/DatabaseSeeder.php` và nơi đăng ký SlugObserver hiện có nếu nó dùng danh sách model tường minh.

**Interfaces:** Solution dùng HasSlug/HasSeoImage, `curatorMedia(): BelongsTo`, `bannerMedia(): BelongsTo`, accessors image_url/banner_url, `scopePublished(Builder $query): Builder`. Trạng thái công khai dùng `is_active` boolean; home dùng `is_home`; thứ tự sort_order.

- [ ] Viết test schema/model/slug, JSON highlights và quan hệ ảnh độc lập. Test seed hai lần và sửa tiêu đề giữa hai lần:

```php
$this->seed(SolutionSeeder::class);
$solution = Solution::query()->firstOrFail();
$id = $solution->id;
$solution->update(['title' => 'Nội dung đã chỉnh']);
$count = Solution::count();
$this->seed(SolutionSeeder::class);
$this->assertSame($count, Solution::count());
$this->assertSame('Nội dung đã chỉnh', Solution::findOrFail($id)->title);
```

- [ ] Chạy `php artisan test --filter=SolutionDataTest`, xác nhận fail do thiếu model/schema.
- [ ] Create migration với id, title, nullable short_title/excerpt/body/highlights JSON, nullable seed_key unique (nhận diện seed ổn định, không lộ trong form), sort_order default 0, is_active/is_home default false, seo_title/seo_description nullable, timestamps. Ba FK media nullable/nullOnDelete: curator_media_id, banner_media_id, og_image_media_id. Không có cột slug trùng với trait.
- [ ] Model casts boolean/array/integer, published chỉ where is_active true; quan hệ/accessor theo model đang dùng trong dự án. Đăng ký slug observer theo đúng cơ chế hiện hành để tạo, cập nhật và xóa slug nhất quán.
- [ ] Chuyển nguyên năm bản ghi HomeController::solutions() sang SolutionSeeder, map name→short_title, description→excerpt, items→highlights. Gán media qua MediaSeeder::id với tên file đã kiểm tra thật. Dùng firstOrCreate theo seed_key; không update bản ghi đã có. Bổ sung vào DatabaseSeeder sau MediaSeeder.
- [ ] Chạy lại SolutionDataTest và test slug hiện hữu; xác nhận cùng một slug không được dùng cho hai chủ thể theo quy tắc dự án.

## Task 3 — Frontend và URL Giải pháp

**Files:** sửa `routes/web.php`, `app/Http/Controllers/Frontend/SolutionsController.php`, `app/Http/Controllers/Frontend/HomeController.php`, `resources/views/frontend/home.blade.php`, `resources/views/frontend/solutions/index.blade.php`, `app/Support/Seo/FrontendSeoBuilder.php`, `app/Support/Seo/SitemapBuilder.php`; tạo `resources/views/frontend/solutions/show.blade.php`, `tests/Feature/SolutionFrontendTest.php`.

**Interfaces:** giữ `solutions.index`; thêm `solutions.show` với tham số solution bind qua HasSlug. `FrontendSeoBuilder::solution(Solution $solution): array`. Home view nhận Eloquent collection solutions, không còn solutionImageUrl chung.

- [ ] Test bản công khai xuất hiện ở index, bản is_home xuất hiện trang chủ, bản nháp bị ẩn và detail 404:

```php
$draft = Solution::create(['title' => 'Giải pháp nháp', 'is_active' => false]);
$this->get(route('solutions.show', ['solution' => $draft->slug]))->assertNotFound();
$this->get('/giai-phap')->assertOk()->assertDontSee('Giải pháp nháp');
```

- [ ] Test title/body/ảnh riêng, canonical đúng `/giai-phap/{slug}`, sitemap không chứa nháp; không có dịch vụ nào được lấy làm giải pháp. Chạy test để ghi nhận fail.
- [ ] Giữ route index đang có; thêm GET `/giai-phap/{solution}` gọi `SolutionsController::show`. Method show abort_unless is_active, chuẩn bị SEO và ảnh trước khi render. Index thêm published query eager load slugs/media và paginate(12), orderBy sort_order rồi id.
- [ ] Home query published()->where('is_home', true) eager load slugs/curatorMedia, sắp xếp sort_order/id. Xóa method solutions() viết cứng và các fallback solutionImageUrl.
- [ ] Blade dùng short_title nếu có hoặc title làm nhãn, id tab `solution-{id}`; ảnh thuộc bản ghi, highlights là text không giả liên kết. Chi tiết route solutions.show, xem tất cả route solutions.index. Không có bản ghi thì ẩn section trang chủ; index dùng @forelse thông báo chưa có giải pháp.
- [ ] Trang detail theo layout nội dung hiện hành: tiêu đề/excerpt, optional banner, nội dung qua cơ chế render RichEditor hiện có. Không lấy ảnh OG thay banner; OG lấy trường riêng. SEO method dùng canonical của solutions.show, breadcrumb Home→Giải pháp→tiêu đề. Sitemap thêm published Solution, URL solutions.show, không đưa URL slug ngắn thành canonical thứ hai.
- [ ] Chạy `php artisan test --filter=SolutionFrontendTest` và `php artisan test --filter=SeoEndpointsTest`.

## Task 4 — Filament và thứ tự menu

**Files:** tạo `app/Filament/Resources/Solutions/SolutionResource.php`, `Schemas/SolutionForm.php`, `Tables/SolutionsTable.php`, `Pages/ListSolutions.php`, `Pages/CreateSolution.php`, `Pages/EditSolution.php` dưới cùng thư mục resource; tạo `app/Policies/SolutionPolicy.php`, `database/seeders/SolutionPermissionsSeeder.php`, `tests/Feature/SolutionAdminTest.php`, `tests/Feature/ContentNavigationTest.php`. Sửa ShieldSeeder và các Resource có menu trong spec.

**Interfaces:** resource dùng UsesPrimaryKeyForRecordRoutes, label Giải pháp, group Nội dung website, sort 4; quyền tên `ViewAny:Solution`, `View:Solution`, `Create:Solution`, `Update:Solution`, `Delete:Solution` và các action theo ServicePolicy hiện có.

- [ ] Test user super_admin tạo/sửa Solution qua Livewire, slug và ba ảnh lưu riêng; test user không có quyền bị chặn cả navigation và trang edit. Chạy `php artisan test --filter=SolutionAdminTest` để thấy fail.
- [ ] Form theo cấu trúc ServiceForm nhưng chỉ chứa các trường thuộc spec: title/short_title/slug/excerpt/body/highlights; SEO trái phía dưới; curator_media_id/banner_media_id phải, OG trong SEO. Dùng RichEditor và CuratorPicker với cấu hình upload/sanitization dự án, không tạo upload đường dẫn thủ công.
- [ ] Table dùng TextColumn::make('title')->copyable(), ảnh, sort_order, ToggleColumn::make('is_active'), ToggleColumn::make('is_home'), edit/delete; không cột slug. Ba Pages theo mẫu resource hiện có, model label tiếng Việt.
- [ ] Policy theo ServicePolicy thay tên subject thành Solution. Thêm Solution vào danh sách ShieldSeeder cho fresh seed. Seeder quyền riêng dùng Permission::findOrCreate và chỉ givePermissionTo super_admin, không sync hoặc cấp cho role khác.
- [ ] Test navigation map với assertSame tên nhóm/thứ tự. Gán sort: Intro1, Service2, ServiceCategory3, Solution4, Project5, ProjectCategory6, Product7, ProductCategory8, Post9, PostCategory10, Tag11, Faq12. Đổi nhãn danh mục dịch vụ/bài viết như spec; ContactRequest sang Khách hàng sort1, Comment giữ sort2.
- [ ] Chạy hai test mới và `AdminSettingsStructureTest`, `CategoryHierarchyTest`; test các toggle cập nhật boolean thật, không chỉ đổi icon.

## Task 5 — Kiểm chứng và áp dụng local

**Files:** không thêm feature mới; cập nhật checklist kế hoạch theo kết quả thật.

- [ ] Kiểm tra APP_ENV/DB thực tế của PHPUnit là dvtec_testing; chạy `php artisan test`. Nếu test cũ đòi nội dung solution viết cứng, sửa fixture để seed Solution chứ không hạ assertion.
- [ ] Chạy `pnpm run build`, xác nhận exit 0; kiểm tra thay đổi bằng đọc file và lint, không dùng Git.
- [ ] Trước DB write local, chạy `php artisan migrate:status`; chỉ migrate đúng create_solutions bằng `php artisan migrate --path=database/migrations/2026_09_22_120000_create_solutions_table.php --force`. Không chạy tất cả migration pending hoặc fresh.
- [ ] Chạy `php artisan db:seed --class=SolutionSeeder --force` và `php artisan db:seed --class=SolutionPermissionsSeeder --force`. Xác nhận có 5 seed record và media FK hợp lệ; không sửa slide, banner hệ thống hoặc service data.
- [ ] Dùng skill computer-use kiểm tra trang chủ ở desktop/mobile: ảnh danh mục, link dịch vụ, hover và focus; tab giải pháp/ảnh riêng; CTA về đúng URL. Kiểm tra `/giai-phap` và một detail.
- [ ] Kiểm tra Filament menu, form2:1, editor, ba ảnh, toggle, copy title và quyền. Dùng dữ liệu test để thao tác lưu, không ghi đè nội dung người dùng.
- [ ] Rà tiêu đề dài và nhiều tab ở viewport390; không tràn trang, link dùng bàn phím được. Kiểm tra reduced motion, không có image request404.
- [ ] Báo rõ số test, build, browser proof, migration/seed đã chạy và phần chưa xác minh. Không commit/push nếu người dùng chưa yêu cầu.

## Duyệt kế hoạch

Đã tự rà đối chiếu spec: dữ liệu, UI, URL/SEO, quyền, seed, menu và an toàn database đều có nhiệm vụ tương ứng. Chờ người dùng duyệt kế hoạch và xác nhận cách thực thi trước khi sửa ứng dụng theo quy trình writing-plans.
