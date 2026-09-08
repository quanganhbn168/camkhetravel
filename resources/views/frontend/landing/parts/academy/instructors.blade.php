<?php
$section = $args['landing']['instructors'] ?? [];
if (empty($section['items'])) return;
?>
<section class="academy-section academy-mentor-section" id="giang-vien" aria-labelledby="academy-mentor-title">
    <div class="academy-container mx-auto w-full max-w-7xl px-4 lg:px-8 academy-mentor-grid">
        <div class="academy-mentor-placeholder academy-mentor-placeholder--photo" data-aos="fade-right"><img src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($section['image'] ?? '')); ?>" alt="Giảng viên hướng dẫn học viên sử dụng máy ảnh" width="1543" height="1029" loading="lazy"><div><strong>ĐỘI NGŨ<br>GIẢNG VIÊN</strong></div></div>
        <div class="academy-mentor-content" data-aos="fade-left"><span class="academy-label"><?php echo e($section['eyebrow'] ?? ''); ?></span><h2 id="academy-mentor-title"><?php echo nl2br(e($section['title'] ?? '')); ?></h2><p><?php echo e($section['body'] ?? ''); ?></p><div class="academy-mentor-list academy-mentor-stats"><?php foreach ($section['items'] as $item) : ?><article class="academy-mentor-item academy-mentor-stat"><strong><?php echo e($item['number'] ?? ''); ?></strong><h3><?php echo e($item['title']); ?></h3><p><?php echo e($item['text']); ?></p></article><?php endforeach; ?></div></div>
    </div>
</section>
