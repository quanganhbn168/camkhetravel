<?php

$process = $args['landing']['process'] ?? [];
$managed_process_items = $args['landing']['managed_process_items'] ?? [];

if (! empty($managed_process_items)) {
    $process['items'] = array_values(array_map(static function (array $item): array {
        return [
            'step' => $item['step'] ?? null,
            'title' => (string) ($item['title'] ?? ''),
            'text' => (string) ($item['text'] ?? $item['description'] ?? ''),
        ];
    }, array_filter($managed_process_items, 'is_array')));
}

if (empty($process['items'])) {
    return;
}
?>

<section class="tht-landing-section tht-landing-process" id="quy-trinh" aria-labelledby="tht-landing-process-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <div class="tht-landing-section-heading text-center" data-aos="fade-up">
            <p class="tht-landing-eyebrow">Quy trình làm việc</p>
            <h2 id="tht-landing-process-title" class="mb-5"><?php echo e($process['title'] ?? 'QUY TRÌNH TRIỂN KHAI'); ?></h2>
        </div>

        <div class="tht-landing-process__timeline">
            <?php foreach ($process['items'] as $index => $item) : ?>
                <div class="tht-landing-process__node" data-aos="fade-up" data-aos-delay="<?php echo min(50 * $index, 250); ?>">
                    <div class="tht-landing-process__badge" aria-hidden="true">
                        <?php echo e($item['step'] ?? str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?>
                    </div>
                    <article class="tht-landing-process__card">
                        <h4><?php echo e($item['title']); ?></h4>
                        <p><?php echo e($item['text']); ?></p>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
