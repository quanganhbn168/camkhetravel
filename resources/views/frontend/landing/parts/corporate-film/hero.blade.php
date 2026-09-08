<?php

$hero = $args['landing']['hero'] ?? [];
$video = (string) ($hero['video'] ?? '');
$poster = (string) ($hero['poster'] ?? '');
$background_image = trim((string) ($hero['background_image'] ?? ''));
$background_image_url = $background_image !== '' ? \App\Support\Landing\LandingRegistry::assetUrl($background_image) : '';
$video_file = dirname(__DIR__, 2) . '/' . ltrim($video, '/');
$has_video = $video !== '' && is_readable($video_file);
$showreel_url = (string) ($hero['showreel_url'] ?? '');
$showreel_is_direct_video = $showreel_url !== '' && (bool) preg_match('/\.(?:mp4|webm|ogg)(?:[?#]|$)/i', $showreel_url);
$inline_video_url = $has_video ? \App\Support\Landing\LandingRegistry::assetUrl($video) : ($showreel_is_direct_video ? $showreel_url : '');
$poster_url = $poster !== '' ? \App\Support\Landing\LandingRegistry::assetUrl($poster) : ($background_image !== '' ? \App\Support\Landing\LandingRegistry::assetUrl($background_image) : '');
$media_background_url = $poster_url !== '' ? $poster_url : $background_image_url;
$zalo_url = \App\Support\Landing\LandingView::zaloUrl($args['landing']['contact'] ?? []);
?>

<section class="tht-landing-film-hero" data-landing-block="hero" id="tht-landing-film-showreel" aria-labelledby="tht-landing-film-hero-title">
    <div class="tht-landing-film-hero__media"<?php if ($media_background_url !== '') : ?> style="--tht-landing-film-background: url('<?php echo e($media_background_url); ?>');"<?php endif; ?>>
        <?php if ($inline_video_url !== '') : ?>
            <video class="tht-landing-film-hero__video" autoplay muted loop playsinline preload="auto" aria-hidden="true" tabindex="-1"<?php if ($poster_url !== '') : ?> poster="<?php echo e($poster_url); ?>"<?php endif; ?>><source src="<?php echo e($inline_video_url); ?>" type="video/mp4"></video>
        <?php elseif ($background_image_url !== '') : ?>
            <div class="tht-landing-film-hero__video tht-landing-film-hero__video-fallback" role="img" aria-label="Hình ảnh sản xuất phim doanh nghiệp tại THT Media"></div>
        <?php else : ?>
            <div class="tht-landing-film-hero__video tht-landing-film-hero__video-fallback" role="img" aria-label="THT Media sản xuất phim doanh nghiệp"></div>
        <?php endif; ?>
    </div>
    <div class="tht-landing-film-hero__overlay" aria-hidden="true"></div>
    <div class="tht-landing-film-hero__grain" aria-hidden="true"></div>

    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8 tht-landing-film-hero__container">
        <header class="tht-landing-film-hero__intro" data-aos="fade-up" data-aos-duration="800">
            <p class="tht-landing-film-hero__eyebrow tht-landing-film-hero__service-title"><?php echo e($hero['eyebrow'] ?? ''); ?></p>
            <h1 id="tht-landing-film-hero-title" class="tht-landing-film-hero__headline">
                <?php echo e($hero['title'] ?? ''); ?>
                <span><?php echo e($hero['title_highlight'] ?? ''); ?></span>
            </h1>
            <p class="tht-landing-film-hero__services"><?php echo e($hero['service_line'] ?? ''); ?></p>
        </header>

        <div class="tht-landing-film-hero__actions tht-landing-film-hero__actions--compact" data-aos="fade-up" data-aos-delay="140">
            <?php if ($inline_video_url !== '') : ?>
                <a class="tht-landing-film-hero__action tht-landing-film-hero__action--showreel glightbox" href="<?php echo e($inline_video_url); ?>" data-type="video" data-gallery="film-hero-video" data-autoplay="true" aria-label="Xem video nền THT Films">
                    <span class="tht-landing-film-hero__action-icon"><i class="fa-solid fa-play" aria-hidden="true"></i></span>
                    <span><strong><?php echo e($hero['showreel_cta'] ?? 'XEM VIDEO'); ?></strong></span>
                </a>
            <?php endif; ?>
            <a class="tht-landing-film-hero__action tht-landing-film-hero__action--contact tht-landing-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer">
                <span class="tht-landing-film-hero__action-icon"><span class="tht-landing-zalo-icon" aria-hidden="true"></span></span>
                <span><strong>NHẮN ZALO TƯ VẤN</strong></span>
            </a>
        </div>

        <footer class="tht-landing-film-hero__footer" data-aos="fade-up">
            <a href="#giai-phap" class="tht-landing-film-hero__scroll" aria-label="Cuộn xuống khám phá dịch vụ">
                <i class="fa-solid fa-arrow-down-long" aria-hidden="true"></i>
            </a>
        </footer>
    </div>
</section>
