<?php

$showcase = $args['landing']['showcase'] ?? [];

if (empty($showcase['items'])) {
    return;
}
?>

<section class="tht-landing-section tht-landing-showcase" id="du-an" aria-labelledby="tht-landing-showcase-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <div class="tht-landing-section-heading text-center" data-aos="fade-up">
            <p class="tht-landing-eyebrow"><?php echo e($showcase['eyebrow'] ?? 'CASE STUDY'); ?></p>
            <h2 id="tht-landing-showcase-title" class="mb-3"><?php echo e($showcase['title'] ?? ''); ?></h2>
        </div>

        <div class="grid gap-4 tht-landing-showcase__grid mt-4 md:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($showcase['items'] as $index => $item) :
                $image_url = \App\Support\Landing\LandingRegistry::assetUrl($item['image']);
                $modal_id = 'tht-landing-project-modal-' . $index;
            ?>
                <div data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                    <div class="tht-landing-project-card">
                        <div class="tht-landing-project-card__image-wrapper">
                            <img
                                class="tht-landing-project-card__img"
                                src="<?php echo e($image_url); ?>"
                                alt="<?php echo e($item['image_alt'] ?? ''); ?>"
                                width="<?php echo e($item['width'] ?? 800); ?>"
                                height="<?php echo e($item['height'] ?? 500); ?>"
                                loading="lazy"
                                decoding="async"
                            >
                        </div>
                        <div class="tht-landing-project-card__content">
                            <span class="tht-landing-project-card__num">DỰ ÁN <?php echo str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT); ?></span>
                            <h3 class="tht-landing-project-card__title"><?php echo e($item['title'] ?? ''); ?></h3>
                            <p class="tht-landing-project-card__excerpt">
                                <?php
                                    // Short teaser from problem description
                                    $excerpt = $item['problem'] ?? '';
                                    if (mb_strlen($excerpt) > 100) {
                                        $excerpt = mb_substr($excerpt, 0, 97) . '...';
                                    }
                                    echo e($excerpt);
                                ?>
                            </p>
                            <button
                                class="tht-landing-project-card__btn"
                                data-landing-modal-open="#<?php echo $modal_id; ?>"
                                type="button"
                            >
                                Chi tiết <i class="fa-solid fa-arrow-right ml-1" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php foreach ($showcase['items'] as $index => $item) :
    $image_url = \App\Support\Landing\LandingRegistry::assetUrl($item['image']);
    $modal_id = 'tht-landing-project-modal-' . $index;
?>
    <!-- Modal for Project <?php echo $index + 1; ?> -->
    <div class="modal fade tht-landing-modal" id="<?php echo $modal_id; ?>" tabindex="-1" aria-labelledby="<?php echo $modal_id; ?>-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header flex items-start justify-between p-4 pb-0">
                    <div>
                        <span class="tht-landing-modal__eyebrow block mb-1" style="font-size: 0.85rem;">Case Study - Dự án <?php echo str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT); ?></span>
                        <h3 class="modal-title tht-landing-modal__title h4" id="<?php echo $modal_id; ?>-title" style="font-weight: 850; text-transform: uppercase; color: var(--tht-landing-ink);"><?php echo e($item['title'] ?? ''); ?></h3>
                    </div>
                    <button type="button" class="tht-landing-modal-close ml-2" data-landing-modal-close aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 p-md-5 pt-3">
                    <div class="tht-landing-modal__image-wrapper mb-4">
                        <img
                            class="tht-landing-modal__img"
                            src="<?php echo e($image_url); ?>"
                            alt="<?php echo e($item['image_alt'] ?? ''); ?>"
                            loading="lazy"
                        >
                    </div>

                    <div class="tht-landing-modal__details">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <div class="tht-landing-modal__detail-item">
                                    <div class="tht-landing-modal__detail-label">
                                        <i class="fa-solid fa-circle-exclamation text-red-600" aria-hidden="true"></i>
                                        <strong>Bài toán</strong>
                                    </div>
                                    <p><?php echo e($item['problem'] ?? ''); ?></p>
                                </div>
                            </div>
                            <div>
                                <div class="tht-landing-modal__detail-item">
                                    <div class="tht-landing-modal__detail-label">
                                        <i class="fa-solid fa-lightbulb text-amber-500" aria-hidden="true"></i>
                                        <strong>Giải pháp</strong>
                                    </div>
                                    <p><?php echo e($item['solution'] ?? ''); ?></p>
                                </div>
                            </div>
                            <div class="md:col-span-2">
                                <div class="tht-landing-modal__result">
                                    <div class="tht-landing-modal__result-label">
                                        <i class="fa-solid fa-chart-line text-green-600" aria-hidden="true"></i>
                                        <strong>Kết quả thực tế</strong>
                                    </div>
                                    <p class="mb-0 font-bold text-green-600"><?php echo e($item['result'] ?? ''); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
