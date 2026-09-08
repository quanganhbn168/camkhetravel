<?php

$positioning = $args['landing']['positioning'] ?? [];

if (empty($positioning)) {
    return;
}
?>

<section class="tht-landing-positioning" aria-labelledby="tht-landing-positioning-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <div class="tht-landing-positioning__heading" data-aos="fade-up">
            <h2 id="tht-landing-positioning-title"><?php echo e($positioning['title'] ?? ''); ?></h2>
        </div>
        <div class="grid gap-4 lg:grid-cols-12 lg:gap-8 tht-landing-positioning__body">
            <div class="lg:col-span-7" data-aos="fade-up" data-aos-delay="80">
                <p><?php echo e($positioning['body'] ?? ''); ?></p>
            </div>
            <div class="lg:col-span-5" data-aos="fade-up" data-aos-delay="140">
                <ul class="tht-landing-checklist" role="list">
                    <?php foreach (($positioning['items'] ?? []) as $item) : ?>
                        <li><?php echo e($item); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</section>
