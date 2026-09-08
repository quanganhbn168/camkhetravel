<?php

$faq = $args['landing']['faq'] ?? [];

if (empty($faq)) {
    return;
}
?>

<section class="tht-landing-section tht-landing-testimonials" id="khach-hang" aria-labelledby="tht-landing-faq-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <div class="tht-landing-section-heading text-center" data-aos="fade-up">
            <p class="tht-landing-eyebrow"><?php echo e($faq['eyebrow'] ?? 'Khách hàng & Đối tác'); ?></p>
            <h2 id="tht-landing-faq-title" class="mb-5">
                <?php echo e($faq['title'] ?? 'KHÁCH HÀNG ĐÃ ĐỒNG HÀNH CÙNG THT MEDIA'); ?>
            </h2>
        </div>
    </div>

    <!-- Full Width Infinite Logo Marquee -->
    <?php if (!empty($faq['logos'])): ?>
        <div class="tht-landing-partner__marquee" data-aos="fade-up" data-aos-delay="100">
            <div class="tht-landing-partner__track">
                <!-- First Set of Logos -->
                <?php foreach ($faq['logos'] as $logo): ?>
                    <div class="tht-landing-partner__logo">
                        <img src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($logo)); ?>" alt="Logo Đối Tác" width="240" height="80"
                            loading="lazy">
                    </div>
                <?php endforeach; ?>

                <!-- Second Set of Logos (Duplicate for Infinite Loop) -->
                <?php foreach ($faq['logos'] as $logo): ?>
                    <div class="tht-landing-partner__logo">
                        <img src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($logo)); ?>" alt="Logo Đối Tác" width="240" height="80"
                            loading="lazy">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <?php if (!empty($faq['testimonials'])) : ?>
            <div class="tht-landing-section-heading text-center mt-5" data-aos="fade-up">
                <h3 class="mb-4 uppercase"><?php echo e($faq['subtitle'] ?? 'Khách hàng nói gì?'); ?></h3>
            </div>

            <!-- Testimonials Cards Slider -->
            <div class="swiper tht-landing-testimonials-swiper mt-4" data-aos="fade-up" data-aos-delay="100">
            <div class="swiper-wrapper">
                <?php foreach (($faq['testimonials'] ?? []) as $index => $item):
                    // Extracts the first character of the actual name for avatar placeholder
                    $firstChar = mb_substr(preg_replace('/^(Ông|Bà)\s+/', '', $item['name']), 0, 1);
                    ?>
                    <div class="swiper-slide h-auto">
                        <article class="tht-landing-testimonial-card">
                            <div class="tht-landing-testimonial__stars" aria-label="Đánh giá 5 sao">
                                <?php for ($i = 0; $i < ($item['rating'] ?? 5); $i++): ?>
                                    <i class="fa-solid fa-star"></i>
                                <?php endfor; ?>
                            </div>
                            <p class="tht-landing-testimonial__text">
                                "<?php echo e($item['quote']); ?>"
                            </p>
                            <div class="tht-landing-testimonial__author mt-auto">
                                <div class="tht-landing-testimonial__avatar" aria-hidden="true">
                                    <?php echo e($firstChar); ?>
                                </div>
                                <div class="tht-landing-testimonial__info">
                                    <h5><?php echo e($item['name']); ?></h5>
                                    <p><?php echo e($item['role']); ?></p>
                                </div>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- Pagination dots -->
            <div class="swiper-pagination tht-landing-testimonials-pagination mt-5 static"></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($faq['items'])) : ?>
            <div class="grid gap-4 mt-2 md:grid-cols-2">
                <?php foreach ($faq['items'] as $index => $item) : ?>
                    <div data-aos="fade-up" data-aos-delay="<?php echo min($index * 60, 180); ?>">
                        <article class="tht-landing-testimonial-card">
                            <div class="tht-landing-testimonial__stars" aria-hidden="true">
                                <i class="fa-solid fa-circle-question"></i>
                            </div>
                            <h3><?php echo e($item['question'] ?? ''); ?></h3>
                            <p class="tht-landing-testimonial__text mb-0"><?php echo e($item['answer'] ?? ''); ?></p>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
