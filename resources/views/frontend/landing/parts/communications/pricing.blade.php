<?php

$pricing = $args['landing']['pricing'] ?? [];
$zalo_url = \App\Support\Landing\LandingView::zaloUrl($args['landing']['contact'] ?? []);
$sections = array_values(array_filter(
    $pricing['pricing_sections'] ?? [],
    static fn (array $section): bool => !isset($section['status']) || (bool) $section['status']
));

if (empty($sections)) {
    return;
}

$format_money = static function ($amount): string {
    return number_format((float) $amount, 0, ',', '.') . 'đ';
};

$format_feature_value = static function ($value): string {
    if ($value === true) {
        return 'Đã bao gồm';
    }

    if ($value === false || $value === null || $value === '') {
        return '';
    }

    if (!is_array($value)) {
        return (string) $value;
    }

    $labels = [
        'total_posts' => 'bài',
        'image_posts' => 'bài ảnh',
        'videos' => 'video',
        'keywords' => 'từ khóa',
        'posts' => 'bài',
    ];
    $parts = [];

    foreach ($value as $key => $item) {
        if (is_scalar($item)) {
            $parts[] = trim((string) $item . ' ' . ($labels[$key] ?? ''));
        }
    }

    return implode(' · ', $parts);
};

$first_section_id = (string) ($sections[0]['id'] ?? 'facebook');
?>

<section
    class="tht-landing-section tht-landing-pricing"
    id="bang-gia"
    data-landing-block="pricing"
    aria-labelledby="tht-landing-pricing-title"
    x-data="{ activePricing: <?php echo e(json_encode($first_section_id)); ?> }"
