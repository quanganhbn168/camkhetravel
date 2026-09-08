<?php

$strategy = $args['landing']['strategy'] ?? [];

if (empty($strategy)) {
    return;
}
?>

<section class="tht-landing-strategy" aria-labelledby="tht-landing-strategy-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <div class="grid items-center gap-8 lg:grid-cols-12 lg:gap-12">
            <div class="lg:col-span-7" data-aos="fade-right">
                <figure class="tht-landing-strategy__media">
                    <a
                        class="glightbox"
                        href="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($strategy['image'])); ?>"
                        data-gallery="tht-landing-strategy"
                    >
                        <img
                            src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($strategy['image'])); ?>"
                            alt="<?php echo e($strategy['image_alt'] ?? ''); ?>"
                            width="<?php echo e((string) ($strategy['width'] ?? 1448)); ?>"
                            height="<?php echo e((string) ($strategy['height'] ?? 1086)); ?>"
                            loading="lazy"
                            decoding="async"
                        >
                    </a>
                </figure>
            </div>
            <div class="lg:col-span-5" data-aos="fade-left">
                <div class="tht-landing-strategy__content">
                    <h2 id="tht-landing-strategy-title"><?php echo e($strategy['title'] ?? ''); ?></h2>
                    <p><?php echo e($strategy['body'] ?? ''); ?></p>
                    <a class="tht-landing-text-link" href="#lien-he">Nhận tư vấn</a>
                </div>
            </div>
        </div>
    </div>
</section>
