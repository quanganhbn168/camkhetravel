<?php
$hero = $args['landing']['hero'] ?? [];
$zalo_url = \App\Support\Landing\LandingView::zaloUrl($args['landing']['contact'] ?? []);
$video = (string) ($hero['video'] ?? '');
$image = (string) ($hero['image'] ?? '');
$has_video = $video !== '' && is_readable(dirname(__DIR__, 2) . '/' . ltrim($video, '/'));
?>
<section class="ads-hero" data-landing-block="hero" aria-labelledby="ads-hero-title">
    <?php if ($image !== '') : ?><img class="ads-hero__image" src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($image)); ?>" alt="Không gian vận hành và tối ưu chiến dịch quảng cáo trực tuyến" width="1824" height="864" loading="eager" fetchpriority="high"><?php endif; ?>
    <?php if ($has_video) : ?><video class="ads-hero__video" autoplay muted loop playsinline preload="metadata" aria-hidden="true"><source src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($video)); ?>" type="video/mp4"></video><?php endif; ?>
    <div class="ads-hero__overlay" aria-hidden="true"></div>
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8 ads-hero__inner">
        <div class="ads-hero__content" data-aos="fade-up">
            <h1 id="ads-hero-title" class="ads-hero__brandline ads-hero__service-title"><?php echo e($hero['brand_line'] ?? ''); ?></h1>
            <p class="ads-hero__eyebrow"><?php echo e($hero['eyebrow'] ?? ''); ?></p>
            <p class="ads-hero__headline"><?php echo e($hero['title'] ?? ''); ?><span><?php echo e($hero['title_highlight'] ?? ''); ?></span></p>
            <div class="ads-hero__copy"><p><?php echo e($hero['summary'] ?? ''); ?></p><p><?php echo e($hero['summary_secondary'] ?? ''); ?></p></div>
            <div class="ads-hero__actions"><a class="tht-landing-button tht-landing-button--primary tht-landing-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer">NHẮN ZALO TƯ VẤN<span class="tht-landing-zalo-icon" aria-hidden="true"></span></a><a class="tht-landing-button tht-landing-button--ghost" href="#giai-phap"><i class="fa-solid fa-chart-line" aria-hidden="true"></i><?php echo e($hero['secondary'] ?? 'Xem giải pháp'); ?></a></div>
        </div>
        <div class="ads-hero__audience" data-aos="fade-up" data-aos-delay="80"><strong><?php echo e($hero['audience_title'] ?? ''); ?></strong><div><?php foreach (($hero['audiences'] ?? []) as $item) : ?><span><i class="fa-solid fa-circle-check" aria-hidden="true"></i><?php echo e($item); ?></span><?php endforeach; ?></div></div>
    </div>
</section>
