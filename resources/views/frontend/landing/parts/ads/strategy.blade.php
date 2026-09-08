<?php
$section = $args['landing']['strategy'] ?? [];
if (empty($section['items'])) return;
$icons = ['fa-compass', 'fa-chart-line', 'fa-headset', 'fa-file-lines', 'fa-diagram-project'];
?>
<section class="tht-landing-strategy ads-reasons" aria-labelledby="ads-reasons-title"><div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8"><div class="ads-section-head" data-aos="fade-up"><p class="tht-landing-eyebrow"><?php echo e($section['eyebrow'] ?? ''); ?></p><h2 id="ads-reasons-title"><?php echo e($section['title'] ?? ''); ?></h2></div><div class="ads-reason-grid"><?php foreach ($section['items'] as $index => $item) : ?><article data-aos="fade-up" data-aos-delay="<?php echo e((string) (($index % 3) * 50)); ?>"><span><i class="fa-solid <?php echo e($icons[$index] ?? 'fa-circle-check'); ?>" aria-hidden="true"></i></span><h3><?php echo e($item['title']); ?></h3><p><?php echo e($item['text']); ?></p></article><?php endforeach; ?></div></div></section>
