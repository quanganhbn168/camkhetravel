<?php


$hero = (array) ($landingContent['hero'] ?? []);
$zalo_url = \App\Support\Landing\LandingView::zaloUrl((array) ($landingContent['contact'] ?? []));
$contact = (array) ($landingContent['contact'] ?? []);
$form_action = \App\Support\Localization\LocalizedUrl::route('contact.store');
$video_url = (string) ($hero['video_url'] ?? '');
$add_ons_background = (string) ($landingContent['add_ons_background'] ?? '');
$album_images = array_values(array_filter((array) ($landingContent['album_images'] ?? [])));
$hero_stats = (array) ($landingContent['hero_stats'] ?? []);

?>
<section class="wedding-hero" aria-labelledby="wedding-hero-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8 wedding-hero__grid">
        <div class="wedding-hero__content" data-aos="fade-up">
            <p class="wedding-kicker">THT MEDIA WEDDING</p>
            <h1 id="wedding-hero-title"><?php echo e($hero['title'] ?? 'Quay chụp phóng sự cưới'); ?></h1>
            <p class="wedding-hero__summary"><?php echo e($hero['summary'] ?? ''); ?></p>
            <div class="wedding-actions">
                <a class="tht-landing-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer"><span class="tht-landing-zalo-icon" aria-hidden="true"></span>Nhận ưu đãi &amp; tư vấn miễn phí</a>
                <a class="wedding-button wedding-button--light" href="#bang-gia">Xem bảng giá</a>
                <?php if ($video_url !== '') : ?>
                    <a class="wedding-text-link glightbox" href="<?php echo e($video_url); ?>" data-type="video" data-gallery="wedding-showreel">Xem video phóng sự cưới <i class="fa-solid fa-play" aria-hidden="true"></i></a>
                <?php else : ?>
                    <a class="wedding-text-link" href="#video-phong-su">Xem video phóng sự cưới <i class="fa-solid fa-play" aria-hidden="true"></i></a>
                <?php endif; ?>
            </div>
            <?php if ($hero_stats) : ?>
                <div class="wedding-hero__stats" aria-label="Dấu ấn THT Media Wedding">
                    <?php foreach ($hero_stats as $stat) : ?>
                        <div class="wedding-hero__stat">
                            <strong><?php echo e($stat['value'] ?? ''); ?></strong>
                            <span><?php echo e($stat['label'] ?? ''); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="wedding-hero__media" data-aos="fade-up" data-aos-delay="100">
            <img src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($hero['image'] ?? '')); ?>" alt="<?php echo e($hero['image_alt'] ?? ''); ?>" width="1200" height="1200" fetchpriority="high">
        </div>
    </div>
</section>

