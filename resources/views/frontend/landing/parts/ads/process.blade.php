<?php

$process = $args['landing']['process'] ?? [];

if (empty($process['items'])) {
    return;
}
?>

<section class="tht-landing-process" id="quy-trinh" aria-labelledby="tht-landing-process-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <div class="tht-landing-process__title" data-aos="fade-up">
            <p class="tht-landing-eyebrow"><?php echo e($process['eyebrow'] ?? ''); ?></p>
            <h2 id="tht-landing-process-title"><?php echo e($process['title'] ?? ''); ?></h2>
        </div>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <?php foreach ($process['items'] as $index => $item) : ?>
                <div data-aos="fade-up" data-aos-delay="<?php echo e((string) ($index * 70)); ?>">
                    <article class="tht-landing-process__item">
                        <span aria-hidden="true"><?php echo e(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                        <h3><?php echo e($item['title']); ?></h3>
                        <p><?php echo e($item['text']); ?></p>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
