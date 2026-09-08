<?php
$section = $args['landing']['showcase'] ?? [];
if (empty($section['items'])) return;
?>
<section class="tht-landing-showcase ads-cases" id="du-an" aria-labelledby="ads-cases-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <div class="ads-section-head" data-aos="fade-up">
            <p class="tht-landing-eyebrow"><?php echo e($section['eyebrow'] ?? ''); ?></p>
            <h2 id="ads-cases-title"><?php echo e($section['title'] ?? ''); ?></h2>
            <p><?php echo e($section['body'] ?? ''); ?></p>
        </div>
        <div class="ads-case-grid">
            <?php foreach ($section['items'] as $index => $item) : ?>
                <?php
                $images = array_values(array_filter((array) ($item['images'] ?? [])));
                if (empty($images) && ! empty($item['image'])) {
                    $images = [(string) $item['image']];
                }
                $image = $images[0] ?? '';
                $image_alt = (string) ($item['image_alt'] ?? $item['title'] ?? 'Case study quảng cáo');
                $gallery_id = 'ads-case-' . $index;
                ?>
                <article class="ads-case-card" data-aos="fade-up" data-aos-delay="<?php echo e((string) ($index * 60)); ?>">
                    <?php if ($image !== '') : ?>
                        <a class="ads-case-card__media glightbox" href="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($image)); ?>" data-gallery="<?php echo e($gallery_id); ?>" data-title="<?php echo e($item['title']); ?>" aria-label="Xem chi tiết chỉ số: <?php echo e($item['title']); ?>">
                            <img src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($image)); ?>" alt="<?php echo e($image_alt); ?>" width="800" height="450" loading="lazy">
                            <span><i class="fa-solid fa-chart-line" aria-hidden="true"></i>Xem chi tiết chỉ số</span>
                        </a>
                        <?php foreach (array_slice($images, 1) as $gallery_image) : ?>
                            <a class="glightbox ads-case-card__gallery-item" href="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($gallery_image)); ?>" data-gallery="<?php echo e($gallery_id); ?>" data-title="<?php echo e($item['title']); ?>" aria-hidden="true" tabindex="-1"></a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <header><span>DỰ ÁN <?php echo e(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span><i class="fa-solid fa-chart-column" aria-hidden="true"></i></header>
                    <h3><?php echo e($item['title']); ?></h3>
                    <dl>
                        <div><dt>Mục tiêu</dt><dd><?php echo e($item['goal']); ?></dd></div>
                        <div><dt>Giải pháp</dt><dd><?php echo e($item['solution']); ?></dd></div>
                        <div><dt>Kết quả</dt><dd><?php echo e($item['result']); ?></dd></div>
                    </dl>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