<section class="wedding-service-info" aria-label="Thông tin dịch vụ">
    <div class="wedding-service-info__grid">
        <?php foreach (($landingContent['service_info'] ?? []) as $index => $item) : ?>
            <?php
            $service_info_title = is_array($item) ? (string) ($item['title'] ?? '') : (string) $item;
            $service_info_image = is_array($item) ? (string) ($item['image'] ?? '') : '';
            $service_info_hover_image = is_array($item) ? (string) ($item['hover_image'] ?? $service_info_image) : '';
            $service_info_url = is_array($item) ? (string) ($item['url'] ?? '#dich-vu') : '#dich-vu';
            ?>
            <a class="wedding-service-info__item" href="<?php echo e($service_info_url); ?>" aria-label="Khám phá dịch vụ <?php echo e($service_info_title); ?>"<?php if ($service_info_hover_image !== '') : ?> style="--wedding-service-info-hover-image: url('<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($service_info_hover_image)); ?>');"<?php endif; ?> data-aos="fade-up" data-aos-delay="<?php echo e((string) ($index * 45)); ?>">
                <?php if ($service_info_image !== '') : ?><img src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($service_info_image)); ?>" alt="<?php echo e($service_info_title); ?>" loading="lazy"><?php endif; ?>
                <div><span>0<?php echo e((string) ($index + 1)); ?></span><strong><?php echo e($service_info_title); ?></strong><small>Khám phá dịch vụ <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></small></div>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="wedding-section" id="dich-vu" aria-labelledby="wedding-services-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <header class="wedding-section-head" data-aos="fade-up"><p class="wedding-kicker">DỊCH VỤ</p><h2 id="wedding-services-title">Dịch vụ quay chụp phóng sự cưới</h2><p>Khách hàng có thể lựa chọn từng dịch vụ riêng hoặc kết hợp quay và chụp trong cùng một gói. THT Media sẽ tư vấn dựa trên lịch trình, địa điểm tổ chức và sản phẩm khách hàng muốn nhận sau ngày cưới.</p></header>
        <div class="wedding-service-grid">
            <?php foreach (($landingContent['services'] ?? []) as $index => $service) : ?>
                <?php $service_image = (string) ($service['image'] ?? ''); ?>
                <article id="<?php echo e($service['anchor'] ?? 'dich-vu-' . ($index + 1)); ?>" class="wedding-service-card<?php echo $service_image !== '' ? ' has-image' : ''; ?>"<?php if ($service_image !== '') : ?> style="--wedding-service-image: url('<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($service_image)); ?>');"<?php endif; ?> data-aos="fade-up" data-aos-delay="<?php echo e((string) (($index % 2) * 65)); ?>">
                    <h3><?php echo e($service['title'] ?? ''); ?></h3>
                    <p><?php echo e($service['description'] ?? ''); ?></p>
                    <ul><?php foreach (($service['items'] ?? []) as $item) : ?><li><i class="fa-solid fa-check" aria-hidden="true"></i><?php echo e($item); ?></li><?php endforeach; ?></ul>
                    <a class="tht-landing-zalo-cta wedding-service-card__cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer"><span class="tht-landing-zalo-icon" aria-hidden="true"></span><?php echo e($service['cta'] ?? 'Nhận ưu đãi & tư vấn miễn phí'); ?><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="wedding-section wedding-section--soft wedding-addons-section"<?php if ($add_ons_background !== '') : ?> style="--wedding-addons-image: url('<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($add_ons_background)); ?>');"<?php endif; ?> aria-labelledby="wedding-addons-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8 wedding-addons">
        <header class="wedding-section-head" data-aos="fade-up"><p class="wedding-kicker">THEO NHU CẦU</p><h2 id="wedding-addons-title">Hạng mục bổ sung</h2><p>Chọn một hoặc nhiều hạng mục, THT Media sẽ gợi ý gói phù hợp với lịch cưới của anh/chị.</p></header>
        <div class="wedding-addon-selector" data-aos="fade-up">
            <div class="wedding-addon-options">
                <p class="wedding-addon-options__title">Chọn hạng mục cần thêm <span>Có thể chọn nhiều</span></p>
                <div class="wedding-addons__grid" aria-label="Chọn hạng mục bổ sung">
                    <?php foreach (($landingContent['add_ons'] ?? []) as $item) : ?>
                        <?php $addon_label = is_array($item) ? (string) ($item['label'] ?? '') : (string) $item; $addon_package = is_array($item) ? (string) ($item['package'] ?? 'combo') : 'combo'; ?>
                        <button class="wedding-addon-choice" type="button" aria-pressed="false" data-addon-label="<?php echo e($addon_label); ?>" data-package="<?php echo e($addon_package); ?>"><i class="fa-solid fa-plus" aria-hidden="true"></i><span><?php echo e($addon_label); ?></span></button>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="wedding-addon-recommendation" aria-live="polite">
                <p class="wedding-addon-recommendation__eyebrow">GÓI TỐI ƯU ĐƯỢC ĐỀ XUẤT</p>
                <div class="wedding-addon-recommendation__main">
                    <div><strong data-recommendation-name>Combo quay và chụp phóng sự cưới</strong><p data-recommendation-description>Phù hợp nhất để lưu giữ trọn vẹn lễ ăn hỏi và lễ đón dâu, đồng bộ cả ảnh lẫn video.</p></div>
                    <span data-recommendation-price>7.000.000 VNĐ</span>
                </div>
                <p class="wedding-addon-recommendation__selection" data-selected-addons>Chọn hạng mục để tinh chỉnh gói theo nhu cầu của anh/chị.</p>
            </div>
        </div>
        <div class="wedding-inline-actions"><a class="tht-landing-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer"><span class="tht-landing-zalo-icon" aria-hidden="true"></span>Nhận ưu đãi &amp; tư vấn miễn phí</a></div>
    </div>
