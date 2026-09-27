# Kế hoạch thiết kế lại trang chủ

Ngày: 27/09/2026. Phạm vi lượt này: kế hoạch, định hướng hình ảnh và bộ ảnh WebP; chưa sửa giao diện đang chạy.

## 1. Mục tiêu đã thống nhất

- Giữ phần nội dung nghiệp vụ đang cơ bản đúng, sắp xếp lại để rõ nhu cầu và có sức hút thị giác.
- Ba nhóm khách có mức ưu tiên ngang nhau: cá nhân/gia đình; đối tác tour/doanh nghiệp; xe cưới/đưa đón gia đình.
- Giữ xanh #28BDBF và vàng #FFCA00; tiếp tục dùng Bootstrap 5, CSS global và CSS riêng từng trang đã chuẩn hoá.
- Ảnh ô tô ưu tiên VinFast. Ảnh tạo mới là ảnh minh hoạ; tên xe, sức chứa, trang bị, giá và lịch phục vụ công bố phải lấy từ dữ liệu đã xác nhận.
- Icon dùng Font Awesome hiện có hoặc SVG path đơn giản. Không tạo icon bằng ảnh raster.
- Dùng lại quản trị, layout, menu, thư viện media và luồng `contact.store`; mọi nội dung mới cần có chủ sở hữu trong CMS.

## 2. Những vấn đề đang làm trang chưa đẹp và chưa đủ thuyết phục

1. Hero, đội xe, đối tác và xe cưới thiếu bộ ảnh riêng; cùng ảnh mặc định xuất hiện nhiều lần, làm các dịch vụ khó phân biệt.
2. Các ô nội dung nhỏ, chữ nhỏ và cam kết lặp lại khiến trang dài nhưng nhịp đọc đều; khu đối tác hiện chiếm nhiều diện tích hơn hai nhóm còn lại.
3. Chữ serif/chữ viết tay được dùng ở nhiều nơi, trong khi ảnh xe chưa đủ mạnh; thiếu một phong cách xuyên suốt cho website dịch vụ xe.
4. Đội xe mới có nhóm số chỗ và vài đặc điểm; chưa có ảnh quản trị riêng, ảnh trong modal và các trường hỗ trợ chọn xe đầy đủ.
5. Thẻ dịch vụ hiện mở form ngay; nên có cả đường xem thông tin dịch vụ và nút tư vấn có sẵn ngữ cảnh.
6. Phản hồi minh hoạ và số liệu chưa xác minh không tạo được bằng chứng tin cậy. Trang công khai chỉ hiện phản hồi/số liệu thật đã được duyệt; thiếu thì ẩn khối.

## 3. Thứ tự nội dung và chức năng

