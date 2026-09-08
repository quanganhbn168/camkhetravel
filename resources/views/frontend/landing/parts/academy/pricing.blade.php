<?php
$section = $args['landing']['pricing'] ?? [];
$zalo_url = \App\Support\Landing\LandingView::zaloUrl($args['landing']['contact'] ?? []);
if (empty($section)) return;
$price = number_format((float) ($section['price'] ?? 0), 0, ',', '.') . 'đ';
?>
<section class="academy-section academy-pricing-section" data-landing-block="pricing" id="hoc-phi" aria-labelledby="academy-pricing-title">
    <div class="academy-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <div class="academy-section-head" data-aos="fade-up"><span class="academy-label"><?php echo e($section['eyebrow'] ?? ''); ?></span><h2 id="academy-pricing-title"><?php echo e($section['title'] ?? ''); ?></h2></div>
        <div class="academy-pricing-layout">
            <article class="academy-price-card is-featured" data-aos="fade-up">
                <span class="academy-price-badge">Bắc Ninh</span><span class="academy-price-for">Học phí chính thức</span><h3>Khóa học Nhiếp ảnh thực chiến</h3><div class="academy-price"><?php echo e($price); ?><small><?php echo e($section['unit'] ?? ''); ?></small></div>
                <ul><?php foreach (($section['facts'] ?? []) as $fact) : ?><li><i class="fa-solid fa-circle-check" aria-hidden="true"></i><strong><?php echo e($fact['label']); ?>:</strong> <?php echo e($fact['value']); ?></li><?php endforeach; ?></ul>
                <a class="academy-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer">NHẮN ZALO TƯ VẤN</a>
            </article>
            <div class="academy-promo-stack" data-aos="fade-up" data-aos-delay="70">
                <?php foreach (($section['promotions'] ?? []) as $promotion) : ?><article><strong><?php echo empty($promotion['is_text']) ? '-' : ''; ?><?php echo e($promotion['value']); ?></strong><div><h3><?php echo e($promotion['title']); ?></h3><p><?php echo e($promotion['condition']); ?></p></div></article><?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