</section>

<section class="wedding-section" id="video-phong-su" aria-labelledby="wedding-videos-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <header class="wedding-section-head" data-aos="fade-up"><p class="wedding-kicker">DỰ ÁN VIDEO</p><h2 id="wedding-videos-title">Video phóng sự cưới</h2></header>
        <?php if (! empty($landingContent['videos'])) : ?>
            <div class="wedding-video-grid">
                <?php foreach ($landingContent['videos'] as $video) : ?><a class="wedding-video-card glightbox" href="<?php echo e($video['url']); ?>" data-type="video" data-gallery="wedding-videos"><img src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($video['thumbnail'])); ?>" alt="<?php echo e($video['title']); ?>" loading="lazy"><span><b><?php echo e($video['title']); ?></b><small><?php echo e($video['meta']); ?></small></span><i class="fa-solid fa-play" aria-hidden="true"></i></a><?php endforeach; ?>
            </div>
        <?php else : ?>
            <div class="wedding-empty-project" data-aos="fade-up"><img src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($hero['image'] ?? '')); ?>" alt="" loading="lazy"><div><strong>Video mẫu sẽ được cập nhật</strong><p>Liên lạc qua Zalo để THT Media gửi video phóng sự phù hợp với nhu cầu của anh/chị.</p><a class="tht-landing-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer"><span class="tht-landing-zalo-icon" aria-hidden="true"></span>Liên lạc qua Zalo</a></div></div>
        <?php endif; ?>
    </div>
</section>

<section class="wedding-section wedding-section--soft" id="album-anh" aria-labelledby="wedding-album-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <header class="wedding-section-head" data-aos="fade-up"><p class="wedding-kicker">ALBUM ẢNH</p><h2 id="wedding-album-title">Album ảnh phóng sự cưới</h2><p>Một số hình ảnh được THT Media thực hiện trong lễ ăn hỏi, lễ đón dâu và tiệc cưới của khách hàng.</p></header>
        <div class="wedding-album-grid" aria-label="Album ảnh phóng sự cưới">
            <?php if ($album_images) : ?>
                <?php foreach ($album_images as $index => $image) : ?>
                    <a class="wedding-album-item glightbox" href="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($image)); ?>" data-gallery="wedding-album" data-title="Ảnh phóng sự cưới <?php echo e(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?>" data-aos="fade-up" data-aos-delay="<?php echo e((string) (($index % 4) * 35)); ?>"><img src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($image)); ?>" alt="Khoảnh khắc phóng sự cưới <?php echo e((string) ($index + 1)); ?>" loading="lazy"></a>
                <?php endforeach; ?>
            <?php else : ?>
                <?php for ($slot = 1; $slot <= (int) ($landingContent['album_slots'] ?? 12); $slot++) : ?><div class="wedding-album-slot" data-aos="fade-up" data-aos-delay="<?php echo e((string) (($slot % 4) * 35)); ?>"><i class="fa-regular fa-image" aria-hidden="true"></i><span>Ảnh dự án <?php echo e((string) $slot); ?></span></div><?php endfor; ?>
            <?php endif; ?>
        </div>
        <div class="wedding-inline-actions"><a class="wedding-button wedding-button--outline wedding-zalo-link" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer"><span class="tht-landing-zalo-icon" aria-hidden="true"></span>Liên lạc qua Zalo để xem dự án phù hợp</a></div>
    </div>
</section>