>
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <div class="tht-landing-pricing__intro" data-aos="fade-up">
            <div>
                <p class="tht-landing-eyebrow"><?php echo e($pricing['eyebrow'] ?? 'Chi phí minh bạch · Lộ trình rõ ràng'); ?></p>
                <h2 id="tht-landing-pricing-title"><?php echo e($pricing['title'] ?? 'Bảng giá dịch vụ truyền thông'); ?></h2>
            </div>
            <p><?php echo e($pricing['description'] ?? ''); ?></p>
        </div>

        <div class="tht-landing-pricing__shell" data-aos="fade-up" data-aos-delay="80">
            <div class="tht-landing-pricing__core">
                <nav class="tht-landing-pricing__tabs" role="tablist" aria-label="Nhóm dịch vụ">
                    <?php foreach ($sections as $section) :
                        $section_id = (string) ($section['id'] ?? '');
                    ?>
                        <button
                            type="button"
                            class="tht-landing-pricing__tab"
                            role="tab"
                            :class="{ 'is-active': activePricing === <?php echo e(json_encode($section_id)); ?> }"
                            :aria-selected="activePricing === <?php echo e(json_encode($section_id)); ?>"
                            @click="activePricing = <?php echo e(json_encode($section_id)); ?>"
                        >
                            <?php echo e($section['name'] ?? 'Dịch vụ'); ?>
                        </button>
                    <?php endforeach; ?>
                </nav>

                <div class="tht-landing-pricing__panels">
                    <?php foreach ($sections as $section) :
                        $section_id = (string) ($section['id'] ?? '');
                    ?>
                        <div
                            class="tht-landing-pricing__panel"
                            role="tabpanel"
                            x-show="activePricing === <?php echo e(json_encode($section_id)); ?>"
                            x-cloak
                            x-transition:enter="tht-landing-pricing-enter"
                            x-transition:enter-start="tht-landing-pricing-enter-start"
                            x-transition:enter-end="tht-landing-pricing-enter-end"
                        >
                            <header class="tht-landing-pricing__panel-head">
                                <p><?php echo e($section['description'] ?? ''); ?></p>
                            </header>

                            <?php if (in_array($section_id, ['facebook', 'corporate-film'], true)) : ?>
                                <div class="tht-landing-pricing__cards tht-landing-pricing__cards--packages">
                                    <?php foreach ($section['packages'] ?? [] as $package_index => $package) :
                                        $is_featured = !empty($package['badge']);
                                    ?>
                                        <div class="tht-landing-price-card-shell<?php echo $is_featured ? ' is-featured' : ''; ?>">
                                            <article class="tht-landing-price-card">
                                                <header class="tht-landing-price-card__head">
                                                    <div>
                                                        <p class="tht-landing-price-card__index">Gói <?php echo e(str_pad((string) ($package_index + 1), 2, '0', STR_PAD_LEFT)); ?></p>
                                                        <h3><?php echo e($package['name'] ?? ''); ?></h3>
                                                    </div>
                                                    <?php if ($is_featured) : ?>
                                                        <span class="tht-landing-price-card__badge"><?php echo e($package['badge']); ?></span>
                                                    <?php endif; ?>
                                                </header>
                                                <div class="tht-landing-price-card__amount">
                                                    <strong><?php echo e($package['price_label'] ?? $format_money($package['price'] ?? 0)); ?></strong>
                                                    <span><?php echo e($package['billing_label'] ?? ''); ?></span>
                                                </div>
                                                <ul class="tht-landing-price-card__features">
                                                    <?php foreach ($package['features'] ?? [] as $feature) :
                                                        $feature_value = $format_feature_value($feature['value'] ?? null);
                                                    ?>
                                                        <li>
                                                            <span class="tht-landing-price-card__check" aria-hidden="true">✓</span>
                                                            <span>
                                                                <strong><?php echo e($feature['label'] ?? ''); ?></strong>
                                                                <?php if ($feature_value !== '') : ?>
                                                                    <small><?php echo e($feature_value); ?></small>
                                                                <?php endif; ?>
                                                            </span>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                                <?php if (!empty($package['note'])) : ?>
                                                    <p class="tht-landing-price-card__note"><?php echo e($package['note']); ?></p>
                                                <?php endif; ?>
                                                <a class="tht-landing-price-card__cta tht-landing-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer">
                                                    <span>Nhắn Zalo tư vấn</span>
                                                    <span class="tht-landing-price-card__cta-icon" aria-hidden="true">↗</span>
                                                </a>
                                            </article>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                            <?php elseif (!empty($section['services'])) : ?>
                                <div class="tht-landing-pricing__cards tht-landing-pricing__cards--services">
                                    <?php foreach ($section['services'] ?? [] as $service) : ?>
                                        <div class="tht-landing-price-card-shell">
                                            <article class="tht-landing-price-card tht-landing-price-card--service">
                                                <div class="tht-landing-price-card__service-top">
                                                    <h3><?php echo e($service['name'] ?? ''); ?></h3>
                                                    <div class="tht-landing-price-card__amount">
                                                        <strong><?php echo e($service['price_label'] ?? $format_money($service['price'] ?? 0)); ?></strong>
                                                        <span><?php echo e($service['unit'] ?? ''); ?></span>
                                                    </div>
                                                </div>
                                                <div class="tht-landing-price-card__meta">
                                                    <?php if (!empty($service['duration'])) : ?>
                                                        <span>Thời lượng <?php echo e($service['duration']); ?></span>
                                                    <?php endif; ?>
                                                    <?php if (!empty($service['deadline_days'])) : ?>
                                                        <span>Dự kiến <?php echo e($service['deadline_days']); ?> ngày</span>
                                                    <?php endif; ?>
                                                    <?php if (!empty($service['minimum_quantity'])) : ?>
                                                        <span>Tối thiểu <?php echo e($service['minimum_quantity']); ?> từ khóa</span>
                                                    <?php endif; ?>
                                                </div>
                                                <ul class="tht-landing-price-card__features">
                                                    <?php foreach ($service['includes'] ?? [] as $item) : ?>
                                                        <li><span class="tht-landing-price-card__check" aria-hidden="true">✓</span><span><?php echo e($item); ?></span></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                                <?php if (!empty($service['tiered_pricing'][0])) :
                                                    $tier = $service['tiered_pricing'][0];
                                                ?>
                                                    <p class="tht-landing-price-card__note">
                                                        Từ <?php echo e($tier['from_quantity']); ?> trang:
                                                        <strong><?php echo e($format_money($tier['price'])); ?><?php echo e($tier['unit'] ?? ''); ?></strong>
                                                    </p>
                                                <?php elseif (!empty($service['note']) || !empty($service['guarantee'])) : ?>
                                                    <p class="tht-landing-price-card__note"><?php echo e($service['note'] ?? $service['guarantee']); ?></p>
                                                <?php endif; ?>
                                                <a class="tht-landing-price-card__cta tht-landing-price-card__cta--compact tht-landing-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer">
                                                    <span>Nhắn Zalo tư vấn</span><span class="tht-landing-price-card__cta-icon" aria-hidden="true">↗</span>
                                                </a>
                                            </article>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                            <?php elseif ($section_id === 'tiktok') : ?>
                                <div class="tht-landing-pricing__cards tht-landing-pricing__cards--packages">
                                    <?php foreach ($section['packages'] ?? [] as $package_index => $package) : ?>
                                        <div class="tht-landing-price-card-shell<?php echo $package_index === 1 ? ' is-featured' : ''; ?>">
                                            <article class="tht-landing-price-card">
                                                <header class="tht-landing-price-card__head">
                                                    <div>
                                                        <p class="tht-landing-price-card__index"><?php echo e($package['video_duration'] ?? 'Video ngắn'); ?></p>
                                                        <h3><?php echo e($package['name'] ?? ''); ?></h3>
                                                    </div>
                                                    <?php if ($package_index === 1) : ?><span class="tht-landing-price-card__badge">Nâng tầm hình ảnh</span><?php endif; ?>
                                                </header>
                                                <p class="tht-landing-price-card__summary"><?php echo e($package['description'] ?? ''); ?></p>
                                                <div class="tht-landing-price-card__tiers">
                                                    <?php foreach ($package['pricing'] ?? [] as $tier) : ?>
                                                        <div>
                                                            <span><?php echo e($tier['package_name'] ?? ''); ?></span>
                                                            <strong><?php echo e($format_money($tier['price_per_video'] ?? 0)); ?><small>/video</small></strong>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                                <ul class="tht-landing-price-card__features">
                                                    <?php foreach ($package['includes'] ?? [] as $item) : ?>
                                                        <li><span class="tht-landing-price-card__check" aria-hidden="true">✓</span><span><?php echo e($item); ?></span></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                                <a class="tht-landing-price-card__cta tht-landing-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer">
                                                    <span>Nhắn Zalo tư vấn</span><span class="tht-landing-price-card__cta-icon" aria-hidden="true">↗</span>
                                                </a>
                                            </article>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                            <?php elseif ($section_id === 'advertising') : ?>
                                <div class="tht-landing-ad-pricing">
                                    <?php foreach ($section['management_fee_rules'] ?? [] as $rule) :
                                        $from = (float) ($rule['budget_from'] ?? 0);
                                        $to = $rule['budget_to'] ?? null;
                                        $budget_label = $to === null
                                            ? 'Từ ' . number_format($from / 1000000, 0, ',', '.') . ' triệu'
                                            : ($from <= 0
                                                ? 'Dưới ' . number_format(((float) $to) / 1000000, 0, ',', '.') . ' triệu'
                                                : number_format($from / 1000000, 0, ',', '.') . '–' . number_format(((float) $to) / 1000000, 0, ',', '.') . ' triệu');
                                        $fee_label = ($rule['fee_type'] ?? '') === 'percentage'
                                            ? number_format((float) ($rule['fee_value'] ?? 0), 0, ',', '.') . '%'
                                            : $format_money($rule['fee_value'] ?? 0);
                                    ?>
                                        <article class="tht-landing-ad-pricing__tier">
                                            <p>Ngân sách quảng cáo</p>
                                            <h3><?php echo e($budget_label); ?></h3>
                                            <div><span>Phí quản lý</span><strong><?php echo e($fee_label); ?></strong></div>
                                        </article>
                                    <?php endforeach; ?>
                                </div>
                                <div class="tht-landing-ad-pricing__extras">
                                    <p><strong>Tối đa 3 chiến dịch/tháng.</strong> Từ chiến dịch thứ <?php echo e($section['extra_campaign_fee']['from_campaign_number'] ?? 4); ?>: cộng <?php echo e($format_money($section['extra_campaign_fee']['fee_per_campaign'] ?? 0)); ?>/chiến dịch.</p>
                                    <p>Báo cáo định kỳ qua Zalo và Google Sheet, cho phép doanh nghiệp theo dõi trực tiếp dữ liệu chiến dịch.</p>
                                    <a class="tht-landing-price-card__cta tht-landing-price-card__cta--inline tht-landing-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer">
                                        <span>Nhắn Zalo tư vấn</span><span class="tht-landing-price-card__cta-icon" aria-hidden="true">↗</span>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <?php if (!empty($pricing['promotions'])) : ?>
            <div class="tht-landing-pricing__promotions" data-aos="fade-up">
                <div class="tht-landing-pricing__promo-heading">
                    <p>Ưu đãi đồng hành</p>
                    <h3>Tối ưu ngân sách khi triển khai dài hạn</h3>
                </div>
                <div class="tht-landing-pricing__promo-grid">
                    <?php foreach ($pricing['promotions'] as $promotion) : ?>
                        <article>
                            <strong>-<?php echo e($promotion['discount_value'] ?? 0); ?>%</strong>
                            <div>
                                <h4><?php echo e($promotion['name'] ?? ''); ?></h4>
                                <p><?php echo e($promotion['condition'] ?? ''); ?></p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($pricing['general_notes'])) : ?>
            <div class="tht-landing-pricing__notes" data-aos="fade-up">
                <span>Lưu ý báo giá</span>
                <ul>
                    <?php foreach ($pricing['general_notes'] as $note) : ?>
                        <li><?php echo e($note); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</section>
