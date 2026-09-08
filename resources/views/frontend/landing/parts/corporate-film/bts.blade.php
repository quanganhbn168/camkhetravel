<?php

$directory = storage_path('app/public/media/landing/pages/dichvulamphimdoanhnghiep/assets/images/bts');
$images = array_values(array_filter(glob($directory . '/*') ?: [], static function (string $image): bool {
    return in_array(strtolower((string) pathinfo($image, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp', 'avif'], true);
}));

if (empty($images)) {
    return;
}

natsort($images);
$relative_base = 'dichvulamphimdoanhnghiep/assets/images/bts/';
?>
<section class="tht-landing-section tht-landing-bts" id="hau-truong" aria-labelledby="tht-landing-bts-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <div class="tht-landing-section-heading text-center" data-aos="fade-up">
            <p class="tht-landing-eyebrow">BEHIND THE SCENES</p>
            <h2 id="tht-landing-bts-title">HẬU TRƯỜNG SẢN XUẤT</h2>
            <p>Những khoảnh khắc ekip THT Films chuẩn bị, ghi hình và hoàn thiện từng thước phim.</p>
        </div>
        <div class="tht-landing-bts__masonry" data-aos="fade-up" data-aos-delay="80">
            <?php foreach ($images as $image) : $filename = basename($image); ?>
                <a class="tht-landing-bts__slide glightbox" href="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($relative_base . $filename)); ?>" data-gallery="tht-landing-film-bts" data-title="Hậu trường sản xuất phim THT Media">
                    <img src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($relative_base . $filename)); ?>" alt="Hậu trường sản xuất phim THT Media" width="1200" height="800" loading="lazy" decoding="async">
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