<section class="wedding-section wedding-invitations" id="thiep-moi-online" aria-labelledby="wedding-invitations-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <header class="wedding-section-head wedding-section-head--center" data-aos="fade-up">
            <p class="wedding-kicker">THIỆP MỜI ONLINE</p>
            <h2 id="wedding-invitations-title">Mẫu thiệp mời online</h2>
            <p>Thiệp mời được thiết kế riêng theo thông tin ngày cưới, giúp khách mời xem địa điểm, thời gian và gửi lời chúc thuận tiện. Hạng mục này đang được tặng kèm trong các gói combo phù hợp.</p>
        </header>
        <div class="wedding-invitation-grid">
            <?php foreach (($landingContent['online_invitations'] ?? []) as $index => $invitation) : ?>
                <a class="wedding-invitation-card<?php echo ! empty($invitation['scroll_preview']) ? ' wedding-invitation-card--scroll' : ''; ?>" href="<?php echo e($invitation['url'] ?? '#'); ?>" target="_blank" rel="noopener noreferrer" data-aos="fade-up" data-aos-delay="<?php echo e((string) ($index * 55)); ?>">
                    <span class="wedding-invitation-card__image"><img src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($invitation['image'] ?? '')); ?>" alt="Mẫu thiệp mời online <?php echo e($invitation['names'] ?? 'THT Media'); ?>" loading="lazy"><?php if (! empty($invitation['scroll_preview'])) : ?><span class="wedding-invitation-card__hint"><i class="fa-solid fa-computer-mouse" aria-hidden="true"></i>Rê chuột để xem thiệp</span><?php endif; ?></span>
                    <span class="wedding-invitation-card__body"><small>THIỆP MỜI ONLINE · <?php echo e($invitation['date'] ?? ''); ?></small><strong><?php echo e($invitation['names'] ?? ''); ?></strong><em>Xem mẫu thiệp <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></em></span>
                </a>
            <?php endforeach; ?>
        </div>
        <div class="wedding-inline-actions"><a class="tht-landing-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer"><span class="tht-landing-zalo-icon" aria-hidden="true"></span>Nhận ưu đãi thiệp mời online</a></div>
    </div>
</section>

<section class="wedding-section" id="bang-gia" aria-labelledby="wedding-pricing-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <header class="wedding-section-head wedding-section-head--center" data-aos="fade-up"><p class="wedding-kicker">BẢNG GIÁ</p><h2 id="wedding-pricing-title">Bảng giá quay chụp phóng sự cưới</h2></header>
        <div class="wedding-price-grid">
            <?php foreach (($landingContent['pricing'] ?? []) as $package) : ?>
                <article class="wedding-price-card<?php echo ! empty($package['featured']) ? ' is-featured' : ''; ?>" data-aos="fade-up">
                    <?php if (! empty($package['featured'])) : ?><span class="wedding-price-card__badge">ĐƯỢC LỰA CHỌN NHIỀU</span><?php endif; ?>
                    <h3><?php echo e($package['name']); ?></h3><strong class="wedding-price-card__amount"><?php echo e($package['price']); ?></strong>
                    <ul><?php foreach (($package['items'] ?? []) as $item) : ?><li><i class="fa-solid fa-check" aria-hidden="true"></i><?php echo e($item); ?></li><?php endforeach; ?></ul>
                    <?php if (! empty($package['note'])) : ?><p class="wedding-price-card__note"><?php echo e($package['note']); ?></p><?php endif; ?>
                    <a class="tht-landing-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer"><span class="tht-landing-zalo-icon" aria-hidden="true"></span><?php echo e($package['cta']); ?></a>
                </article>
            <?php endforeach; ?>
        </div>
        <p class="wedding-pricing-note"><?php echo e($landingContent['pricing_note'] ?? ''); ?></p>
        <div class="wedding-inline-actions"><a class="tht-landing-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer"><span class="tht-landing-zalo-icon" aria-hidden="true"></span>Liên lạc qua Zalo để kiểm tra lịch</a></div>
    </div>
</section>

