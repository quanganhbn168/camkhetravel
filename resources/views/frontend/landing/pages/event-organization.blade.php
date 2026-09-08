<?php


$hero = (array) ($landingContent['hero'] ?? []);
$positioning = (array) ($landingContent['positioning'] ?? []);
$event_types = (array) ($landingContent['event_types'] ?? []);
$services = (array) ($landingContent['services'] ?? []);
$plans = (array) ($landingContent['plans'] ?? []);
$process = (array) ($landingContent['process'] ?? []);
$budget = (array) ($landingContent['budget'] ?? []);
$projects = (array) ($landingContent['projects'] ?? []);
$reasons = (array) ($landingContent['reasons'] ?? []);
$faq = (array) ($landingContent['faq'] ?? []);
$contact = (array) ($landingContent['contact'] ?? []);
$zalo_url = \App\Support\Landing\LandingView::zaloUrl($contact);
$hero_image = ! empty($hero['image']) ? \App\Support\Landing\LandingRegistry::assetUrl($hero['image']) : '';

?>
<div class="tht-landing-event-page">
    <section class="tht-landing-event-hero" data-landing-block="hero" aria-labelledby="event-hero-title">
        <div class="tht-landing-event-hero__glow" aria-hidden="true"></div>
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8 tht-landing-event-hero__container">
            <div class="tht-landing-event-hero__grid">
                <div class="tht-landing-event-hero__copy" data-aos="fade-up">
                    <p class="tht-landing-event-kicker"><?php echo e($hero['eyebrow'] ?? 'TỔ CHỨC SỰ KIỆN TRỌN GÓI'); ?></p>
                    <h1 id="event-hero-title"><?php echo \App\Support\Landing\LandingView::safeHtml($hero['title'] ?? 'TỔ CHỨC SỰ KIỆN TRỌN GÓI'); ?></h1>
                    <p class="tht-landing-event-hero__summary"><?php echo e($hero['summary'] ?? ''); ?></p>
                    <div class="tht-landing-event-hero__actions">
                        <a class="tht-landing-event-button tht-landing-event-button--primary" href="#brief"><?php echo e($hero['primary'] ?? 'GỬI BRIEF NHANH'); ?><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                        <a class="tht-landing-event-button tht-landing-event-button--text" href="#giai-phap"><?php echo e($hero['secondary'] ?? 'XEM GIẢI PHÁP'); ?><i class="fa-solid fa-arrow-down" aria-hidden="true"></i></a>
                    </div>
                    <?php if (! empty($hero['highlights'])) : ?>
                        <ul class="tht-landing-event-hero__highlights">
                            <?php foreach ($hero['highlights'] as $highlight) : ?>
                                <li><i class="fa-solid fa-circle-check" aria-hidden="true"></i><?php echo e($highlight); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <div class="tht-landing-event-brief-card" id="brief" data-aos="fade-left" data-aos-delay="120">
                    <div class="tht-landing-event-brief-card__top">
                        <span class="tht-landing-event-brief-card__index">01 / 04</span>
                        <span class="tht-landing-event-brief-card__mark"><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></span>
                    </div>
                    <h2><?php echo e($hero['brief_title'] ?? 'GỬI BRIEF NHANH'); ?></h2>
                    <p><?php echo e($hero['brief_note'] ?? ''); ?></p>
                    <form class="tht-landing-event-brief-form" action="<?php echo e($landingContent['form_action'] ?: '#'); ?>" method="post">
                        <label>
                            <span>Họ và tên</span>
                            <input type="text" name="fullname" placeholder="Anh/chị là..." required>
                        </label>
                        <label>
                            <span>Số điện thoại</span>
                            <input type="tel" name="phone" placeholder="Số điện thoại liên hệ" required>
                        </label>
                        <label>
                            <span>Loại sự kiện</span>
                            <select name="event_type">
                                <option value="">Anh/chị đang chuẩn bị...</option>
                                <?php foreach (($event_types['items'] ?? []) as $event_type) : ?>
                                    <option value="<?php echo e($event_type['title']); ?>"><?php echo e($event_type['title']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label>
                            <span>Thông tin sơ bộ</span>
                            <textarea name="message" rows="3" placeholder="Thời gian, địa điểm, quy mô dự kiến..."></textarea>
                        </label>
                        <button class="tht-landing-event-button tht-landing-event-button--primary tht-landing-event-brief-form__submit" type="submit">GỬI THÔNG TIN <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
                    </form>
                    <p class="tht-landing-event-brief-card__privacy"><i class="fa-solid fa-lock" aria-hidden="true"></i> Thông tin chỉ dùng để tư vấn phương án.</p>
                </div>
            </div>

            <div class="tht-landing-event-hero__media" data-aos="fade-up" data-aos-delay="220">
                <?php if (! empty($hero['video'])) : ?>
                    <video class="tht-landing-event-hero__video" controls muted playsinline preload="metadata" poster="<?php echo e($hero['poster'] ?? ''); ?>">
                        <source src="<?php echo e($hero['video']); ?>" type="video/mp4">
                    </video>
                <?php else : ?>
                    <div class="tht-landing-event-media-image" role="img" aria-label="<?php echo e($hero['image_alt'] ?? 'Đội ngũ THT Media tại hiện trường'); ?>" style="background-image: url('<?php echo e($hero_image); ?>');"></div>
                <?php endif; ?>
                <div class="tht-landing-event-hero__media-caption"><span>01</span><span>Từ ý tưởng đến hiện trường</span><span>Scroll để khám phá <i class="fa-solid fa-arrow-down" aria-hidden="true"></i></span></div>
            </div>
        </div>
    </section>

    <section class="tht-landing-event-section tht-landing-event-problems" aria-labelledby="event-problems-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-event-section__heading tht-landing-event-section__heading--split" data-aos="fade-up">
                <div>
                    <h2 id="event-problems-title" class="tht-landing-event-section__title"><?php echo e($positioning['eyebrow'] ?? ''); ?></h2>
                    <p class="tht-landing-event-section__description"><?php echo e($positioning['title'] ?? ''); ?></p>
                </div>
                <p><?php echo e($positioning['body'] ?? ''); ?></p>
            </div>
            <div class="tht-landing-event-problem-grid">
                <?php foreach (($positioning['items'] ?? []) as $index => $item) : ?>
                    <article class="tht-landing-event-problem-card" data-aos="fade-up" data-aos-delay="<?php echo e($index * 70); ?>">
                        <span class="tht-landing-event-card-number">0<?php echo e($index + 1); ?></span>
                        <i class="fa-solid <?php echo e($item['icon'] ?? 'fa-circle'); ?>" aria-hidden="true"></i>
                        <h3><?php echo e($item['title'] ?? ''); ?></h3>
                        <p><?php echo e($item['text'] ?? ''); ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
            <div class="tht-landing-event-statement" data-aos="fade-up">
                <i class="fa-solid fa-quote-left" aria-hidden="true"></i>
                <p><?php echo e($positioning['footer'] ?? ''); ?></p>
            </div>
        </div>
    </section>

    <section class="tht-landing-event-section tht-landing-event-types" id="giai-phap" aria-labelledby="event-types-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-event-section__heading" data-aos="fade-up">
                <h2 id="event-types-title" class="tht-landing-event-section__title"><?php echo e($event_types['eyebrow'] ?? ''); ?></h2>
                <p class="tht-landing-event-section__description"><?php echo e($event_types['title'] ?? ''); ?></p>
                <p><?php echo e($event_types['body'] ?? ''); ?></p>
            </div>
            <div class="tht-landing-event-type-grid">
                <?php foreach (($event_types['items'] ?? []) as $index => $item) : ?>
                    <article class="tht-landing-event-type-card" data-aos="fade-up" data-aos-delay="<?php echo e($index * 60); ?>">
                        <span class="tht-landing-event-type-card__number"><?php echo e($item['number'] ?? sprintf('%02d', $index + 1)); ?></span>
                        <div>
                            <h3><?php echo e($item['title'] ?? ''); ?></h3>
                            <p><?php echo e($item['text'] ?? ''); ?></p>
                        </div>
                        <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
                    </article>
                <?php endforeach; ?>
            </div>
            <a class="tht-landing-event-inline-cta" href="#brief"><?php echo e($event_types['cta'] ?? 'TRAO ĐỔI VỀ SỰ KIỆN'); ?><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </div>
    </section>

    <section class="tht-landing-event-section tht-landing-event-services" aria-labelledby="event-services-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-event-services__grid">
                <div class="tht-landing-event-services__visual" data-aos="fade-right">
                    <div class="tht-landing-event-services__image" style="--event-placeholder: url('<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($services['image'] ?? $placeholder)); ?>');" role="img" aria-label="<?php echo e($services['image_alt'] ?? ''); ?>">
                        <span class="tht-landing-event-services__visual-label">EVENT<br>PRODUCTION</span>
                            <span class="tht-landing-event-media-badge"><i class="fa-solid fa-image" aria-hidden="true"></i> Tư liệu hiện trường THT</span>
                    </div>
                    <div class="tht-landing-event-services__visual-note">Từ một ý tưởng rõ ràng đến một trải nghiệm có thể vận hành.</div>
                </div>
                <div class="tht-landing-event-services__content" data-aos="fade-left">
                    <h2 id="event-services-title" class="tht-landing-event-section__title"><?php echo e($services['eyebrow'] ?? ''); ?></h2>
                    <p class="tht-landing-event-section__description"><?php echo e($services['title'] ?? ''); ?></p>
                    <p class="tht-landing-event-section__lead"><?php echo e($services['body'] ?? ''); ?></p>
                    <div class="tht-landing-event-accordion" data-event-accordion>
                        <?php foreach (($services['items'] ?? []) as $index => $item) : ?>
                            <div class="tht-landing-event-accordion__item<?php echo $index === 0 ? ' is-open' : ''; ?>">
                                <h3>
                                    <button type="button" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>">
                                        <span><b><?php echo sprintf('%02d', $index + 1); ?></b><?php echo e($item['title'] ?? ''); ?></span>
                                        <i class="fa-solid fa-plus" aria-hidden="true"></i>
                                    </button>
                                </h3>
                                <div class="tht-landing-event-accordion__panel">
                                    <p><?php echo e($item['text'] ?? ''); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="tht-landing-event-section tht-landing-event-plans" data-landing-block="pricing" aria-labelledby="event-plans-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-event-section__heading" data-aos="fade-up">
                <h2 id="event-plans-title" class="tht-landing-event-section__title"><?php echo e($plans['eyebrow'] ?? ''); ?></h2>
                <p class="tht-landing-event-section__description"><?php echo e($plans['title'] ?? ''); ?></p>
            </div>
            <div class="tht-landing-event-plan-grid">
                <?php foreach (($plans['items'] ?? []) as $index => $item) : ?>
                    <article class="tht-landing-event-plan-card<?php echo $index === 1 ? ' tht-landing-event-plan-card--featured' : ''; ?>" data-aos="fade-up" data-aos-delay="<?php echo e($index * 80); ?>">
                        <span class="tht-landing-event-plan-card__label"><?php echo e($item['label'] ?? sprintf('%02d', $index + 1)); ?></span>
                        <h3><?php echo e($item['title'] ?? ''); ?></h3>
                        <?php if (! empty($item['price_label'])) : ?><div class="tht-landing-price"><strong><?php echo e($item['price_label']); ?></strong><?php if (! empty($item['price_note'])) : ?><small><?php echo e($item['price_note']); ?></small><?php endif; ?></div><?php endif; ?>
                        <p><?php echo e($item['text'] ?? ''); ?></p>
                        <a href="#brief" aria-label="Trao đổi về <?php echo e($item['title'] ?? 'phương án'); ?>"><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="tht-landing-event-section tht-landing-event-process" id="quy-trinh" aria-labelledby="event-process-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-event-section__heading tht-landing-event-section__heading--split" data-aos="fade-up">
                <div>
                    <h2 id="event-process-title" class="tht-landing-event-section__title"><?php echo e($process['eyebrow'] ?? ''); ?></h2>
                    <p class="tht-landing-event-section__description"><?php echo e($process['title'] ?? ''); ?></p>
                </div>
            </div>
            <div class="tht-landing-event-process-grid">
                <?php foreach (($process['items'] ?? []) as $index => $item) : ?>
                    <article class="tht-landing-event-process-card" data-aos="fade-up" data-aos-delay="<?php echo e($index * 60); ?>">
                        <span class="tht-landing-event-process-card__step"><?php echo e($item['step'] ?? sprintf('%02d', $index + 1)); ?></span>
                        <h3><?php echo e($item['title'] ?? ''); ?></h3>
                        <p><?php echo e($item['text'] ?? ''); ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="tht-landing-event-section tht-landing-event-budget" aria-labelledby="event-budget-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-event-section__heading" data-aos="fade-up">
                <h2 id="event-budget-title" class="tht-landing-event-section__title"><?php echo e($budget['eyebrow'] ?? ''); ?></h2>
                <p class="tht-landing-event-section__description"><?php echo e($budget['title'] ?? ''); ?></p>
                <p><?php echo e($budget['body'] ?? ''); ?></p>
            </div>
            <div class="tht-landing-event-budget-grid">
                <?php foreach (($budget['groups'] ?? []) as $index => $group) : ?>
                    <article class="tht-landing-event-budget-card tht-landing-event-budget-card--<?php echo e($group['tone'] ?? 'required'); ?>" data-aos="fade-up" data-aos-delay="<?php echo e($index * 80); ?>">
                        <span class="tht-landing-event-budget-card__index">0<?php echo e($index + 1); ?></span>
                        <h3><?php echo e($group['title'] ?? ''); ?></h3>
                        <ul>
                            <?php foreach (($group['items'] ?? []) as $item) : ?>
                                <li><i class="fa-solid fa-check" aria-hidden="true"></i><?php echo e($item); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="tht-landing-event-section tht-landing-event-projects" id="du-an" aria-labelledby="event-projects-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-event-section__heading tht-landing-event-section__heading--split" data-aos="fade-up">
                <div>
                    <h2 id="event-projects-title" class="tht-landing-event-section__title"><?php echo e($projects['eyebrow'] ?? ''); ?></h2>
                    <p class="tht-landing-event-section__description"><?php echo e($projects['title'] ?? ''); ?></p>
                </div>
                <p><?php echo e($projects['body'] ?? ''); ?></p>
            </div>
            <div class="tht-landing-event-project-grid">
                <?php foreach (($projects['items'] ?? []) as $index => $project) : ?>
                    <article class="tht-landing-event-project-card" data-aos="fade-up" data-aos-delay="<?php echo e($index * 60); ?>">
                        <div class="tht-landing-event-project-card__visual">
                            <?php if (! empty($project['images'])) : ?>
                                <img src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($project['images'][0])); ?>" alt="<?php echo e($project['title'] ?? ''); ?>" loading="lazy">
                            <?php else : ?>
                                <div class="tht-landing-event-project-card__pending" style="--event-placeholder: url('<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($placeholder)); ?>');">
                                    <i class="fa-solid fa-images" aria-hidden="true"></i>
                                    <span>ẢNH TƯ LIỆU ĐANG CHỌN</span>
                                </div>
                            <?php endif; ?>
                            <span class="tht-landing-event-project-card__number"><?php echo sprintf('%02d', $index + 1); ?></span>
                        </div>
                        <div class="tht-landing-event-project-card__body">
                            <span><?php echo e($project['type'] ?? 'Dự án sự kiện'); ?></span>
                            <h3><?php echo e($project['title'] ?? ''); ?></h3>
                            <p><?php echo e($projects['pending_note'] ?? ''); ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="tht-landing-event-section tht-landing-event-reasons" aria-labelledby="event-reasons-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-event-section__heading" data-aos="fade-up">
                <h2 id="event-reasons-title" class="tht-landing-event-section__title"><?php echo e($reasons['eyebrow'] ?? ''); ?></h2>
                <p class="tht-landing-event-section__description"><?php echo e($reasons['title'] ?? ''); ?></p>
            </div>
            <div class="tht-landing-event-reason-grid">
                <?php foreach (($reasons['items'] ?? []) as $index => $item) : ?>
                    <article class="tht-landing-event-reason-card" data-aos="fade-up" data-aos-delay="<?php echo e($index * 70); ?>">
                        <span class="tht-landing-event-reason-card__number"><?php echo sprintf('%02d', $index + 1); ?></span>
                        <i class="fa-solid <?php echo e($item['icon'] ?? 'fa-circle'); ?>" aria-hidden="true"></i>
                        <h3><?php echo e($item['title'] ?? ''); ?></h3>
                        <p><?php echo e($item['text'] ?? ''); ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="tht-landing-event-section tht-landing-event-faq" id="faq" aria-labelledby="event-faq-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-event-faq__grid">
                <div class="tht-landing-event-section__heading" data-aos="fade-right">
                    <h2 id="event-faq-title" class="tht-landing-event-section__title"><?php echo e($faq['eyebrow'] ?? ''); ?></h2>
                    <p class="tht-landing-event-section__description"><?php echo e($faq['title'] ?? ''); ?></p>
                    <p>Chưa thấy câu trả lời phù hợp? Gửi brief để THT Media trao đổi trực tiếp về chương trình của anh/chị.</p>
                    <a class="tht-landing-event-inline-cta" href="#brief">GỬI BRIEF NHANH <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                </div>
                <div class="tht-landing-event-accordion tht-landing-event-accordion--faq" data-event-accordion data-aos="fade-left">
                    <?php foreach (($faq['items'] ?? []) as $index => $item) : ?>
                        <div class="tht-landing-event-accordion__item<?php echo $index === 0 ? ' is-open' : ''; ?>">
                            <h3>
                                <button type="button" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>">
                                    <span><?php echo e($item['question'] ?? ''); ?></span>
                                    <i class="fa-solid fa-plus" aria-hidden="true"></i>
                                </button>
                            </h3>
                            <div class="tht-landing-event-accordion__panel">
                                <p><?php echo e($item['answer'] ?? ''); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="tht-landing-event-final-cta" id="lien-he" aria-labelledby="event-final-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-event-final-cta__inner" data-aos="fade-up">
                <div>
                    <p class="tht-landing-event-kicker">SẴN SÀNG BẮT ĐẦU?</p>
                    <h2 id="event-final-title"><?php echo e($landingContent['contact_section']['title'] ?? ''); ?></h2>
                    <p><?php echo e($landingContent['contact_section']['body'] ?? ''); ?></p>
                </div>
                <div class="tht-landing-event-final-cta__actions">
                    <a class="tht-landing-event-button tht-landing-event-button--light tht-landing-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer"><span class="tht-landing-zalo-icon" aria-hidden="true"></span> NHẮN ZALO</a>
                    <button class="tht-landing-event-button tht-landing-event-button--outline-light" type="button" data-landing-modal-open="#tht-landing-contact-modal"><?php echo e($landingContent['contact_section']['cta'] ?? 'GỬI YÊU CẦU'); ?> <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
                </div>
                <p class="tht-landing-event-final-cta__message"><i class="fa-solid fa-comment-dots" aria-hidden="true"></i> Gợi ý tin nhắn Zalo: “Tôi muốn tổ chức sự kiện [loại sự kiện] vào [thời gian], quy mô khoảng [số khách]. Nhờ THT Media tư vấn phương án.”</p>
            </div>
        </div>
    </section>
</div>
