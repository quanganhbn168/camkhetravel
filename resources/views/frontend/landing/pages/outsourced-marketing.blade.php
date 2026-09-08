<?php


$d = $landingContent;
$hero = (array) ($d['hero'] ?? []);
$contact = (array) ($d['contact'] ?? []);
$zalo_url = \App\Support\Landing\LandingView::zaloUrl($contact);
$hero_image = \App\Support\Landing\LandingRegistry::assetUrl($hero['image'] ?? 'assets/images/landing-placeholder.svg');

?>
<div class="tht-landing-marketing-page">
    <section class="tht-landing-marketing-hero" data-landing-block="hero" aria-labelledby="marketing-hero-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8 tht-landing-marketing-hero__grid">
            <div data-aos="fade-up">
                <p class="tht-landing-marketing-kicker"><?php echo e($hero['eyebrow'] ?? ''); ?></p>
                <h1 id="marketing-hero-title"><?php echo e($hero['title'] ?? ''); ?></h1>
                <p class="tht-landing-marketing-hero__summary"><?php echo e($hero['summary'] ?? ''); ?></p>
                <div class="tht-landing-marketing-actions"><a class="tht-landing-marketing-button tht-landing-marketing-button--primary" href="#audit"><?php echo e($hero['primary'] ?? 'GỬI FANPAGE – NHẬN AUDIT'); ?><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a><a class="tht-landing-marketing-button tht-landing-marketing-button--text" href="#30-ngay"><?php echo e($hero['secondary'] ?? 'NHẬN KHUNG 30 NGÀY'); ?><i class="fa-solid fa-arrow-down" aria-hidden="true"></i></a></div>
                <ul class="tht-landing-marketing-hero__highlights"><?php foreach (($hero['highlights'] ?? []) as $highlight) : ?><li><i class="fa-solid fa-circle-check" aria-hidden="true"></i><?php echo e($highlight); ?></li><?php endforeach; ?></ul>
                <p class="tht-landing-marketing-hero__microcopy"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><?php echo e($hero['microcopy'] ?? ''); ?></p>
            </div>
            <figure class="tht-landing-marketing-hero__image" data-aos="fade-left" data-aos-delay="100">
                <img src="<?php echo e($hero_image); ?>" alt="Đội ngũ THT Media triển khai marketing cho doanh nghiệp" width="1200" height="900" loading="eager" decoding="async">
                <figcaption>Chiến lược · nội dung · media · quảng cáo · đo lường</figcaption>
            </figure>
        </div>
    </section>

    <section class="tht-landing-marketing-section tht-landing-marketing-problems" aria-labelledby="marketing-problems-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-marketing-section__heading" data-aos="fade-up"><h2 id="marketing-problems-title" class="tht-landing-marketing-section__title"><?php echo e($d['problems']['eyebrow'] ?? ''); ?></h2><p class="tht-landing-marketing-section__description"><?php echo e($d['problems']['title'] ?? ''); ?></p></div>
            <div class="tht-landing-marketing-problem-grid"><?php foreach (($d['problems']['items'] ?? []) as $index => $item) : ?><article data-aos="fade-up" data-aos-delay="<?php echo e($index * 60); ?>"><span>0<?php echo e($index + 1); ?></span><h3><?php echo e($item['title'] ?? ''); ?></h3><p><?php echo e($item['text'] ?? ''); ?></p></article><?php endforeach; ?></div>
            <p class="tht-landing-marketing-statement"><i class="fa-solid fa-quote-left" aria-hidden="true"></i><?php echo e($d['problems']['footer'] ?? ''); ?></p>
        </div>
    </section>

    <section class="tht-landing-marketing-section tht-landing-marketing-fit" id="phu-hop" aria-labelledby="marketing-fit-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-marketing-section__heading" data-aos="fade-up"><h2 id="marketing-fit-title" class="tht-landing-marketing-section__title"><?php echo e($d['fit']['eyebrow'] ?? ''); ?></h2><p class="tht-landing-marketing-section__description"><?php echo e($d['fit']['title'] ?? ''); ?></p></div>
            <div class="tht-landing-marketing-fit-grid"><article><span class="tht-landing-marketing-fit-label"><i class="fa-solid fa-check" aria-hidden="true"></i> PHÙ HỢP KHI</span><ul><?php foreach (($d['fit']['yes'] ?? []) as $item) : ?><li><?php echo e($item); ?></li><?php endforeach; ?></ul></article><article><span class="tht-landing-marketing-fit-label tht-landing-marketing-fit-label--no"><i class="fa-solid fa-minus" aria-hidden="true"></i> CHƯA PHÙ HỢP KHI</span><ul><?php foreach (($d['fit']['no'] ?? []) as $item) : ?><li><?php echo e($item); ?></li><?php endforeach; ?></ul></article></div>
            <p class="tht-landing-marketing-note"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><?php echo e($d['fit']['footer'] ?? ''); ?></p>
        </div>
    </section>

    <section class="tht-landing-marketing-section tht-landing-marketing-team" id="doi-ngu" aria-labelledby="marketing-team-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-marketing-section__heading" data-aos="fade-up"><h2 id="marketing-team-title" class="tht-landing-marketing-section__title"><?php echo e($d['team']['eyebrow'] ?? ''); ?></h2><p class="tht-landing-marketing-section__description"><?php echo e($d['team']['title'] ?? ''); ?></p></div>
            <div class="tht-landing-marketing-team-grid"><?php foreach (($d['team']['items'] ?? []) as $index => $item) : ?><article data-aos="fade-up" data-aos-delay="<?php echo e(($index % 4) * 60); ?>"><div class="tht-landing-marketing-team-card__top"><i class="fa-solid <?php echo e($item['icon'] ?? 'fa-user'); ?>" aria-hidden="true"></i><span>0<?php echo e($index + 1); ?></span></div><h3><?php echo e($item['role'] ?? ''); ?></h3><p><?php echo e($item['text'] ?? ''); ?></p></article><?php endforeach; ?></div>
            <p class="tht-landing-marketing-note"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><?php echo e($d['team']['note'] ?? ''); ?></p>
        </div>
    </section>

    <section class="tht-landing-marketing-section tht-landing-marketing-modules" id="pham-vi" aria-labelledby="marketing-modules-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-marketing-modules__grid"><div class="tht-landing-marketing-section__heading" data-aos="fade-right"><h2 id="marketing-modules-title" class="tht-landing-marketing-section__title"><?php echo e($d['modules']['eyebrow'] ?? ''); ?></h2><p class="tht-landing-marketing-section__description"><?php echo e($d['modules']['title'] ?? ''); ?></p></div><div class="tht-landing-marketing-accordion" data-marketing-accordion data-aos="fade-left"><?php foreach (($d['modules']['items'] ?? []) as $index => $item) : ?><div class="tht-landing-marketing-accordion__item<?php echo $index === 0 ? ' is-open' : ''; ?>"><h3><button type="button" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>"><span><b>0<?php echo e($index + 1); ?></b><i class="fa-solid <?php echo e($item['icon'] ?? 'fa-check'); ?>" aria-hidden="true"></i><?php echo e($item['title'] ?? ''); ?></span><i class="fa-solid fa-plus" aria-hidden="true"></i></button></h3><div class="tht-landing-marketing-accordion__panel"><p><?php echo e($item['text'] ?? ''); ?></p></div></div><?php endforeach; ?></div></div>
        </div>
    </section>

    <section class="tht-landing-marketing-section tht-landing-marketing-30" id="30-ngay" aria-labelledby="marketing-30-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-marketing-section__heading" data-aos="fade-up"><h2 id="marketing-30-title" class="tht-landing-marketing-section__title"><?php echo e($d['first_30_days']['eyebrow'] ?? ''); ?></h2><p class="tht-landing-marketing-section__description"><?php echo e($d['first_30_days']['title'] ?? ''); ?></p></div>
            <div class="tht-landing-marketing-30-grid"><?php foreach (($d['first_30_days']['items'] ?? []) as $index => $item) : ?><article data-aos="fade-up" data-aos-delay="<?php echo e($index * 70); ?>"><span><?php echo e($item['week'] ?? ''); ?></span><h3><?php echo e($item['title'] ?? ''); ?></h3><p><?php echo e($item['text'] ?? ''); ?></p></article><?php endforeach; ?></div>
            <a class="tht-landing-marketing-inline-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer"><?php echo e($d['first_30_days']['cta'] ?? 'NHẬN KHUNG 30 NGÀY'); ?><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
        </div>
    </section>

    <section class="tht-landing-marketing-section tht-landing-marketing-plans" data-landing-block="pricing" aria-labelledby="marketing-plans-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-marketing-section__heading" data-aos="fade-up"><h2 id="marketing-plans-title" class="tht-landing-marketing-section__title"><?php echo e($d['plans']['eyebrow'] ?? ''); ?></h2><p class="tht-landing-marketing-section__description"><?php echo e($d['plans']['title'] ?? ''); ?></p></div>
            <div class="tht-landing-marketing-plan-grid"><?php foreach (($d['plans']['items'] ?? []) as $index => $item) : ?><article class="<?php echo $index === 1 ? 'is-featured' : ''; ?>" data-aos="fade-up" data-aos-delay="<?php echo e($index * 80); ?>"><span class="tht-landing-marketing-plan-number"><?php echo e($item['number'] ?? sprintf('%02d', $index + 1)); ?></span><h3><?php echo e($item['title'] ?? ''); ?></h3><?php if (! empty($item['price_label'])) : ?><div class="tht-landing-price"><strong><?php echo e($item['price_label']); ?></strong><?php if (! empty($item['price_note'])) : ?><small><?php echo e($item['price_note']); ?></small><?php endif; ?></div><?php endif; ?><p><?php echo e($item['fit'] ?? ''); ?></p><ul><?php foreach (($item['items'] ?? []) as $detail) : ?><li><i class="fa-solid fa-check" aria-hidden="true"></i><?php echo e($detail); ?></li><?php endforeach; ?></ul><a href="#audit"><?php echo e($item['cta'] ?? 'TRAO ĐỔI PHƯƠNG ÁN'); ?><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></article><?php endforeach; ?></div>
        </div>
    </section>

    <section class="tht-landing-marketing-section tht-landing-marketing-kpi" id="kpi" aria-labelledby="marketing-kpi-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-marketing-section__heading" data-aos="fade-up"><h2 id="marketing-kpi-title" class="tht-landing-marketing-section__title"><?php echo e($d['kpi']['eyebrow'] ?? ''); ?></h2><p class="tht-landing-marketing-section__description"><?php echo e($d['kpi']['title'] ?? ''); ?></p></div>
            <div class="tht-landing-marketing-kpi-grid"><?php foreach (($d['kpi']['items'] ?? []) as $index => $item) : ?><article data-aos="fade-up" data-aos-delay="<?php echo e($index * 70); ?>"><div class="tht-landing-marketing-kpi-visual"><span><?php echo e($item['level'] ?? ''); ?></span><i class="fa-solid <?php echo e($item['icon'] ?? 'fa-chart-line'); ?>" aria-hidden="true"></i></div><h3><?php echo e($item['title'] ?? ''); ?></h3><p><?php echo e($item['text'] ?? ''); ?></p></article><?php endforeach; ?></div>
            <p class="tht-landing-marketing-note"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><?php echo e($d['kpi']['footer'] ?? ''); ?></p>
        </div>
    </section>

    <section class="tht-landing-marketing-section tht-landing-marketing-coordination" aria-labelledby="marketing-coordination-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-marketing-section__heading" data-aos="fade-up"><h2 id="marketing-coordination-title" class="tht-landing-marketing-section__title"><?php echo e($d['coordination']['eyebrow'] ?? ''); ?></h2><p class="tht-landing-marketing-section__description"><?php echo e($d['coordination']['title'] ?? ''); ?></p></div>
            <div class="tht-landing-marketing-coordination-grid"><?php foreach (($d['coordination']['items'] ?? []) as $index => $item) : ?><article data-aos="fade-up" data-aos-delay="<?php echo e(($index % 3) * 60); ?>"><i class="fa-solid <?php echo e($item['icon'] ?? 'fa-check'); ?>" aria-hidden="true"></i><span>0<?php echo e($index + 1); ?></span><h3><?php echo e($item['title'] ?? ''); ?></h3><p><?php echo e($item['text'] ?? ''); ?></p></article><?php endforeach; ?></div>
        </div>
    </section>

    <section class="tht-landing-marketing-section tht-landing-marketing-cases" id="du-an" aria-labelledby="marketing-cases-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-marketing-section__heading" data-aos="fade-up"><h2 id="marketing-cases-title" class="tht-landing-marketing-section__title"><?php echo e($d['cases']['eyebrow'] ?? ''); ?></h2><p class="tht-landing-marketing-section__description"><?php echo e($d['cases']['title'] ?? ''); ?></p></div>
            <div class="tht-landing-marketing-case-grid"><?php foreach (($d['cases']['items'] ?? []) as $index => $item) : ?><article data-aos="fade-up" data-aos-delay="<?php echo e($index * 60); ?>"><div class="tht-landing-marketing-case-visual"><?php if (! empty($item['image'])) : ?><img src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($item['image'])); ?>" alt="<?php echo e($item['title'] ?? 'Tư liệu Marketing'); ?>" loading="lazy"><?php else : ?><i class="fa-solid fa-chart-column" aria-hidden="true"></i><?php endif; ?><span>TƯ LIỆU TRIỂN KHAI</span><b>0<?php echo e($index + 1); ?></b></div><div><span><?php echo e($item['type'] ?? ''); ?></span><h3><?php echo e($item['title'] ?? ''); ?></h3><p><?php echo e($item['note'] ?? ''); ?></p></div></article><?php endforeach; ?></div>
            <p class="tht-landing-marketing-note"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><?php echo e($d['cases']['note'] ?? ''); ?></p>
        </div>
    </section>

    <section class="tht-landing-marketing-section tht-landing-marketing-reasons" aria-labelledby="marketing-reasons-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-marketing-section__heading" data-aos="fade-up"><h2 id="marketing-reasons-title" class="tht-landing-marketing-section__title"><?php echo e($d['reasons']['eyebrow'] ?? ''); ?></h2><p class="tht-landing-marketing-section__description"><?php echo e($d['reasons']['title'] ?? ''); ?></p></div>
            <div class="tht-landing-marketing-reason-grid"><?php foreach (($d['reasons']['items'] ?? []) as $index => $item) : ?><article data-aos="fade-up" data-aos-delay="<?php echo e($index * 70); ?>"><span>0<?php echo e($index + 1); ?></span><i class="fa-solid <?php echo e($item['icon'] ?? 'fa-check'); ?>" aria-hidden="true"></i><h3><?php echo e($item['title'] ?? ''); ?></h3><p><?php echo e($item['text'] ?? ''); ?></p></article><?php endforeach; ?></div>
        </div>
    </section>

    <section class="tht-landing-marketing-section tht-landing-marketing-faq" id="faq" aria-labelledby="marketing-faq-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
            <div class="tht-landing-marketing-faq__grid"><div class="tht-landing-marketing-section__heading" data-aos="fade-right"><h2 id="marketing-faq-title" class="tht-landing-marketing-section__title"><?php echo e($d['faq']['eyebrow'] ?? ''); ?></h2><p class="tht-landing-marketing-section__description"><?php echo e($d['faq']['title'] ?? ''); ?></p></div><div class="tht-landing-marketing-accordion tht-landing-marketing-accordion--faq" data-marketing-accordion data-aos="fade-left"><?php foreach (($d['faq']['items'] ?? []) as $index => $item) : ?><div class="tht-landing-marketing-accordion__item<?php echo $index === 0 ? ' is-open' : ''; ?>"><h3><button type="button" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>"><span><?php echo e($item['question'] ?? ''); ?></span><i class="fa-solid fa-plus" aria-hidden="true"></i></button></h3><div class="tht-landing-marketing-accordion__panel"><p><?php echo e($item['answer'] ?? ''); ?></p></div></div><?php endforeach; ?></div></div>
        </div>
    </section>

    <section class="tht-landing-marketing-final" id="audit" aria-labelledby="marketing-final-title">
        <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8 tht-landing-marketing-final__inner" data-aos="fade-up"><div><p class="tht-landing-marketing-kicker">THT MEDIA · OUTSOURCED MARKETING</p><h2 id="marketing-final-title"><?php echo e($d['contact_section']['title'] ?? ''); ?></h2><p><?php echo e($d['contact_section']['body'] ?? ''); ?></p></div><div class="tht-landing-marketing-final__actions"><a class="tht-landing-marketing-button tht-landing-marketing-button--light tht-landing-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer"><span class="tht-landing-zalo-icon" aria-hidden="true"></span> NHẮN ZALO</a><button class="tht-landing-marketing-button tht-landing-marketing-button--outline" type="button" data-landing-modal-open="#tht-landing-contact-modal"><?php echo e($d['contact_section']['cta'] ?? 'NHẬN AUDIT'); ?> <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button></div><p class="tht-landing-marketing-final__message">Gợi ý tin nhắn: “Tôi cần tư vấn Marketing thuê ngoài. Lĩnh vực: … Sản phẩm/dịch vụ chính: … Fanpage/Website: … Mục tiêu 3 tháng: … Ngân sách dự kiến: …”</p></div>
    </section>
</div>
