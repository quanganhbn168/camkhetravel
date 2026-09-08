<?php
$section = $args['landing']['process'] ?? [];
$zalo_url = \App\Support\Landing\LandingView::zaloUrl($args['landing']['contact'] ?? []);
if (empty($section['items'])) return;
?>
<section class="academy-section academy-roadmap-section" id="lo-trinh" aria-labelledby="academy-roadmap-title">
    <div class="academy-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <div class="academy-section-head" data-aos="fade-up"><span class="academy-label"><?php echo e($section['eyebrow'] ?? ''); ?></span><h2 id="academy-roadmap-title"><?php echo e($section['title'] ?? ''); ?></h2></div>
        <div class="academy-roadmap-wrap academy-roadmap-wrap--six">
            <?php foreach ($section['items'] as $index => $item) : ?>
                <article class="academy-roadmap-item<?php echo $index === 0 ? ' academy-roadmap-item--trial' : ''; ?>" data-aos="fade-up" data-aos-delay="<?php echo e((string) (($index % 3) * 50)); ?>">
                    <div class="academy-roadmap-card"><span>Buổi <?php echo e((string) ($index + 1)); ?></span><h3><?php echo e($item['title']); ?></h3><p><?php echo e($item['text']); ?></p></div>
                </article>
            <?php endforeach; ?>
            <a class="academy-roadmap-item academy-roadmap-item--cta academy-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="Nhắn Zalo tư vấn khóa học">
                <span>NHẮN ZALO TƯ VẤN</span><strong>NHẮN ZALO TƯ VẤN KHÓA HỌC <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></strong>
            </a>
        </div>
    </div>
</section>