| Vị trí | Hiện gì | Bố cục đề xuất | Hành động và nguồn dữ liệu |
|---|---|---|---|
| Header | Logo, Dịch vụ, Đội xe, Hành trình, Kinh nghiệm, Liên hệ; hotline nếu có | Header gọn, menu ít mục, một nút Tư vấn chuyến đi | Menu và liên hệ từ quản trị; mobile có nút Gọi/Zalo/Tư vấn gọn |
| Hero | Một lời giới thiệu rõ: xe cho chuyến đi riêng, đối tác và ngày cưới; ảnh VinFast lớn | Một khung ảnh chủ đạo, xe ở bên phải, tiêu đề ở bên trái; mobile ảnh và chữ tách rõ | CTA Tư vấn phương án xe + Xem các dịch vụ. Dữ liệu HeroSlide hiện có; nếu nhiều slide vẫn hỗ trợ chuyển bằng nút, không tự chạy làm gián đoạn đọc |
| Chọn nhu cầu nhanh | Ba lựa chọn ngang nhau: Chuyến đi riêng / Hợp tác đối tác / Xe cưới | Ba tab cùng kích thước, nằm trong khung báo giá ngắn | Đón ở đâu, đi đâu, ngày đi, số khách/loại xe; bấm tiếp mới hỏi tên và điện thoại. Tự giữ dữ liệu khi mở form; chưa phải đặt xe hay thanh toán |
| Ba nhóm dịch vụ | Mỗi nhóm có ảnh riêng, một câu giá trị, 2–3 nhu cầu cụ thể và CTA riêng | Ba thẻ ảnh lớn bằng nhau, desktop 3 cột; mobile xếp dọc, không giấu hai nhóm sau carousel | Cá nhân: đi tỉnh, du lịch, xe ghép. Đối tác: xe hợp đồng, đoàn/tour, đầu mối điều phối. Xe cưới: xe dâu, đưa đón gia đình, lịch đón. Có link dịch vụ chuẩn và nút tư vấn theo nhóm |
| Lựa chọn xe | Nhóm 4–5 chỗ, 7 chỗ, 16 chỗ theo danh mục hiện có; ảnh, nhu cầu phù hợp, hành lý/tiện ích khi đã xác minh | 3 ảnh xe đồng nhất góc chụp/nền; thông tin ngắn, không biến thành bảng thông số dài | Xem chi tiết mở modal từ CMS; Tư vấn xe này điền sẵn xe và nhóm nhu cầu. Không gắn ảnh VF 8 vào nhóm 7 chỗ hay ảnh VinFast vào xe 16 chỗ |
| Cách đồng hành | Giới thiệu rất ngắn và 4 cam kết có ý nghĩa thực tế | Một ảnh trải nghiệm lớn cạnh nội dung thoáng; gộp phần cam kết đang lặp ở nhiều vị trí | Tái sử dụng about, commitments và commitment_items; chỉ hiện chỉ số đã xác minh |
| Tuyến và phạm vi hỗ trợ | Các tuyến/nhu cầu đã có nội dung: đi tỉnh theo lịch trình, Hà Nội ⇄ Cẩm Khê/Yên Lập | Dải thông tin gọn với icon vị trí; không tạo bản đồ hoặc lịch chạy giả | Xem dịch vụ/nhắn hỏi lịch; danh sách CMS. Không công bố giờ chạy, giá hay còn chỗ nếu chưa có dữ liệu |
| Quy trình | Gửi nhu cầu → Trao đổi phương án → Xác nhận chi tiết → Đồng hành chuyến đi | 4 bước rõ, icon SVG/Font Awesome; cùng một quy trình cho cả ba nhóm | Tái sử dụng dữ liệu bước hiện có, chỉnh ngôn ngữ cho khách cá nhân, đối tác và xe cưới đều hiểu |
| Tin cậy | Ảnh chuyến đi thực tế, phản hồi thật hoặc thông tin doanh nghiệp có thể xác minh | Một khối chọn lọc; không lặp một dãy thẻ giống dịch vụ | Chỉ xuất bản khi có dữ liệu thật. Ảnh AI không được đưa vào mục “chuyến đi thực tế” hay gắn thành khách hàng/đội ngũ thật |
| Kinh nghiệm + FAQ | 3 bài mới phù hợp; 4–6 câu hỏi về báo giá, loại xe, hành lý, lịch đón, hợp tác | Bài viết dạng 1 lớn + 2 nhỏ; FAQ gọn, dễ mở | Post/Faq hiện có, link trang chi tiết và dữ liệu SEO FAQ tiếp tục dùng nguồn CMS |
| CTA cuối + Footer | Một lời mời gửi lịch trình, nút tư vấn, hotline/Zalo, thông tin pháp lý/liên hệ | Background phong cảnh thoáng; gộp CTA cuối trang với CTA footer để tránh hai lời mời giống nhau liền nhau | Nội dung/liên hệ quản trị; giữ shared footer, thêm tuỳ chọn ẩn CTA footer ở trang đã có CTA cuối |

Ba thẻ dịch vụ là ba cửa vào ngang nhau. Không giữ khối đối tác dài nhiều tầng cùng một khối xe cưới ngắn như hiện tại. Nội dung sâu đưa về trang dịch vụ tương ứng.

## 4. Luồng tư vấn

- Chọn một trong ba nhu cầu ở khung đầu trang hoặc ở thẻ dịch vụ; nội dung form tự phù hợp ngữ cảnh.
- Khách cá nhân: điểm đón, điểm đến, ngày đi, số người; ngày về/điểm dừng để ở phần mở rộng.
- Đối tác: thêm tên đơn vị, số chuyến/quy mô đoàn, nhu cầu hợp tác; không bắt nhập các trường chỉ dành cho một chuyến cá nhân.
- Xe cưới: ngày, địa điểm/giờ đón dự kiến, xe dâu hoặc đưa đón gia đình, ghi chú trang trí nếu có.
- Người dùng có thể chuyển nhu cầu mà không mất tên/điện thoại đã nhập. Giữ tên dịch vụ, service_id và nguồn gửi để quản trị biết khách đến từ đâu.
- Dùng lại endpoint ContactRequest/contact.store, validation phía server, chống gửi lặp và thông báo lỗi hiện có. Không tạo hệ thống đặt xe/thanh toán hay báo giá tự động trong đợt thiết kế này.
- Trạng thái thành công xác nhận đã nhận yêu cầu, có nút gọi/Zalo theo dữ liệu cấu hình. Không hứa thời gian phản hồi nếu doanh nghiệp chưa chốt.