<section class="wedding-section wedding-section--dark" aria-labelledby="wedding-deliverables-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8"><header class="wedding-section-head" data-aos="fade-up"><p class="wedding-kicker">BÀN GIAO</p><h2 id="wedding-deliverables-title">Sản phẩm bàn giao</h2></header><div class="wedding-deliverables-grid"><?php foreach (($landingContent['deliverables'] ?? []) as $group) : ?><article data-aos="fade-up"><h3><?php echo e($group['title']); ?></h3><ul><?php foreach (($group['items'] ?? []) as $item) : ?><li><?php echo e($item); ?></li><?php endforeach; ?></ul></article><?php endforeach; ?></div><a class="tht-landing-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer"><span class="tht-landing-zalo-icon" aria-hidden="true"></span>Liên lạc qua Zalo để xem sản phẩm mẫu</a></div>
</section>

<section class="wedding-section" id="quy-trinh" aria-labelledby="wedding-process-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8"><header class="wedding-section-head wedding-section-head--center" data-aos="fade-up"><p class="wedding-kicker">QUY TRÌNH</p><h2 id="wedding-process-title">Quy trình thực hiện</h2></header><ol class="wedding-process-list"><?php foreach (($landingContent['process'] ?? []) as $item) : ?><li data-aos="fade-up"><span><?php echo e($item['step']); ?></span><div><h3><?php echo e($item['title']); ?></h3><p><?php echo e($item['text']); ?></p></div></li><?php endforeach; ?></ol><div class="wedding-inline-actions"><a class="tht-landing-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer"><span class="tht-landing-zalo-icon" aria-hidden="true"></span>Liên lạc qua Zalo để giữ lịch</a></div></div>
</section>

<section class="wedding-section wedding-section--soft" id="hau-truong" aria-labelledby="wedding-bts-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8 wedding-bts"><header class="wedding-section-head" data-aos="fade-up"><p class="wedding-kicker">HẬU TRƯỜNG</p><h2 id="wedding-bts-title">Hậu trường quay chụp phóng sự cưới</h2><p><?php echo e($landingContent['bts']['body'] ?? ''); ?></p></header><div class="wedding-bts__grid"><?php foreach (($landingContent['bts']['items'] ?? []) as $index => $item) : ?><?php $bts_text = is_array($item) ? (string) ($item['text'] ?? '') : (string) $item; $bts_image = is_array($item) ? (string) ($item['image'] ?? '') : ''; ?><article class="wedding-bts__item" data-aos="fade-up"><?php if ($bts_image !== '') : ?><img src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($bts_image)); ?>" alt="Hậu trường quay chụp phóng sự cưới <?php echo e((string) ($index + 1)); ?>" loading="lazy"><?php endif; ?><div><i class="fa-solid fa-camera-retro" aria-hidden="true"></i><span><?php echo e($bts_text); ?></span></div></article><?php endforeach; ?></div><a class="wedding-button wedding-button--outline wedding-zalo-link" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer"><span class="tht-landing-zalo-icon" aria-hidden="true"></span>Liên lạc qua Zalo với ekip</a></div>
</section>

<section class="wedding-section" aria-labelledby="wedding-reasons-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8"><header class="wedding-section-head" data-aos="fade-up"><p class="wedding-kicker">THT MEDIA WEDDING</p><h2 id="wedding-reasons-title">Lý do chọn THT Media</h2></header><div class="wedding-reasons-grid"><?php foreach (($landingContent['reasons'] ?? []) as $index => $reason) : ?><article data-aos="fade-up"><span>0<?php echo e((string) ($index + 1)); ?></span><h3><?php echo e($reason['title']); ?></h3><p><?php echo e($reason['text']); ?></p></article><?php endforeach; ?></div><a class="tht-landing-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer"><span class="tht-landing-zalo-icon" aria-hidden="true"></span>Liên lạc qua Zalo</a></div>
</section>

