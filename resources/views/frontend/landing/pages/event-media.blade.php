<?php


$d = $landingContent;
$hero = (array) ($d['hero'] ?? []);
$contact = (array) ($d['contact'] ?? []);
$zalo_url = \App\Support\Landing\LandingView::zaloUrl($contact);
$placeholder_url = \App\Support\Landing\LandingRegistry::assetUrl($hero['image'] ?? 'assets/images/landing-placeholder.svg');

?>
<div class="tht-landing-event-media-page">
    <section class="tht-landing-media-hero" data-landing-block="hero" aria-labelledby="media-hero-title">
        <div class="tht-landing-media-hero__visual" aria-hidden="true">
            <div class="tht-landing-media-hero__image" style="--media-placeholder: url('<?php echo e($placeholder_url); ?>');"></div>
            <div class="tht-landing-media-hero__video">
                <?php if (! empty($hero['video'])) : ?>
                    <video autoplay muted loop playsinline preload="metadata">
                        <source src="<?php echo e($hero['video']); ?>" type="video/mp4">
                    </video>
                <?php else : ?>
                    <div class="tht-landing-media-video-poster" role="img" aria-label="<?php echo e($hero['image_alt'] ?? 'Tư liệu quay chụp sự kiện của THT Media'); ?>" style="background-image: url('<?php echo e($placeholder_url); ?>');"></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8 tht-landing-media-hero__container">
            <div class="tht-landing-media-hero__content" data-aos="fade-up">
                <p class="tht-landing-media-kicker"><?php echo e($hero['eyebrow'] ?? ''); ?></p>
                <h1 id="media-hero-title"><?php echo \App\Support\Landing\LandingView::safeHtml($hero['title'] ?? 'QUAY PHIM – CHỤP ẢNH SỰ KIỆN'); ?></h1>
                <p class="tht-landing-media-hero__summary"><?php echo e($hero['summary'] ?? ''); ?></p>
                <div class="tht-landing-media-actions">
                    <a class="tht-landing-media-button tht-landing-media-button--primary" href="#media-brief"><?php echo e($hero['primary'] ?? 'GỬI LỊCH SỰ KIỆN'); ?><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    <a class="tht-landing-media-button tht-landing-media-button--text" href="#bo-dau-ra"><?php echo e($hero['secondary'] ?? 'NHẬN BÁO GIÁ'); ?><i class="fa-solid fa-arrow-down" aria-hidden="true"></i></a>
                </div>
                <ul class="tht-landing-media-hero__highlights">
                    <?php foreach (($hero['highlights'] ?? []) as $highlight) : ?>
                        <li><i class="fa-solid fa-circle-check" aria-hidden="true"></i><?php echo e($highlight); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="tht-landing-media-hero__brief" id="media-brief" data-aos="fade-left" data-aos-delay="120">
                <span class="tht-landing-media-hero__brief-label">GỬI LỊCH SỰ KIỆN</span>
                <h2>Kiểm tra ekip còn trống</h2>
                <p><?php echo e($hero['microcopy'] ?? ''); ?></p>
                <form class="tht-landing-media-brief-form" action="#" method="post">
                    <label><span>Ngày tổ chức</span><input type="text" name="event_date" placeholder="Ví dụ: 20/08/2026" required></label>
                    <label><span>Địa điểm</span><input type="text" name="event_location" placeholder="Tỉnh/thành phố, địa điểm"></label>
                    <label><span>Thời lượng</span><input type="text" name="event_duration" placeholder="Ví dụ: 4 tiếng"></label>
                    <label><span>Nhu cầu</span><textarea name="message" rows="2" placeholder="Ảnh / video / flycam / livestream"></textarea></label>
                    <button class="tht-landing-media-button tht-landing-media-button--primary" type="submit">GỬI THÔNG TIN <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
                </form>
            </div>
        </div>
        <div class="tht-landing-media-hero__caption"><span>01 / EVENT MEDIA</span><span>Hình ảnh để sự kiện tiếp tục tạo giá trị</span></div>
    </section>

    <section class="tht-landing-media-section tht-landing-media-problems" aria-labelledby="media-problems-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-media-section__heading tht-landing-media-section__heading--split" data-aos="fade-up">
                <div>
                    <h2 id="media-problems-title" class="tht-landing-media-section__title"><?php echo e($d['problems']['eyebrow'] ?? ''); ?></h2>
                    <p class="tht-landing-media-section__description"><?php echo e($d['problems']['title'] ?? ''); ?></p>
                </div>
                <div class="tht-landing-media-compare"><span><?php echo e($d['problems']['left_title'] ?? ''); ?></span><i class="fa-solid fa-arrow-right" aria-hidden="true"></i><strong><?php echo e($d['problems']['right_title'] ?? ''); ?></strong></div>
            </div>
            <div class="tht-landing-media-problem-grid">
                <?php foreach (($d['problems']['items'] ?? []) as $index => $item) : ?>
                    <article data-aos="fade-up" data-aos-delay="<?php echo e($index * 70); ?>">
                        <span>0<?php echo e($index + 1); ?></span>
                        <h3><?php echo e($item['title'] ?? ''); ?></h3>
                        <p><?php echo e($item['text'] ?? ''); ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
            <p class="tht-landing-media-statement"><i class="fa-solid fa-quote-left" aria-hidden="true"></i><?php echo e($d['problems']['footer'] ?? ''); ?></p>
        </div>
    </section>

    <section class="tht-landing-media-section tht-landing-media-outputs" id="bo-dau-ra" aria-labelledby="media-outputs-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-media-section__heading" data-aos="fade-up">
                <h2 id="media-outputs-title" class="tht-landing-media-section__title"><?php echo e($d['outputs']['eyebrow'] ?? ''); ?></h2>
                <p class="tht-landing-media-section__description"><?php echo e($d['outputs']['title'] ?? ''); ?></p>
            </div>
            <div class="tht-landing-media-output-grid">
                <?php foreach (($d['outputs']['items'] ?? []) as $index => $item) : ?>
                    <article data-aos="fade-up" data-aos-delay="<?php echo e($index * 60); ?>">
                        <div class="tht-landing-media-output-card__mockup">
                            <?php if (! empty($item['image'])) : ?><img src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($item['image'])); ?>" alt="<?php echo e($item['title'] ?? 'Tư liệu đầu ra sự kiện'); ?>" loading="lazy"><?php endif; ?>
                            <span><?php echo e($item['mockup'] ?? 'MEDIA'); ?></span><i class="fa-solid <?php echo e($item['icon'] ?? 'fa-film'); ?>" aria-hidden="true"></i>
                        </div>
                        <span class="tht-landing-media-output-card__number"><?php echo sprintf('%02d', $index + 1); ?></span>
                        <h3><?php echo e($item['title'] ?? ''); ?></h3>
                        <p><?php echo e($item['text'] ?? ''); ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
            <p class="tht-landing-media-note"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><?php echo e($d['outputs']['note'] ?? ''); ?></p>
            <a class="tht-landing-media-inline-cta" href="#media-brief">NHẬN DANH SÁCH ĐẦU RA PHÙ HỢP <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </div>
    </section>

    <section class="tht-landing-media-section tht-landing-media-plans" data-landing-block="pricing" aria-labelledby="media-plans-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-media-section__heading" data-aos="fade-up">
                <h2 id="media-plans-title" class="tht-landing-media-section__title"><?php echo e($d['plans']['eyebrow'] ?? ''); ?></h2>
                <p class="tht-landing-media-section__description"><?php echo e($d['plans']['title'] ?? ''); ?></p>
            </div>
            <div class="tht-landing-media-plan-grid">
                <?php foreach (($d['plans']['items'] ?? []) as $index => $item) : ?>
                    <article class="<?php echo $index === 1 ? 'is-featured' : ''; ?>" data-aos="fade-up" data-aos-delay="<?php echo e($index * 80); ?>">
                        <span class="tht-landing-media-plan-card__number"><?php echo e($item['number'] ?? sprintf('%02d', $index + 1)); ?></span>
                        <h3><?php echo e($item['title'] ?? ''); ?></h3>
                        <?php if (! empty($item['price_label'])) : ?><div class="tht-landing-price"><strong><?php echo e($item['price_label']); ?></strong><?php if (! empty($item['price_note'])) : ?><small><?php echo e($item['price_note']); ?></small><?php endif; ?></div><?php endif; ?>
                        <p class="tht-landing-media-plan-card__fit"><?php echo e($item['fit'] ?? ''); ?></p>
                        <ul><?php foreach (($item['items'] ?? []) as $detail) : ?><li><i class="fa-solid fa-check" aria-hidden="true"></i><?php echo e($detail); ?></li><?php endforeach; ?></ul>
                        <a href="#media-brief"><?php echo e($item['cta'] ?? 'TRAO ĐỔI PHƯƠNG ÁN'); ?><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="tht-landing-media-section tht-landing-media-shotlist" id="shot-list" aria-labelledby="media-shotlist-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-media-section__heading" data-aos="fade-up">
                <h2 id="media-shotlist-title" class="tht-landing-media-section__title"><?php echo e($d['shotlists']['eyebrow'] ?? ''); ?></h2>
                <p class="tht-landing-media-section__description"><?php echo e($d['shotlists']['title'] ?? ''); ?></p>
            </div>
            <div class="tht-landing-media-tabs" data-media-tabs>
                <div class="tht-landing-media-tabs__nav" role="tablist" aria-label="Loại sự kiện">
                    <?php foreach (($d['shotlists']['items'] ?? []) as $index => $item) : ?>
                        <button type="button" role="tab" aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-controls="media-tab-panel-<?php echo e($index); ?>" class="<?php echo $index === 0 ? 'is-active' : ''; ?>"><?php echo e($item['label'] ?? ''); ?></button>
                    <?php endforeach; ?>
                </div>
                <div class="tht-landing-media-tabs__panels">
                    <?php foreach (($d['shotlists']['items'] ?? []) as $index => $item) : ?>
                        <div id="media-tab-panel-<?php echo e($index); ?>" role="tabpanel" class="<?php echo $index === 0 ? 'is-active' : ''; ?>">
                            <span>SHOT-LIST 0<?php echo e($index + 1); ?></span>
                            <h3><?php echo e($item['title'] ?? ''); ?></h3>
                            <ul><?php foreach (($item['items'] ?? []) as $shot) : ?><li><i class="fa-solid fa-check" aria-hidden="true"></i><?php echo e($shot); ?></li><?php endforeach; ?></ul>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <a class="tht-landing-media-inline-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer"><?php echo e($d['shotlists']['cta'] ?? 'NHẬN MẪU SHOT-LIST QUA ZALO'); ?><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
        </div>
    </section>

    <section class="tht-landing-media-section tht-landing-media-preparation" aria-labelledby="media-preparation-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-media-preparation__grid">
                <div class="tht-landing-media-section__heading" data-aos="fade-right">
                    <h2 id="media-preparation-title" class="tht-landing-media-section__title"><?php echo e($d['preparation']['eyebrow'] ?? ''); ?></h2>
                    <p class="tht-landing-media-section__description"><?php echo e($d['preparation']['title'] ?? ''); ?></p>
                </div>
                <ol class="tht-landing-media-preparation__list" data-aos="fade-left">
                    <?php foreach (($d['preparation']['items'] ?? []) as $item) : ?>
                        <li><span><?php echo e($item['number'] ?? ''); ?></span><div><h3><?php echo e($item['title'] ?? ''); ?></h3><p><?php echo e($item['text'] ?? ''); ?></p></div></li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>
    </section>

    <section class="tht-landing-media-section tht-landing-media-layers" aria-labelledby="media-layers-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-media-section__heading" data-aos="fade-up">
                <h2 id="media-layers-title" class="tht-landing-media-section__title"><?php echo e($d['capture_layers']['eyebrow'] ?? ''); ?></h2>
                <p class="tht-landing-media-section__description"><?php echo e($d['capture_layers']['title'] ?? ''); ?></p>
            </div>
            <div class="tht-landing-media-layer-grid">
                <?php foreach (($d['capture_layers']['items'] ?? []) as $index => $item) : ?>
                    <article data-aos="fade-up" data-aos-delay="<?php echo e($index * 70); ?>">
                        <div class="tht-landing-media-layer-card__visual">
                            <?php if (! empty($item['image'])) : ?><img src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($item['image'])); ?>" alt="<?php echo e($item['image_alt'] ?? $item['title'] ?? 'Tư liệu sự kiện THT Media'); ?>" loading="lazy"><?php endif; ?>
                            <div class="tht-landing-media-layer-card__visual-meta"><i class="fa-solid <?php echo e($item['icon'] ?? 'fa-camera'); ?>" aria-hidden="true"></i><span>0<?php echo e($index + 1); ?></span></div>
                        </div>
                        <div class="tht-landing-media-layer-card__body"><h3><?php echo e($item['title'] ?? ''); ?></h3><p><?php echo e($item['text'] ?? ''); ?></p></div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="tht-landing-media-section tht-landing-media-after" aria-labelledby="media-after-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-media-after__grid">
                <div class="tht-landing-media-section__heading" data-aos="fade-right">
                    <h2 id="media-after-title" class="tht-landing-media-section__title"><?php echo e($d['after_value']['eyebrow'] ?? ''); ?></h2>
                    <p class="tht-landing-media-section__description"><?php echo e($d['after_value']['title'] ?? ''); ?></p>
                    <a class="tht-landing-media-inline-cta" href="#media-brief"><?php echo e($d['after_value']['cta'] ?? 'GỬI KÊNH SỬ DỤNG'); ?><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                </div>
                <div class="tht-landing-media-after__flow" data-aos="fade-left">
                    <div class="tht-landing-media-after__node"><span>01</span><strong>SỰ KIỆN</strong></div><i class="fa-solid fa-arrow-right" aria-hidden="true"></i><div class="tht-landing-media-after__node"><span>02</span><strong>TÀI NGUYÊN</strong></div><i class="fa-solid fa-arrow-right" aria-hidden="true"></i><div class="tht-landing-media-after__channels"><span>03</span><ul><?php foreach (($d['after_value']['items'] ?? []) as $item) : ?><li><?php echo e($item); ?></li><?php endforeach; ?></ul></div>
                </div>
            </div>
        </div>
    </section>

    <section class="tht-landing-media-section tht-landing-media-projects" id="du-an" aria-labelledby="media-projects-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <span class="sr-only">HÌNH ẢNH HẬU TRƯỜNG</span>
            <div class="tht-landing-media-section__heading" data-aos="fade-up">
                <h2 id="media-projects-title" class="tht-landing-media-section__title"><?php echo e($d['projects']['eyebrow'] ?? ''); ?></h2>
                <p class="tht-landing-media-section__description"><?php echo e($d['projects']['title'] ?? ''); ?></p>
            </div>
            <div class="tht-landing-media-project-grid">
                <?php foreach (($d['projects']['items'] ?? []) as $index => $project) : ?>
                    <article data-aos="fade-up" data-aos-delay="<?php echo e($index * 80); ?>">
                        <div class="tht-landing-media-project-card__visual">
                            <?php if (! empty($project['images'][0])) : ?>
                                <img src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($project['images'][0])); ?>" alt="<?php echo e($project['title'] ?? 'Tư liệu sự kiện'); ?>" loading="lazy">
                            <?php else : ?>
                                <i class="fa-solid fa-film" aria-hidden="true"></i>
                            <?php endif; ?>
                            <span>TƯ LIỆU THAM KHẢO</span><b><?php echo sprintf('%02d', $index + 1); ?></b>
                        </div>
                        <div class="tht-landing-media-project-card__body"><span><?php echo e($project['type'] ?? 'Sự kiện'); ?></span><h3><?php echo e($project['title'] ?? ''); ?></h3><p><?php echo e($project['scope'] ?? ''); ?></p><a href="#media-brief">TRAO ĐỔI ĐẦU RA <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></div>
                    </article>
                <?php endforeach; ?>
            </div>
            <p class="tht-landing-media-note"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><?php echo e($d['projects']['note'] ?? ''); ?></p>
        </div>
    </section>

    <section class="tht-landing-media-section tht-landing-media-reasons" aria-labelledby="media-reasons-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-media-section__heading" data-aos="fade-up"><h2 id="media-reasons-title" class="tht-landing-media-section__title"><?php echo e($d['reasons']['eyebrow'] ?? ''); ?></h2><p class="tht-landing-media-section__description"><?php echo e($d['reasons']['title'] ?? ''); ?></p></div>
            <div class="tht-landing-media-reason-grid"><?php foreach (($d['reasons']['items'] ?? []) as $index => $item) : ?><article data-aos="fade-up" data-aos-delay="<?php echo e($index * 70); ?>"><span>0<?php echo e($index + 1); ?></span><i class="fa-solid <?php echo e($item['icon'] ?? 'fa-check'); ?>" aria-hidden="true"></i><h3><?php echo e($item['title'] ?? ''); ?></h3><p><?php echo e($item['text'] ?? ''); ?></p></article><?php endforeach; ?></div>
        </div>
    </section>

    <section class="tht-landing-media-section tht-landing-media-faq" id="faq" aria-labelledby="media-faq-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-media-faq__grid">
                <div class="tht-landing-media-section__heading" data-aos="fade-right"><h2 id="media-faq-title" class="tht-landing-media-section__title"><?php echo e($d['faq']['eyebrow'] ?? ''); ?></h2><p class="tht-landing-media-section__description"><?php echo e($d['faq']['title'] ?? ''); ?></p><p>Gửi lịch dự kiến để THT tư vấn cấu hình ekip trước khi chốt.</p></div>
                <div class="tht-landing-media-accordion" data-media-accordion data-aos="fade-left"><?php foreach (($d['faq']['items'] ?? []) as $index => $item) : ?><div class="tht-landing-media-accordion__item<?php echo $index === 0 ? ' is-open' : ''; ?>"><h3><button type="button" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>"><span><?php echo e($item['question'] ?? ''); ?></span><i class="fa-solid fa-plus" aria-hidden="true"></i></button></h3><div class="tht-landing-media-accordion__panel"><p><?php echo e($item['answer'] ?? ''); ?></p></div></div><?php endforeach; ?></div>
            </div>
        </div>
    </section>

    <section class="tht-landing-media-final" id="lien-he" aria-labelledby="media-final-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8 tht-landing-media-final__inner" data-aos="fade-up">
            <div><p class="tht-landing-media-kicker">SẴN SÀNG CHUẨN BỊ?</p><h2 id="media-final-title"><?php echo e($d['contact_section']['title'] ?? ''); ?></h2><p><?php echo e($d['contact_section']['body'] ?? ''); ?></p></div>
            <div class="tht-landing-media-final__actions"><a class="tht-landing-media-button tht-landing-media-button--light tht-landing-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer"><span class="tht-landing-zalo-icon" aria-hidden="true"></span> NHẮN ZALO</a><button class="tht-landing-media-button tht-landing-media-button--outline" type="button" data-landing-modal-open="#tht-landing-contact-modal"><?php echo e($d['contact_section']['cta'] ?? 'KIỂM TRA LỊCH EKIP'); ?> <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button></div>
            <p class="tht-landing-media-final__message">Gợi ý tin nhắn: “Tôi cần quay/chụp sự kiện. Ngày tổ chức: … Địa điểm: … Thời lượng: … Nhu cầu: ảnh / video / flycam / livestream.”</p>
        </div>
    </section>
</div>
