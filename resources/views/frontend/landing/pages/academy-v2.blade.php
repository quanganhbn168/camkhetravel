<?php


$hero = (array) ($landingContent['hero'] ?? []);
$contact = (array) ($landingContent['contact'] ?? []);
$zalo_url = \App\Support\Landing\LandingView::zaloUrl($contact);

?>
<section class="academy-v2-hero" data-landing-block="hero" aria-labelledby="academy-v2-hero-title">
    <div class="academy-v2-container mx-auto w-full max-w-7xl px-4 lg:px-8 academy-v2-hero__grid">
        <div class="academy-v2-hero__copy" data-aos="fade-up">
            <p class="academy-v2-eyebrow"><?php echo e($hero['eyebrow'] ?? ''); ?></p>
            <h1 id="academy-v2-hero-title"><?php echo e($hero['title'] ?? ''); ?></h1>
            <p class="academy-v2-hero__summary"><?php echo e($hero['summary'] ?? ''); ?></p>
            <div class="academy-v2-actions"><a class="academy-v2-button academy-v2-button--primary academy-v2-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer"><?php echo e($hero['primary'] ?? 'ĐĂNG KÝ HỌC THỬ'); ?><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a><a class="academy-v2-button academy-v2-button--text" href="#lo-trinh"><?php echo e($hero['secondary'] ?? 'XEM LỘ TRÌNH'); ?><i class="fa-solid fa-arrow-down" aria-hidden="true"></i></a></div>
            <p class="academy-v2-hero__note"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><?php echo e($hero['note'] ?? ''); ?></p>
        </div>
        <figure class="academy-v2-hero__visual" data-aos="fade-left"><img src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($hero['image'] ?? '')); ?>" alt="<?php echo e($hero['image_alt'] ?? ''); ?>" fetchpriority="high"><figcaption><span>01</span><span>HỌC THỰC CHIẾN · LÀM THỰC TẾ</span></figcaption></figure>
    </div>
    <div class="academy-v2-container mx-auto w-full max-w-7xl px-4 lg:px-8 academy-v2-facts"><?php foreach (($landingContent['facts'] ?? []) as $fact) : ?><div><strong><?php echo e($fact['value'] ?? ''); ?></strong><span><?php echo e($fact['label'] ?? ''); ?></span></div><?php endforeach; ?></div>
</section>

<section class="academy-v2-section academy-v2-curriculum" id="lo-trinh" aria-labelledby="academy-v2-curriculum-title">
    <div class="academy-v2-container mx-auto w-full max-w-7xl px-4 lg:px-8"><div class="academy-v2-section__head"><p class="academy-v2-eyebrow">LỘ TRÌNH 13 CHUYÊN ĐỀ</p><h2 id="academy-v2-curriculum-title">Từ chiếc máy ảnh đầu tiên đến bộ Portfolio có thể giới thiệu</h2><p>Mỗi chuyên đề nối tiếp một kỹ năng. Học viên nhìn thấy mình đang tiến bộ ở đâu và cần luyện tiếp điều gì.</p></div><div class="academy-v2-curriculum__list"><?php foreach (($landingContent['curriculum'] ?? []) as $item) : ?><article><span><?php echo e($item['number'] ?? ''); ?></span><div><h3><?php echo e($item['title'] ?? ''); ?></h3><p><?php echo e($item['text'] ?? ''); ?></p></div><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></article><?php endforeach; ?></div></div>
</section>

<section class="academy-v2-section academy-v2-outcomes" id="dau-ra" aria-labelledby="academy-v2-outcomes-title">
    <div class="academy-v2-container mx-auto w-full max-w-7xl px-4 lg:px-8"><div class="academy-v2-section__head"><p class="academy-v2-eyebrow">ĐẦU RA SAU KHÓA HỌC</p><h2 id="academy-v2-outcomes-title">Không chỉ biết chỉnh thông số. Biết tạo ra một bức ảnh có lý do.</h2></div><div class="academy-v2-outcomes__grid"><?php foreach (($landingContent['outcomes'] ?? []) as $item) : ?><article><span><?php echo e($item['number'] ?? ''); ?></span><h3><?php echo e($item['title'] ?? ''); ?></h3><p><?php echo e($item['text'] ?? ''); ?></p></article><?php endforeach; ?></div></div>
</section>