<section class="wedding-section wedding-section--soft" id="phan-hoi" aria-labelledby="wedding-testimonial-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8"><header class="wedding-section-head wedding-section-head--center" data-aos="fade-up"><p class="wedding-kicker">PHẢN HỒI KHÁCH HÀNG</p><h2 id="wedding-testimonial-title">Phản hồi của khách hàng</h2></header><div class="swiper wedding-testimonials-swiper" data-aos="fade-up"><div class="swiper-wrapper"><?php foreach (($landingContent['testimonials'] ?? []) as $item) : ?><?php $testimonial_image = (string) ($item['image'] ?? ''); ?><article class="swiper-slide wedding-testimonial-card"><?php if ($testimonial_image !== '') : ?><a class="wedding-testimonial-card__image glightbox" href="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($testimonial_image)); ?>" data-gallery="wedding-testimonial-proof" data-title="Phản hồi từ <?php echo e($item['name'] ?? 'khách hàng'); ?>"><img src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($testimonial_image)); ?>" alt="Phản hồi từ <?php echo e($item['name'] ?? 'khách hàng'); ?>" loading="lazy"></a><?php endif; ?><div class="wedding-testimonial-card__body"><i class="fa-solid fa-quote-left" aria-hidden="true"></i><p><?php echo e($item['text'] ?? ''); ?></p><footer><strong><?php echo e($item['name'] ?? 'Khách hàng THT Media'); ?></strong><span><?php echo e($item['role'] ?? 'Khách hàng'); ?></span></footer></div></article><?php endforeach; ?></div><div class="swiper-pagination wedding-testimonials-pagination"></div></div></div>
</section>

<section class="wedding-section" id="faq" aria-labelledby="wedding-faq-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8"><header class="wedding-section-head wedding-section-head--center" data-aos="fade-up"><p class="wedding-kicker">CÂU HỎI THƯỜNG GẶP</p><h2 id="wedding-faq-title">Câu hỏi thường gặp</h2></header><div class="wedding-faq-list"><?php foreach (($landingContent['faq'] ?? []) as $index => $item) : ?><details data-aos="fade-up"<?php echo $index === 0 ? ' open' : ''; ?>><summary><?php echo e($item['question']); ?><i class="fa-solid fa-plus" aria-hidden="true"></i></summary><p><?php echo e($item['answer']); ?></p></details><?php endforeach; ?></div><div class="wedding-inline-actions"><a class="tht-landing-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer"><span class="tht-landing-zalo-icon" aria-hidden="true"></span>Liên lạc qua Zalo để được giải đáp</a></div></div>
</section>

<section class="wedding-section wedding-contact" id="lien-he" aria-labelledby="wedding-contact-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8 wedding-contact__grid"><div data-aos="fade-up"><p class="wedding-kicker">LIÊN HỆ TƯ VẤN</p><h2 id="wedding-contact-title"><?php echo e($landingContent['contact_section']['title'] ?? 'Liên hệ tư vấn'); ?></h2><p><?php echo e($landingContent['contact_section']['body'] ?? ''); ?></p><div class="tht-landing-zalo-contact"><p class="tht-landing-zalo-contact__copy">Liên lạc qua Zalo để được phản hồi nhanh và kiểm tra lịch ekip.</p><a class="tht-landing-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer"><span class="tht-landing-zalo-icon" aria-hidden="true"></span>Liên lạc qua Zalo</a></div></div><div class="wedding-contact__form" data-aos="fade-up" data-aos-delay="80"><div class="wedding-contact__form-label">Hoặc để lại thông tin</div><form class="wedding-lead-form" action="<?php echo e($form_action); ?>" method="POST"><x-landing.lead-fields :landing-page="$landingPage ?? null" :service="$service ?? null" block-id="wedding-contact" return-anchor="lien-he" /><label>Họ và tên<input type="text" name="name" value="{{ old('name') }}" required autocomplete="name" maxlength="255" placeholder="Nhập họ và tên"></label><label>Số điện thoại<input type="tel" name="phone" value="{{ old('phone') }}" required autocomplete="tel" maxlength="32" placeholder="Nhập số điện thoại hoặc số Zalo"></label><label>Nội dung<textarea name="message" rows="5" maxlength="5000" placeholder="Ngày tổ chức, địa điểm và dịch vụ anh/chị đang quan tâm (tùy chọn)">{{ old('message') }}</textarea></label><button class="wedding-button wedding-button--dark" type="submit">Gửi yêu cầu tư vấn</button></form></div></div>
</section>
