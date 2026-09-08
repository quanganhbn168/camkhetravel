<?php


$d = $landingContent;
$hero = $d['hero'] ?? [];
$contact = $d['contact'] ?? [];
$promotion = $d['promotion'] ?? [];
$zalo_url = \App\Support\Landing\LandingView::zaloUrl($contact);
$hero_image = \App\Support\Landing\LandingRegistry::assetUrl($hero['image'] ?? 'assets/images/landing-placeholder.svg');
$reason_image = trim((string) ($d['reasons']['image'] ?? ''));

?>
<div class="tht-landing-profile-page">
    <section class="tht-landing-profile-hero" aria-labelledby="profile-hero-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8 tht-landing-profile-hero__grid">
            <div data-aos="fade-up">
                <h1 id="profile-hero-title"><?php echo e($hero['title'] ?? ''); ?></h1>
                <p class="tht-landing-profile-hero__summary"><?php echo e($hero['summary'] ?? ''); ?></p>
                <div class="tht-landing-profile-actions">
                    <div class="tht-landing-profile-cta-stack">
                        <span class="tht-landing-profile-offer-signal"><?php echo e($promotion['badge'] ?? 'ƯU ĐÃI ĐANG DIỄN RA'); ?></span>
                        <a class="tht-landing-profile-button tht-landing-profile-button--primary tht-landing-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer"><span class="tht-landing-zalo-icon" aria-hidden="true"></span><?php echo e($hero['primary'] ?? 'NHẬN ƯU ĐÃI NGAY'); ?></a>
                    </div>
                    <a class="tht-landing-profile-button tht-landing-profile-button--text" href="#uu-dai"><?php echo e($hero['secondary'] ?? 'XEM ƯU ĐÃI'); ?><i class="fa-solid fa-arrow-down" aria-hidden="true"></i></a>
                </div>
                <ul class="tht-landing-profile-hero__highlights"><?php foreach (($hero['highlights'] ?? []) as $highlight) : ?><li><i class="fa-solid fa-circle-check" aria-hidden="true"></i><?php echo e($highlight); ?></li><?php endforeach; ?></ul>
                <p class="tht-landing-profile-hero__microcopy"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><?php echo e($hero['microcopy'] ?? ''); ?></p>
            </div>
            <figure class="tht-landing-profile-hero__image" data-aos="fade-left" data-aos-delay="100">
                <img src="<?php echo e($hero_image); ?>" alt="<?php echo e($hero['image_alt'] ?? 'Thiết kế profile và hồ sơ năng lực doanh nghiệp'); ?>" width="1200" height="900" loading="eager" decoding="async">
                <figcaption>Nội dung · hình ảnh · cấu trúc · thiết kế</figcaption>
            </figure>
        </div>
    </section>

    <section class="tht-landing-profile-section tht-landing-profile-problems" aria-labelledby="profile-problems-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-profile-section__heading" data-aos="fade-up">
                <h2 id="profile-problems-title" class="tht-landing-profile-section__title"><?php echo e($d['problems']['eyebrow'] ?? ''); ?></h2>
                <p class="tht-landing-profile-section__description"><?php echo e($d['problems']['title'] ?? ''); ?></p>
            </div>
            <div class="tht-landing-profile-problem-grid"><?php foreach (($d['problems']['items'] ?? []) as $index => $item) : ?><article data-aos="fade-up" data-aos-delay="<?php echo e($index * 60); ?>"><span>0<?php echo e($index + 1); ?></span><h3><?php echo e($item['title'] ?? ''); ?></h3><p><?php echo e($item['text'] ?? ''); ?></p></article><?php endforeach; ?></div>
            <p class="tht-landing-profile-statement"><i class="fa-solid fa-quote-left" aria-hidden="true"></i><?php echo e($d['problems']['footer'] ?? ''); ?></p>
        </div>
    </section>

    <section class="tht-landing-profile-section tht-landing-profile-services" id="dich-vu" aria-labelledby="profile-services-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-profile-section__heading tht-landing-profile-services__heading" data-aos="fade-up"><h2 id="profile-services-title" class="tht-landing-profile-section__title"><?php echo e($d['services']['eyebrow'] ?? ''); ?></h2><p class="tht-landing-profile-section__description"><?php echo e($d['services']['title'] ?? ''); ?></p><p><?php echo e($d['services']['note'] ?? ''); ?></p></div>
            <div class="tht-landing-profile-accordion tht-landing-profile-services__accordion" data-profile-accordion data-aos="fade-up"><?php foreach (($d['services']['items'] ?? []) as $index => $item) : ?><div class="tht-landing-profile-accordion__item<?php echo $index === 0 ? ' is-open' : ''; ?>"><h3><button type="button" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>"><span><b>0<?php echo e($index + 1); ?></b><?php echo e($item['title'] ?? ''); ?></span><span class="tht-landing-profile-accordion__toggle" aria-hidden="true">+</span></button></h3><div class="tht-landing-profile-accordion__panel"><p><?php echo e($item['text'] ?? ''); ?></p></div></div><?php endforeach; ?></div>
            <div class="tht-landing-profile-services__cta" data-aos="fade-up">
                <div>
                    <span class="tht-landing-profile-offer-signal"><?php echo e($promotion['badge'] ?? 'ƯU ĐÃI ĐANG DIỄN RA'); ?></span>
                    <p><?php echo e($d['services']['cta_note'] ?? ''); ?></p>
                </div>
                <a class="tht-landing-profile-button tht-landing-profile-button--primary tht-landing-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer"><span class="tht-landing-zalo-icon" aria-hidden="true"></span><?php echo e($d['services']['cta'] ?? 'NHẬN ƯU ĐÃI NGAY'); ?></a>
            </div>
        </div>
    </section>

    <section class="tht-landing-profile-section tht-landing-profile-plans" aria-labelledby="profile-plans-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-profile-section__heading" data-aos="fade-up">
                <h2 id="profile-plans-title" class="tht-landing-profile-section__title"><?php echo e($d['plans']['eyebrow'] ?? ''); ?></h2>
                <p class="tht-landing-profile-section__description"><?php echo e($d['plans']['title'] ?? ''); ?></p>
                <span class="tht-landing-profile-offer-signal"><?php echo e($promotion['badge'] ?? 'ƯU ĐÃI ĐANG DIỄN RA'); ?></span>
            </div>
            <div class="tht-landing-profile-plan-grid"><?php foreach (($d['plans']['items'] ?? []) as $index => $item) : ?><article class="<?php echo $index === 1 ? 'is-featured' : ''; ?>" data-aos="fade-up" data-aos-delay="<?php echo e($index * 80); ?>"><h3><?php echo e($item['title'] ?? ''); ?></h3><?php if (! empty($item['price_label'])) : ?><div class="tht-landing-price"><strong><?php echo e($item['price_label']); ?></strong><?php if (! empty($item['price_note'])) : ?><small><?php echo e($item['price_note']); ?></small><?php endif; ?></div><?php endif; ?><p><?php echo e($item['fit'] ?? ''); ?></p><ul><?php foreach (($item['items'] ?? []) as $detail) : ?><li><i class="fa-solid fa-check" aria-hidden="true"></i><?php echo e($detail); ?></li><?php endforeach; ?></ul><div class="tht-landing-profile-plan-action"><a class="tht-landing-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer"><span class="tht-landing-zalo-icon" aria-hidden="true"></span><?php echo e($item['cta'] ?? 'NHẬN ƯU ĐÃI NGAY'); ?></a></div></article><?php endforeach; ?></div>
        </div>
    </section>

    <section class="tht-landing-profile-promotion" id="uu-dai" aria-labelledby="profile-promotion-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-profile-promotion__inner" data-aos="fade-up">
                <div class="tht-landing-profile-promotion__copy">
                    <p class="tht-landing-profile-offer-signal tht-landing-profile-offer-signal--on-dark"><?php echo e($promotion['badge'] ?? 'ƯU ĐÃI ĐANG DIỄN RA'); ?></p>
                    <h2 id="profile-promotion-title"><span><?php echo e($promotion['headline_lead'] ?? 'Giảm ngay'); ?></span><strong><?php echo e($promotion['discount'] ?? '50.000đ/trang'); ?></strong><span><?php echo e($promotion['headline_tail'] ?? 'trong thời gian ưu đãi'); ?></span></h2>
                    <p class="tht-landing-profile-promotion__body"><?php echo e($promotion['body'] ?? ''); ?></p>
                    <a class="tht-landing-profile-button tht-landing-profile-button--primary tht-landing-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer"><span class="tht-landing-zalo-icon" aria-hidden="true"></span><?php echo e($promotion['cta'] ?? 'NHẬN ƯU ĐÃI NGAY'); ?></a>
                </div>
                <div class="tht-landing-profile-promotion__timer" data-profile-countdown data-deadline="<?php echo e($promotion['deadline'] ?? ''); ?>" data-active-message="<?php echo e($promotion['deadline_status'] ?? ''); ?>" data-expired-message="<?php echo e($promotion['expired_status'] ?? ''); ?>" aria-live="polite">
                    <p class="tht-landing-profile-promotion__timer-label"><?php echo e($promotion['deadline_label'] ?? 'Ưu đãi có thời hạn'); ?></p>
                    <div class="tht-landing-profile-countdown__units">
                        <div><strong data-countdown-days>10</strong><span>Ngày</span></div>
                        <div><strong data-countdown-hours>00</strong><span>Giờ</span></div>
                        <div><strong data-countdown-minutes>00</strong><span>Phút</span></div>
                        <div><strong data-countdown-seconds>00</strong><span>Giây</span></div>
                    </div>
                    <p class="tht-landing-profile-countdown__status" data-countdown-status></p>
                </div>
            </div>
        </div>
    </section>

    <section class="tht-landing-profile-section tht-landing-profile-portfolio" id="portfolio" aria-labelledby="profile-portfolio-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-profile-section__heading" data-aos="fade-up"><h2 id="profile-portfolio-title" class="tht-landing-profile-section__title"><?php echo e($d['portfolio']['eyebrow'] ?? ''); ?></h2><p class="tht-landing-profile-section__description"><?php echo e($d['portfolio']['title'] ?? ''); ?></p></div>
            <div class="tht-landing-profile-portfolio-grid">
                <?php foreach (($d['portfolio']['items'] ?? []) as $index => $item) : ?>
                    <article class="tht-landing-profile-portfolio-card" data-aos="fade-up" data-aos-delay="<?php echo e(($index % 3) * 70); ?>">
                        <img src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($item['image'] ?? 'assets/images/landing-placeholder.svg')); ?>" alt="<?php echo e($item['alt'] ?? $item['title'] ?? 'Tư liệu profile doanh nghiệp'); ?>" loading="lazy">
                        <div>
                            <span><?php echo e($item['label'] ?? 'Sản phẩm thực tế'); ?></span>
                            <h3><?php echo e($item['title'] ?? ''); ?></h3>
                            <?php if (! empty($item['preview_url'])) : ?>
                                <a class="tht-landing-profile-portfolio-card__preview" href="<?php echo e($item['preview_url']); ?>" target="_blank" rel="noopener noreferrer"><?php echo e($item['preview_label'] ?? 'Xem profile'); ?><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <p class="tht-landing-profile-note"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><?php echo e($d['portfolio']['note'] ?? ''); ?></p>
        </div>
    </section>

    <section class="tht-landing-profile-section tht-landing-profile-reasons" aria-labelledby="profile-reasons-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-profile-reasons__layout">
                <figure class="tht-landing-profile-reasons__image" data-aos="fade-right">
                    <?php if ($reason_image !== '') : ?>
                        <img src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($reason_image)); ?>" alt="<?php echo e($d['reasons']['image_alt'] ?? ''); ?>" loading="lazy">
                    <?php else : ?>
                        <div class="tht-landing-profile-reasons__image-placeholder"><i class="fa-regular fa-image" aria-hidden="true"></i><span>ẢNH THT MEDIA</span><small>Chờ bổ sung ảnh thực tế</small></div>
                    <?php endif; ?>
                </figure>
                <div class="tht-landing-profile-reasons__content">
                    <div class="tht-landing-profile-section__heading" data-aos="fade-left"><h2 id="profile-reasons-title" class="tht-landing-profile-section__title"><?php echo e($d['reasons']['eyebrow'] ?? ''); ?></h2><p class="tht-landing-profile-section__description"><?php echo e($d['reasons']['title'] ?? ''); ?></p></div>
                    <div class="tht-landing-profile-reason-grid"><?php foreach (($d['reasons']['items'] ?? []) as $index => $item) : ?><article data-aos="fade-up" data-aos-delay="<?php echo e($index * 70); ?>"><i class="fa-solid <?php echo e($item['icon'] ?? 'fa-check'); ?>" aria-hidden="true"></i><h3><?php echo e($item['title'] ?? ''); ?></h3><p><?php echo e($item['text'] ?? ''); ?></p></article><?php endforeach; ?></div>
                </div>
            </div>
        </div>
    </section>

    <section class="tht-landing-profile-section tht-landing-profile-faq" id="faq" aria-labelledby="profile-faq-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-profile-faq__grid"><div class="tht-landing-profile-section__heading" data-aos="fade-right"><h2 id="profile-faq-title" class="tht-landing-profile-section__title"><?php echo e($d['faq']['eyebrow'] ?? ''); ?></h2><p class="tht-landing-profile-section__description"><?php echo e($d['faq']['title'] ?? ''); ?></p></div><div class="tht-landing-profile-accordion tht-landing-profile-accordion--faq" data-profile-accordion data-aos="fade-left"><?php foreach (($d['faq']['items'] ?? []) as $index => $item) : ?><div class="tht-landing-profile-accordion__item<?php echo $index === 0 ? ' is-open' : ''; ?>"><h3><button type="button" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>"><span><?php echo e($item['question'] ?? ''); ?></span><i class="fa-solid fa-plus" aria-hidden="true"></i></button></h3><div class="tht-landing-profile-accordion__panel"><p><?php echo e($item['answer'] ?? ''); ?></p></div></div><?php endforeach; ?></div></div>
        </div>
    </section>

    <section class="tht-landing-profile-final" id="lien-he" aria-labelledby="profile-final-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8 tht-landing-profile-final__inner" data-aos="fade-up">
            <div class="tht-landing-profile-final__copy">
                <span class="tht-landing-profile-offer-signal tht-landing-profile-offer-signal--on-dark"><?php echo e($promotion['badge'] ?? 'ƯU ĐÃI ĐANG DIỄN RA'); ?></span>
                <h2 id="profile-final-title"><?php echo e($d['contact_section']['title'] ?? ''); ?></h2>
                <p><?php echo e($d['contact_section']['body'] ?? ''); ?></p>
            </div>
            <div class="tht-landing-profile-final__actions"><a class="tht-landing-profile-button tht-landing-profile-button--light tht-landing-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer"><span class="tht-landing-zalo-icon" aria-hidden="true"></span><?php echo e($promotion['cta'] ?? 'NHẬN ƯU ĐÃI NGAY'); ?></a><button class="tht-landing-profile-button tht-landing-profile-button--outline" type="button" data-landing-modal-open="#tht-landing-contact-modal"><?php echo e($d['contact_section']['cta'] ?? 'GỬI PROFILE'); ?> <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button></div>
            <p class="tht-landing-profile-final__message">Gợi ý tin nhắn: “Tôi cần tư vấn thiết kế profile. Ngành nghề: … Mục đích sử dụng: … Đã có nội dung: có / chưa. Đã có ảnh doanh nghiệp: có / chưa. Ngôn ngữ dự kiến: …”</p>
        </div>
    </section>
</div>