## 5. Quy chuẩn thị giác

- Phong cách: dịch vụ xe hiện đại, chỉn chu và gần gũi; ảnh thật cảm giác tự nhiên, ánh sáng sáng sớm, nhất quán màu sắc.
- Nền chủ đạo trắng/ngà rất nhạt. Xanh là màu thao tác và nhận diện; vàng dùng ít cho chỉ dẫn/chi tiết nhấn. Khối tối dùng để tạo nhịp nghỉ có chủ đích.
- Be Vietnam Pro cho nội dung và tiêu đề chính, dùng độ đậm và kích thước tạo phân cấp. Lora có thể giữ ở một câu ngắn thuộc xe cưới; hạn chế chữ viết tay, bỏ các câu trang trí lặp ở nhiều khối.
- Desktop: nội dung rộng khoảng 1240–1280px, khoảng cách section 72–88px, gap 24px; tiêu đề hero khoảng 52–60px, tiêu đề section 32–40px; nội dung chính 16–18px.
- Mobile: khoảng cách section 40–48px, tiêu đề hero 34–38px; nội dung không co về 10–12px để nhét nhiều thẻ; nút và vùng chạm tối thiểu khoảng 44px.
- Card bo góc vừa phải, đường viền nhẹ, bóng đổ tiết chế. Thay đổi nhịp bố cục bằng ảnh lớn/nhỏ và vùng trống thay cho việc thêm nhiều hộp.
- Tiêu đề, CTA và icon là HTML/SVG; không gắn chữ, giá, hotline vào ảnh để thay nội dung, dịch thuật và SEO vẫn dễ.
- Tiếp tục lấy màu/font từ brand.css và theme.css. CSS trang chủ phụ trách bố cục và responsive, không tạo bảng màu riêng.

## 6. Dữ liệu quản trị cần bổ sung tối thiểu khi triển khai

| Chủ sở hữu | Giữ lại | Bổ sung dự kiến |
|---|---|---|
| HeroSlide | Nội dung, CTA, ảnh, thứ tự, bật/tắt | Ảnh mobile hoặc điểm lấy nét nếu ảnh desktop không cắt tốt trên màn nhỏ |
| HomepageSettings / ManageSettings | Giới thiệu, cam kết, đội xe, bước, CTA, FAQ | 3 nhóm nhu cầu: key, tiêu đề, mô tả, media_id, service_ids và nhãn CTA; ảnh background CTA |
| fleet_types | code, title, features | media_id, mô tả, nhãn nhu cầu; sức chứa/hành lý/tiện ích là dữ liệu nhập sau khi xác nhận; không suy đoán theo tên nhóm |
| Service | Nội dung và URL đang sở hữu, ảnh, bật trên trang chủ | Chọn dịch vụ cho từng nhóm nhu cầu; bỏ phụ thuộc vào việc đoán nhóm chỉ bằng từ khoá trong tiêu đề |
| WebsiteSettings / Curator | Logo, liên hệ, about_image_media_id, thư viện ảnh | Import bộ WebP đã duyệt, alt text và vai trò ảnh minh hoạ; không ghi đè media đang dùng |
| Testimonial / Post / Faq | Quy trình duyệt, bài xuất bản, nguồn SEO | Lọc phản hồi thật cho trang công khai; thiếu dữ liệu thì ẩn khối phù hợp |

Chuẩn bị dữ liệu tại Controller/Service; Blade chỉ hiển thị. Migration thêm trường/giá trị mặc định phải giữ các chỉnh sửa CMS hiện tại. Không dùng migrate:fresh hoặc reseed ghi đè database thật.

## 7. Bộ ảnh WebP cho phương án

Thư mục: `public/images/homepage-redesign/v1/`. Ảnh không chứa chữ hoặc nút. Mỗi ảnh có một vai trò, được xuất WebP tối ưu kèm bản nhỏ khi cần. Đây là bộ đề xuất chờ duyệt, chưa gắn vào trang đang chạy.

