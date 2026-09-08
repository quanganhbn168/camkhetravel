<?php
$hero = $args['landing']['hero'] ?? [];
$zalo_url = \App\Support\Landing\LandingView::zaloUrl($args['landing']['contact'] ?? []);
$video = (string) ($hero['video'] ?? '');
$poster = (string) ($hero['poster'] ?? '');
$video_file = dirname(__DIR__, 2) . '/' . ltrim($video, '/');
$has_video = $video !== '' && is_readable($video_file);
?>
<section class="academy-hero academy-hero--cinematic" data-landing-block="hero" aria-labelledby="academy-hero-title">
    <?php if ($has_video) : ?>
        <video class="academy-hero-video" autoplay muted loop playsinline preload="metadata" aria-hidden="true">
            <source src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($video)); ?>" type="video/mp4">
        </video>
    <?php elseif ($poster !== '') : ?>
        <img class="academy-hero-video" src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($poster)); ?>" alt="" width="1530" height="1020" fetchpriority="high" aria-hidden="true">
    <?php endif; ?>
    <div class="academy-hero-overlay" aria-hidden="true"></div>
    <div class="academy-container mx-auto w-full max-w-7xl px-4 lg:px-8 academy-hero-cinematic-inner">
        <div class="academy-hero-content" data-aos="fade-up">
            <p class="academy-hero-brandline"><?php echo e($hero['brand_line'] ?? ''); ?></p>
            <span class="academy-label"><?php echo e($hero['eyebrow'] ?? ''); ?></span>
            <p class="academy-hero-kicker"><?php echo e($hero['subline'] ?? ''); ?></p>
            <h1 id="academy-hero-title">
                <?php echo e($hero['title'] ?? ''); ?>
                <em><?php echo e($hero['title_highlight'] ?? ''); ?></em>
            </h1>
            <div class="academy-hero-copy">
                <p><?php echo e($hero['summary'] ?? ''); ?></p>
                <p><?php echo e($hero['summary_secondary'] ?? ''); ?></p>
            </div>
            <div class="academy-hero-actions">
                <a class="academy-btn academy-btn-primary academy-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer"><span class="tht-landing-zalo-icon" aria-hidden="true"></span>NHẮN ZALO TƯ VẤN</a>
                <a class="academy-btn academy-btn-outline" href="#dang-ky"><i class="fa-solid fa-route" aria-hidden="true"></i><?php echo e($hero['secondary'] ?? 'Để lại thông tin'); ?></a>
            </div>
        </div>

        <div class="academy-hero-panels" data-aos="fade-up" data-aos-delay="100">
            <div class="academy-hero-panel">
                <strong><?php echo e($hero['audience_title'] ?? ''); ?></strong>
                <div class="academy-hero-chips">
                    <?php foreach (($hero['audiences'] ?? []) as $item) : ?>
                        <span><i class="fa-solid fa-circle-check" aria-hidden="true"></i><?php echo e($item); ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="academy-hero-panel academy-hero-panel--method">
                <strong><?php echo e($hero['method_title'] ?? ''); ?></strong>
                <div class="academy-method-list">
                    <?php foreach (($hero['methods'] ?? []) as $index => $item) : ?>
                        <span><b><?php echo e(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></b><?php echo e($item); ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
