<?php
$section = $args['landing']['positioning'] ?? [];
if (empty($section['items'])) return;
$icons = ['fa-arrow-trend-up', 'fa-comments', 'fa-layer-group', 'fa-chart-pie', 'fa-pen-nib', 'fa-window-maximize', 'fa-gauge-high'];
?>
<section class="tht-landing-positioning ads-problem-section" aria-labelledby="ads-problem-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <header class="ads-problem-intro" data-aos="fade-up">
            <div>
                <p class="tht-landing-eyebrow"><?php echo e($section['eyebrow'] ?? ''); ?></p>
                <h2 id="ads-problem-title"><?php echo e($section['title'] ?? ''); ?></h2>
            </div>
            <p class="ads-problem-intro__copy"><?php echo e($section['body'] ?? ''); ?></p>
        </header>

        <div class="ads-problem-layout">
            <figure class="ads-problem-media" data-aos="fade-right">
                <img src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($section['image'] ?? '')); ?>" alt="Đội ngũ phân tích dữ liệu và hiệu suất chiến dịch quảng cáo" width="1122" height="1402" loading="lazy">
                <figcaption>
                    <span>Phân tích • Đo lường • Tối ưu</span>
                    <strong>Nhìn đúng vấn đề trước khi tăng ngân sách.</strong>
                </figcaption>
            </figure>

            <div class="ads-problem-list" aria-label="Những vấn đề quảng cáo thường gặp">
                <?php foreach ($section['items'] as $index => $item) : ?>
                    <article data-aos="fade-left" data-aos-delay="<?php echo e((string) (($index % 4) * 45)); ?>">
                        <span class="ads-problem-list__number"><?php echo e(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                        <i class="fa-solid <?php echo e($icons[$index] ?? 'fa-circle-exclamation'); ?>" aria-hidden="true"></i>
                        <p><?php echo e($item); ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>

        <blockquote class="ads-problem-conclusion" data-aos="fade-up">
            <span><i class="fa-solid fa-bullseye" aria-hidden="true"></i></span>
            <p><?php echo e($section['footer'] ?? ''); ?></p>
        </blockquote>
    </div>
</section>
