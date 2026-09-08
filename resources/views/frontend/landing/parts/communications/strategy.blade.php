<?php

$strategy = $args['landing']['strategy'] ?? [];

if (empty($strategy['items'])) {
    return;
}

// Map custom FontAwesome icons for each USP item
$usp_icons = [
    0 => 'fa-solid fa-handshake-angle',
    1 => 'fa-solid fa-compass-drafting',
    2 => 'fa-solid fa-users-gear',
    3 => 'fa-solid fa-chart-line',
    4 => 'fa-solid fa-seedling'
];
?>

<section class="tht-landing-section tht-landing-strategy" aria-labelledby="tht-landing-strategy-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <div class="tht-landing-section-heading text-center" data-aos="fade-up">
            <p class="tht-landing-eyebrow">Giá trị cốt lõi</p>
            <h2 id="tht-landing-strategy-title" class="mb-5"><?php echo e($strategy['title'] ?? 'VÌ SAO DOANH NGHIỆP LỰA CHỌN THT MEDIA?'); ?></h2>
        </div>

        <div class="tht-landing-strategy__grid">
            <?php foreach ($strategy['items'] as $index => $item) :
                $icon_class = $usp_icons[$index] ?? 'fa-solid fa-circle-check';
            ?>
                <div class="tht-landing-strategy__card" data-aos="fade-up" data-aos-delay="<?php echo 100 * ($index % 3); ?>">
                    <div class="tht-landing-strategy__icon" aria-hidden="true">
                        <i class="<?php echo e($icon_class); ?>"></i>
                    </div>
                    <h4><?php echo e($item['title'] ?? ''); ?></h4>
                    <p><?php echo e($item['text'] ?? ''); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
