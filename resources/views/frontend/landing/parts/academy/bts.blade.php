<?php

$directory = storage_path('app/public/media/landing/pages/khoahocnhiepanhthucchien/assets/images/bts');
$images = array_values(array_filter(glob($directory . '/*') ?: [], static function (string $image): bool {
    return in_array(strtolower((string) pathinfo($image, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp', 'avif'], true);
}));

if (empty($images)) {
    return;
}

natsort($images);
$relative_base = 'khoahocnhiepanhthucchien/assets/images/bts/';
?>
<section class="academy-section academy-bts" id="hau-truong" aria-labelledby="academy-bts-title">
    <div class="academy-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <div class="academy-section-head" data-aos="fade-up">
            <span class="academy-label">BEHIND THE SCENES</span>
            <h2 id="academy-bts-title">HẬU TRƯỜNG LỚP HỌC</h2>
            <p>Những giờ thực hành, set-up ánh sáng và khoảnh khắc học viên cầm máy tại THT Academy.</p>
        </div>
        <div class="academy-bts__masonry" data-aos="fade-up" data-aos-delay="80">
            <?php foreach ($images as $image) : $filename = basename($image); ?>
                <a class="academy-bts__slide glightbox" href="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($relative_base . $filename)); ?>" data-gallery="academy-bts" data-title="Hậu trường lớp học nhiếp ảnh THT Academy">
                    <img src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($relative_base . $filename)); ?>" alt="Hậu trường lớp học nhiếp ảnh THT Academy" width="1200" height="800" loading="lazy" decoding="async">
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
