<?php

$solutions = $args['landing']['solutions'] ?? [];

if (empty($solutions['items'])) {
    return;
}
?>

<section class="tht-landing-solutions" id="giai-phap" aria-labelledby="tht-landing-solutions-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <div class="tht-landing-section-heading" data-aos="fade-up">
            <p class="tht-landing-eyebrow"><?php echo e($solutions['eyebrow'] ?? ''); ?></p>
            <h2 id="tht-landing-solutions-title"><?php echo e($solutions['title'] ?? ''); ?></h2>
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
                    <div>
                        <h3><?php echo e($solution['title']); ?></h3>
                        <p><?php echo e($solution['text']); ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
