<?php
$section = $args['landing']['positioning'] ?? [];
if (empty($section['items'])) return;
$icons = ['fa-camera-retro', 'fa-image', 'fa-sun', 'fa-sliders', 'fa-people-group', 'fa-briefcase'];
?>
<section class="academy-section academy-audience-section" aria-labelledby="academy-challenges-title">
    <div class="academy-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <div class="academy-section-head" data-aos="fade-up">
            <span class="academy-label"><?php echo e($section['eyebrow'] ?? ''); ?></span>
            <h2 id="academy-challenges-title"><?php echo e($section['title'] ?? ''); ?></h2>
            <p><?php echo e($section['body'] ?? ''); ?></p>
        </div>
        <div class="academy-challenge-grid">
            <?php foreach ($section['items'] as $index => $item) : ?>
                <article class="academy-challenge-card" data-aos="fade-up" data-aos-delay="<?php echo e((string) (($index % 3) * 50)); ?>">
                    <span class="academy-audience-icon"><i class="fa-solid <?php echo e($icons[$index] ?? 'fa-circle-exclamation'); ?>" aria-hidden="true"></i></span>
                    <p><?php echo e($item); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
        <p class="academy-section-note"><i class="fa-solid fa-arrow-trend-up" aria-hidden="true"></i><?php echo e($section['footer'] ?? ''); ?></p>
    </div>
</section>
