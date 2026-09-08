<?php
$section = $args['landing']['solutions'] ?? [];
$zalo_url = \App\Support\Landing\LandingView::zaloUrl($args['landing']['contact'] ?? []);
if (empty($section['items'])) return;
?>
<section class="academy-section academy-curriculum-section" id="noi-dung-hoc" aria-labelledby="academy-curriculum-title">
    <div class="academy-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <div class="academy-section-head" data-aos="fade-up">
            <span class="academy-label"><?php echo e($section['eyebrow'] ?? ''); ?></span>
            <h2 id="academy-curriculum-title"><?php echo e($section['title'] ?? ''); ?></h2>
            <p><?php echo e($section['body'] ?? ''); ?></p>
        </div>
        <div class="academy-curriculum-grid">
            <?php foreach ($section['items'] as $index => $item) : ?>
                <article class="academy-module-card" data-aos="fade-up" data-aos-delay="<?php echo e((string) ($index * 50)); ?>">
                    <div class="academy-module-card__media" aria-hidden="true"><img src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($item['image'] ?? '')); ?>" alt="" width="800" height="600" loading="lazy"></div>
                    <div class="academy-module-card__overlay" aria-hidden="true"></div>
                    <div class="academy-module-top"><span class="academy-module-icon"><i class="fa-solid <?php echo e($item['icon'] ?? 'fa-camera'); ?>" aria-hidden="true"></i></span><span>Module <?php echo e((string) ($index + 1)); ?></span></div>
                    <div class="academy-module-card__content"><h3><?php echo e($item['title']); ?></h3>
                    <ul><?php foreach (($item['details'] ?? []) as $detail) : ?><li><i class="fa-solid fa-check" aria-hidden="true"></i><?php echo e($detail); ?></li><?php endforeach; ?></ul></div>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="academy-section-cta"><a class="academy-btn academy-btn-primary academy-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer">NHẮN ZALO TƯ VẤN<i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></div>
    </div>
</section>
