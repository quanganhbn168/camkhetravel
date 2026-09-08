<?php

$gallery = $args['landing']['gallery'] ?? [];
$items = $gallery['items'] ?? [];

if (empty($items)) {
    return;
}
?>
<section class="tht-landing-section tht-landing-gallery" id="hinh-anh-thuc-te" aria-labelledby="tht-landing-gallery-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <div class="tht-landing-section-heading text-center" data-aos="fade-up">
            <p class="tht-landing-eyebrow"><?php echo e($gallery['eyebrow'] ?? 'HÌNH ẢNH THỰC TẾ'); ?></p>
            <h2 id="tht-landing-gallery-title" class="mb-3"><?php echo e($gallery['title'] ?? ''); ?></h2>
            <?php if (! empty($gallery['body'])) : ?>
                <p class="tht-landing-gallery__intro mx-auto mb-0"><?php echo e($gallery['body']); ?></p>
            <?php endif; ?>
        </div>

        <div class="tht-landing-gallery__grid">
            <?php foreach ($items as $index => $item) :
                $image_url = \App\Support\Landing\LandingRegistry::assetUrl($item['image'] ?? '');
                $title = $item['title'] ?? '';
                $alt = $item['alt'] ?? $title;
            ?>
                <a
                    class="tht-landing-gallery__item glightbox"
                    href="<?php echo e($image_url); ?>"
                    data-gallery="tht-landing-real-gallery"
                    data-title="<?php echo e($title); ?>"
                    aria-label="<?php echo e(sprintf('Xem ảnh lớn: %s', $title)); ?>"
                    data-aos="fade-up"
                    data-aos-delay="<?php echo e((string) (($index % 4) * 50)); ?>"
                >
                    <img
                        src="<?php echo e($image_url); ?>"
                        alt="<?php echo e($alt); ?>"
                        width="<?php echo e((string) ($item['width'] ?? 1600)); ?>"
                        height="<?php echo e((string) ($item['height'] ?? 1067)); ?>"
                        loading="lazy"
                        decoding="async"
                    >
                    <span class="tht-landing-gallery__overlay" aria-hidden="true"></span>
                    <span class="tht-landing-gallery__zoom" aria-hidden="true"><i class="fa-solid fa-expand"></i></span>
                    <span class="visually-hidden"><?php echo e($title); ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <p class="tht-landing-gallery__hint mb-0"><i class="fa-regular fa-images" aria-hidden="true"></i> Chạm vào ảnh để xem trọn khung hình</p>
    </div>
</section>
