<?php
$section = $args['landing']['faq'] ?? [];
if (empty($section['items'])) return;
?>
<section class="academy-section academy-reviews-section" aria-labelledby="academy-reviews-title">
    <div class="academy-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <div class="academy-section-head" data-aos="fade-up"><span class="academy-label"><?php echo e($section['eyebrow'] ?? ''); ?></span><h2 id="academy-reviews-title"><?php echo e($section['title'] ?? ''); ?></h2><p><?php echo e($section['body'] ?? ''); ?></p></div>
        <div class="academy-review-grid">
            <?php foreach ($section['items'] as $index => $item) : ?><article class="academy-review-card" data-aos="fade-up" data-aos-delay="<?php echo e((string) ($index * 60)); ?>"><span><i class="fa-solid <?php echo e($item['icon'] ?? 'fa-star'); ?>" aria-hidden="true"></i></span><h3><?php echo e($item['title']); ?></h3><p><?php echo e($item['text']); ?></p></article><?php endforeach; ?>
        </div>
    </div>
</section>
