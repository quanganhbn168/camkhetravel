<?php

$positioning = $args['landing']['positioning'] ?? [];
$positioning_eyebrow = $positioning['eyebrow'] ?? 'Thách thức doanh nghiệp';
$positioning_image_alt = $positioning['image_alt'] ?? 'Khó khăn trong phân tích marketing doanh nghiệp';
$scroll_target = $positioning['scroll_target'] ?? '#giai-phap';

if (empty($positioning)) {
    return;
}
?>

<section class="tht-landing-section tht-landing-pain" aria-labelledby="tht-landing-pain-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <div class="tht-landing-pain__intro text-center" data-aos="fade-up">
            <p class="tht-landing-eyebrow"><?php echo e($positioning_eyebrow); ?></p>
            <h2 id="tht-landing-pain-title" class="mb-4"><?php echo $positioning['title'] ?? ''; ?></h2>
            <p class="text-slate-600"><?php echo e($positioning['body'] ?? ''); ?></p>
        </div>

        <div class="grid items-stretch gap-8 mb-5 lg:grid-cols-2">
            <!-- Left Column: Bottleneck Image -->
            <div class="flex" data-aos="fade-up">
                <div class="tht-landing-pain__image-wrapper flex-grow-1">
                    <?php if (!empty($positioning['image'])): ?>
                        <img class="tht-landing-pain__img" src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($positioning['image'])); ?>"
                            alt="<?php echo e($positioning_image_alt); ?>" width="800" height="600" loading="lazy">
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Column: 7 pain point cards in a responsive list grid -->
            <div data-aos="fade-up" data-aos-delay="100">
                <div class="tht-landing-pain__grid-list">
                    <?php foreach (($positioning['items'] ?? []) as $index => $item): ?>
                        <div class="tht-landing-pain__card">
                            <span class="tht-landing-pain__icon" aria-hidden="true">⚠️</span>
                            <span class="tht-landing-pain__text"><?php echo e($item); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Bounce down arrow to Solutions section -->
        <div class="text-center mt-5 tht-landing-pain__scroll-down" data-aos="fade-up" data-aos-delay="200">
            <a href="<?php echo e($scroll_target); ?>" aria-label="Cuộn xuống phần tiếp theo" class="tht-landing-bounce-arrow">
                <i class="fa-solid fa-circle-chevron-down"
                    style="font-size: 2.2rem; color: var(--tht-landing-accent-orange);"></i>
            </a>
        </div>

        <?php if (!empty($positioning['footer'])): ?>
            <div class="tht-landing-pain__footer" data-aos="fade-up">
                <span><?php echo e($positioning['footer']); ?></span>
            </div>
        <?php endif; ?>

    </div>
</section>