<section class="academy-v2-section academy-v2-workshop" aria-labelledby="academy-v2-workshop-title">
    <div class="academy-v2-container mx-auto w-full max-w-7xl px-4 lg:px-8 academy-v2-workshop__grid"><figure><img src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($landingContent['workshop']['image'] ?? '')); ?>" alt="Học viên thực hành tại THT Academy" loading="lazy"></figure><div><p class="academy-v2-eyebrow"><?php echo e($landingContent['workshop']['eyebrow'] ?? ''); ?></p><h2 id="academy-v2-workshop-title"><?php echo e($landingContent['workshop']['title'] ?? ''); ?></h2><p><?php echo e($landingContent['workshop']['text'] ?? ''); ?></p><ul><?php foreach (($landingContent['workshop']['items'] ?? []) as $item) : ?><li><i class="fa-solid fa-check" aria-hidden="true"></i><?php echo e($item); ?></li><?php endforeach; ?></ul></div></div>
</section>

<section class="academy-v2-section academy-v2-gallery" aria-labelledby="academy-v2-gallery-title"><div class="academy-v2-container mx-auto w-full max-w-7xl px-4 lg:px-8"><div class="academy-v2-section__head"><p class="academy-v2-eyebrow">TƯ LIỆU LỚP HỌC</p><h2 id="academy-v2-gallery-title">Nhìn thấy không khí học trước khi đăng ký</h2></div><div class="academy-v2-gallery__grid"><?php foreach (($landingContent['gallery'] ?? []) as $index => $item) : ?><figure class="academy-v2-gallery__item academy-v2-gallery__item--<?php echo e($index + 1); ?>"><img src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($item['image'] ?? '')); ?>" alt="<?php echo e($item['alt'] ?? 'Tư liệu lớp học'); ?>" loading="lazy"><figcaption>0<?php echo e($index + 1); ?> / THT ACADEMY</figcaption></figure><?php endforeach; ?></div></div></section>

<section class="academy-v2-section academy-v2-pricing" id="hoc-phi" aria-labelledby="academy-v2-pricing-title"><div class="academy-v2-container mx-auto w-full max-w-7xl px-4 lg:px-8 academy-v2-pricing__grid"><div><p class="academy-v2-eyebrow">THÔNG TIN KHÓA HỌC</p><h2 id="academy-v2-pricing-title">Đầu tư cho một lộ trình đủ ngắn để bắt đầu, đủ sâu để làm được.</h2><p><?php echo e($landingContent['pricing']['note'] ?? ''); ?></p><a class="academy-v2-button academy-v2-button--light academy-v2-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer">Hỏi lịch học gần nhất <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></div><div class="academy-v2-price-card"><div><strong><?php echo e($landingContent['pricing']['price'] ?? ''); ?></strong><span><?php echo e($landingContent['pricing']['unit'] ?? ''); ?></span></div><dl><?php foreach (($landingContent['pricing']['facts'] ?? []) as $fact) : ?><div><dt><?php echo e($fact['label'] ?? ''); ?></dt><dd><?php echo e($fact['value'] ?? ''); ?></dd></div><?php endforeach; ?></dl></div></div></section>

<section class="academy-v2-section academy-v2-faq" id="faq" aria-labelledby="academy-v2-faq-title"><div class="academy-v2-container mx-auto w-full max-w-7xl px-4 lg:px-8 academy-v2-faq__grid"><div><p class="academy-v2-eyebrow">FAQ</p><h2 id="academy-v2-faq-title">Trao đổi trước khi bắt đầu</h2></div><div><?php foreach (($landingContent['faq'] ?? []) as $item) : ?><details><summary><?php echo e($item['question'] ?? ''); ?><i class="fa-solid fa-plus" aria-hidden="true"></i></summary><p><?php echo e($item['answer'] ?? ''); ?></p></details><?php endforeach; ?></div></div></section>

<section class="academy-v2-final" id="dang-ky" aria-labelledby="academy-v2-final-title"><div class="academy-v2-container mx-auto w-full max-w-7xl px-4 lg:px-8"><p class="academy-v2-eyebrow">THT ACADEMY · VERSION 02</p><h2 id="academy-v2-final-title"><?php echo e($landingContent['contact_section']['title'] ?? ''); ?></h2><p><?php echo e($landingContent['contact_section']['body'] ?? ''); ?></p><a class="academy-v2-button academy-v2-button--light academy-v2-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer"><?php echo e($landingContent['contact_section']['cta'] ?? 'ĐĂNG KÝ'); ?> <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></div></section>
