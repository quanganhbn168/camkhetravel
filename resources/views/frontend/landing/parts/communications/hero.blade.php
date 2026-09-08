<?php

$landing = $args['landing'] ?? [];
$hero = $landing['hero'] ?? [];
$cungcap = $hero['cung_cap'] ?? [];
$zalo_url = \App\Support\Landing\LandingView::zaloUrl($landing['contact'] ?? []);
?>

<section class="tht-landing-hero" data-landing-block="hero" aria-labelledby="tht-landing-hero-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8 tht-landing-hero__inner">
        <div class="grid items-center gap-8 lg:grid-cols-2">

            <!-- Left Column: Hero Content & Call to Actions -->
            <div data-aos="fade-right" data-aos-duration="800">
                <div class="tht-landing-hero__content">
                    <h1 id="tht-landing-hero-title" class="tht-landing-hero__badge mb-3"><?php echo e($hero['eyebrow'] ?? ''); ?></h1>
                    <h2 class="tht-landing-hero__tagline mb-3"><?php echo \App\Support\Landing\LandingView::safeHtml($hero['title'] ?? '', ['br' => [], 'span' => ['class' => []]]); ?></h2>
                    <p class="tht-landing-hero__summary mb-4"><?php echo e($hero['summary'] ?? ''); ?></p>

                    <!-- Offering Tags (Visible on Mobile/Tablet where right collage floats are hidden) -->
                    <?php if (!empty($cungcap)) : ?>
                        <div class="tht-landing-hero__tags tht-landing-hero__tags--mobile mb-4 pb-2">
                            <?php foreach ($cungcap as $tag) : ?>
                                <span class="tht-landing-hero__tag">
                                    <i class="fa-solid fa-circle-check mr-1" aria-hidden="true"></i>
                                    <?php echo e($tag); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div class="tht-landing-actions">
                        <a class="tht-landing-button tht-landing-hero__btn-cta tht-landing-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer">
                            <span><?php echo e($hero['zalo_cta'] ?? 'NHẮN ZALO TƯ VẤN'); ?></span>
                            <span class="tht-landing-hero__btn-icon"><span class="tht-landing-zalo-icon" aria-hidden="true"></span></span>
                        </a>
                        <a class="tht-landing-button tht-landing-button--ghost" href="#giai-phap">
                            <?php echo e($hero['secondary'] ?? 'Xem giải pháp'); ?>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Column: Visual Collage (Circle BG, Person Image & Floating Items) -->
            <div class="mt-5 hidden lg:mt-0 lg:block" data-aos="fade-left" data-aos-duration="800" data-aos-delay="100">
                <div class="tht-landing-hero__collage">

                    <!-- Solid Circle Background -->
                    <div class="tht-landing-hero__circle-bg" aria-hidden="true"></div>

                    <!-- Base Ellipse at the bottom -->
                    <div class="tht-landing-hero__base-ellipse" aria-hidden="true"></div>

                    <!-- Hero Main Portrait -->
                    <img
                        class="tht-landing-hero__person-img"
                        src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($hero['image'] ?? '')); ?>"
                        alt="<?php echo e($hero['image_alt'] ?? 'THT Media Hero Person Image'); ?>"
                        width="500"
                        height="500"
                        loading="eager"
                        fetchpriority="high"
                    >

                    <!-- Floating Cards dynamically loaded from data (6 items) -->
                    <?php
                    $icons = [
                        'fa-solid fa-compass',
                        'fa-solid fa-pen-nib',
                        'fa-solid fa-video',
                        'fa-solid fa-bullhorn',
                        'fa-solid fa-paper-plane',
                        'fa-solid fa-handshake-angle'
                    ];
                    foreach (($cungcap ?? []) as $index => $item) :
                        $icon = $icons[$index % count($icons)];
                    ?>
                        <div class="tht-landing-hero__float tht-landing-hero__float--pos-<?php echo $index; ?>" aria-hidden="true">
                            <div class="tht-landing-hero__float-icon-wrapper pos-<?php echo $index; ?>">
                                <i class="<?php echo e($icon); ?>"></i>
                            </div>
                            <div class="tht-landing-hero__float-text">
                                <?php echo e($item); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                </div>
            </div>

        </div>
    </div>
</section>
