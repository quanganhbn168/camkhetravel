<?php

$hero = $args['landing']['hero'] ?? [];
$benefits = $hero['benefits'] ?? [];
$background_image = trim((string) ($hero['background_image'] ?? ''));
$background_image_url = $background_image !== '' ? \App\Support\Landing\LandingRegistry::assetUrl($background_image) : '';

if (empty($benefits)) {
    return;
}
?>

<section class="tht-landing-film-benefits" id="gia-tri-bo-phim" aria-labelledby="tht-landing-film-benefits-title"<?php if ($background_image_url !== '') : ?> style="--tht-landing-film-benefits-background: url('<?php echo e($background_image_url); ?>');"<?php endif; ?>>
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <div class="tht-landing-film-benefits__inner">
            <header class="tht-landing-film-benefits__heading" data-aos="fade-up">
                <p class="tht-landing-eyebrow">GIÁ TRỊ BỘ PHIM MANG LẠI</p>
                <h2 id="tht-landing-film-benefits-title"><?php echo e($hero['benefits_title'] ?? ''); ?></h2>
            </header>

            <div class="tht-landing-film-benefits__grid">
                <?php foreach ($benefits as $index => $benefit) : ?>
                    <article class="tht-landing-film-benefits__item" data-aos="fade-up" data-aos-delay="<?php echo e((string) (($index % 3) * 70)); ?>">
                        <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                        <span><?php echo e($benefit); ?></span>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
