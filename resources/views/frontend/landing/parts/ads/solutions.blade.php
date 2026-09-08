<?php

$solutions = $args['landing']['solutions'] ?? [];
$zalo_url = \App\Support\Landing\LandingView::zaloUrl($args['landing']['contact'] ?? []);

if (empty($solutions['items'])) {
    return;
}
?>

<section class="tht-landing-solutions" id="giai-phap" aria-labelledby="tht-landing-solutions-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <div class="tht-landing-section-heading" data-aos="fade-up">
            <p class="tht-landing-eyebrow"><?php echo e($solutions['eyebrow'] ?? ''); ?></p>
            <h2 id="tht-landing-solutions-title"><?php echo e($solutions['title'] ?? ''); ?></h2>
            <p><?php echo e($solutions['body'] ?? ''); ?></p>
        </div>

        <div class="tht-landing-solutions__grid">
            <?php foreach ($solutions['items'] as $index => $solution) : ?>
                <article
                    class="tht-landing-solution <?php echo e($solution['class'] ?? 'is-light'); ?>"
                    data-aos="fade-up"
                    data-aos-delay="<?php echo e((string) (($index % 3) * 70)); ?>"
                >
                    <span class="tht-landing-solution__index" aria-hidden="true">
                        <?php echo e(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?>
                    </span>
                    <?php if (! empty($solution['icon'])) : ?>
                        <span class="tht-landing-solution__visual" aria-hidden="true"><i class="<?php echo e($solution['icon']); ?>"></i></span>
                    <?php endif; ?>
                    <div>
                        <h3><?php echo e($solution['title']); ?></h3>
                        <p><?php echo e($solution['text']); ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="ads-solutions-cta"><a class="tht-landing-button tht-landing-button--primary tht-landing-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer">NHẮN ZALO TƯ VẤN<span class="tht-landing-zalo-icon" aria-hidden="true"></span></a></div>
    </div>
</section>