| File dự kiến | Vai trò / bố cục | Tỷ lệ mục tiêu |
|---|---|---|
| hero-vinfast.webp | Xe SUV VinFast màu teal trên hành trình miền Bắc; chủ thể lệch phải, bên trái đủ chỗ cho tiêu đề | Ngang 3:2, cắt hero khoảng 2.2:1 khi triển khai |
| service-private.webp | Gia đình và xe VinFast, khoảnh khắc chuẩn bị chuyến đi; thẻ khách cá nhân | 3:2 |
| service-partner.webp | Hai xe VinFast trước không gian đón đoàn/doanh nghiệp, không có logo công ty giả | 3:2 |
| service-wedding.webp | Xe VinFast trắng với hoa cưới tinh tế, không gian sáng trang nhã | 3:2 |
| fleet-vf8.webp | VF 8 màu teal, ảnh xe góc 3/4 trên nền studio sáng; minh hoạ nhóm phù hợp sau khi xác nhận xe thực tế | 3:2 |
| fleet-vf9.webp | VF 9 trắng, cùng góc chụp/nền và tỉ lệ ảnh xe | 3:2 |
| fleet-minibus.webp | Minibus trắng cùng bộ studio, không gắn nhãn VinFast hoặc dựng mẫu VinFast 16 chỗ không có cơ sở | 3:2 |
| journey-background.webp | Background đồi chè/đường cong miền Bắc, thoáng để đặt CTA bằng HTML; không gắn tên địa danh cụ thể nếu chỉ là cảnh minh hoạ | 3:2 |

Tham khảo nhận diện xe từ nguồn hãng: [VF 8](https://vinfastauto.com/vn_vi/hinh-anh-vinfast-vf-8-noi-that-ngoai-that), [VF 9](https://vinfastauto.com/vn_vi/hinh-anh-vinfast-vf-9-noi-that-ngoai-that). Hình AI không thay thế ảnh xác nhận của đội xe đang vận hành.

Đã tạo đủ 8 ảnh, mỗi ảnh có WebP master 1536 × 1024 và bản 768 × 512; tổng 16 file ảnh khoảng 2.00 MB (1.91 MiB). Dung lượng bản master khoảng 86–263 KB/ảnh. Bộ ảnh đã được kiểm tra nội dung và giải mã WebP. Xem bảng ảnh tại `public/previews/homepage-redesign-assets.html`; prompt gốc tại `docs/design/homepage-image-prompts.md`; kích thước, dung lượng và alt text tại `public/images/homepage-redesign/v1/manifest.json`.

## 8. Các bước triển khai sau khi anh duyệt phương án

1. Chốt bố cục, ảnh và nội dung ngắn của ba nhóm; kiểm tra danh sách xe, các trường thông tin còn thiếu.
2. Bổ sung những trường CMS nhỏ ở mục 6 và đưa media đã duyệt vào Curator; không tổ chức lại toàn bộ quản trị.
3. Làm desktop/mobile theo thứ tự hero → 3 nhóm dịch vụ → đội xe → tin cậy/quy trình → bài/FAQ/CTA; dùng chung header/footer.
4. Nối đầy đủ link dịch vụ, ngữ cảnh tư vấn và dữ liệu xe; ẩn phản hồi/số liệu minh hoạ khỏi phần bằng chứng thật.
5. Kiểm tra giao diện ở 390px, 768px, 1440px; bàn phím, focus, menu, modal, CTA theo cả ba nhu cầu và ảnh mobile.
6. Chạy build và tests; chứng minh sửa ảnh/nội dung trong CMS thay được phần frontend tương ứng; duyệt ảnh chụp desktop/mobile trước khi giao.

Tiêu chí nghiệm thu: nhìn đầu trang hiểu doanh nghiệp phục vụ cả ba nhóm; ba nhóm nổi bật ngang nhau; ảnh có chủ đích và nhất quán; nội dung chính dễ đọc; không tràn ngang; không có CTA vô tác dụng; không hardcode thông tin kinh doanh có thể thay đổi; giữ màu/font global; ảnh WebP có kích thước khai báo và lazy-load ngoài hero; chỉ tải một ảnh hero ưu tiên, không tải nhiều background lớn cùng lúc.
