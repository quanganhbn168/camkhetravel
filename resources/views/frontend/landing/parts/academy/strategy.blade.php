<?php
$section = $args['landing']['strategy'] ?? [];
if (empty($section['items'])) return;
?>
<section class="academy-section academy-difference-section" aria-labelledby="academy-practice-title">
    <div class="academy-container mx-auto w-full max-w-7xl px-4 lg:px-8 academy-difference-grid">
        <div class="academy-difference-content" data-aos="fade-right">
            <span class="academy-label"><?php echo e($section['eyebrow'] ?? ''); ?></span>
            <h2 id="academy-practice-title"><?php echo e($section['title'] ?? ''); ?></h2>
            <p><?php echo e($section['body'] ?? ''); ?></p>
            <div class="academy-difference-list">
                <?php foreach ($section['items'] as $item) : ?><div><i class="fa-solid fa-circle-check" aria-hidden="true"></i><span><?php echo e($item); ?></span></div><?php endforeach; ?>
            </div>
        </div>
        <div class="academy-practice-visual academy-practice-visual--photo" data-aos="fade-left" aria-label="Lớp học thực hành THT Academy">
            <img src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($section['image'] ?? '')); ?>" alt="Học viên THT Academy thực hành trong studio" width="1543" height="1029" loading="lazy">
            <div><strong>THỰC HÀNH<br>NGAY TỪ BUỔI ĐẦU</strong><small>Learn • Shoot • Review • Improve</small></div>
        </div>
    </div>
</section>
