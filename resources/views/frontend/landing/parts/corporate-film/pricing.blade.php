<?php

$pricing = $args['landing']['pricing'] ?? [];
$sections = array_values(array_filter(
    $pricing['pricing_sections'] ?? [],
    static fn (array $section): bool => !isset($section['status']) || (bool) $section['status']
));
$section = $sections[0] ?? [];
$packages = $section['packages'] ?? [];
$managed_plans = $args['landing']['managed_pricing_plans'] ?? [];

if (empty($packages)) {
    return;
}

$format_money = static function ($amount): string {
    return number_format((float) $amount, 0, ',', '.') . 'đ';
};

$card_content = [
    'corporate-film-basic' => [
        'icon' => 'fa-clapperboard',
        'background' => 'dichvulamphimdoanhnghiep/assets/images/film-package-basic-v1.webp',
        'summary' => 'Phù hợp doanh nghiệp cần một video giới thiệu chỉn chu, rõ ràng và tối ưu ngân sách.',
        'highlights' => [
            'Ý tưởng và kịch bản chi tiết',
            'Sony A7S3 · Ekip 5 nhân sự',
            'Video 3–5 phút · Chất lượng 4K',
            'Tiến độ dự kiến 10–15 ngày',
        ],
    ],
    'corporate-film-standard' => [
        'icon' => 'fa-film',
        'background' => 'dichvulamphimdoanhnghiep/assets/images/film-package-standard-v1.webp',
        'summary' => 'Nâng cấp hình ảnh thương hiệu bằng thông điệp riêng, diễn xuất và góc máy giàu cảm xúc.',
        'highlights' => [
            'Kịch bản hiện đại, thông điệp riêng',
            'Flycam · Ánh sáng nghệ thuật',
            'Ekip 8 nhân sự · Quay 1–2 ngày',
            'Video 3–5 phút · Chất lượng 4K',
        ],
    ],
    'corporate-film-professional' => [
        'icon' => 'fa-wand-magic-sparkles',
        'background' => 'dichvulamphimdoanhnghiep/assets/images/film-package-professional-v2.webp',
        'summary' => 'Dành cho thương hiệu cần câu chuyện khác biệt cùng hình ảnh và hậu kỳ chuyên sâu.',
        'highlights' => [
            'Creative storytelling và diễn xuất',
            'Flycam · FPV · Graphic 2D',
            'Ekip 15 nhân sự · Quay 2 ngày',
            'Video 3–7 phút · Chất lượng 4K',
        ],
    ],
    'corporate-film-premium' => [
        'icon' => 'fa-crown',
        'background' => 'dichvulamphimdoanhnghiep/assets/images/film-package-premium-v1.webp',
        'summary' => 'Gói sản xuất cao cấp với tiêu chuẩn hình ảnh điện ảnh và nhận diện riêng của thương hiệu.',
        'highlights' => [
            'Arri Alexa · Hệ ánh sáng chuyên dụng',
            'Bối cảnh, makeup · Graphic 2D pro',
            'Ekip 20 nhân sự · Quay 2 ngày',
            'Video 3–7 phút · Chất lượng 4K',
        ],
    ],
];
?>

<section class="tht-landing-section tht-landing-film-pricing" data-landing-block="pricing" id="bang-gia" aria-labelledby="tht-landing-film-pricing-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <div class="tht-landing-film-pricing__intro" data-aos="fade-up">
            <div>
                <p class="tht-landing-eyebrow"><?php echo e($pricing['eyebrow'] ?? 'Báo giá THT Media 2026'); ?></p>
                <h2 id="tht-landing-film-pricing-title"><?php echo e($pricing['title'] ?? 'Bảng giá sản xuất phim doanh nghiệp'); ?></h2>
            </div>
            <p><?php echo e($pricing['description'] ?? ''); ?></p>
        </div>

        <div class="tht-landing-film-pricing__cards">
            <?php foreach ($packages as $package_index => $package) :
                $package_id = (string) ($package['id'] ?? 'package-' . $package_index);
                $package_slug = \App\Support\Landing\LandingView::className($package_id);
                $detail_template_id = 'tht-landing-film-package-detail-' . $package_slug;
                $presentation = $card_content[$package_id] ?? [
                    'icon' => 'fa-video',
                    'summary' => '',
                    'highlights' => array_slice(array_column($package['features'] ?? [], 'label'), 0, 4),
                ];
                $is_featured = !empty($package['badge']);
                $price_label = (string) ($package['price_label'] ?? $format_money($package['price'] ?? 0));
                $background_url = !empty($presentation['background'])
                    ? \App\Support\Landing\LandingRegistry::assetUrl((string) $presentation['background'])
                    : '';
                $sample_video_url = trim((string) ($package['sample_video_url'] ?? ''));
            ?>
                <article
                    class="tht-landing-film-price-card<?php echo $is_featured ? ' is-featured' : ''; ?>"
                    <?php if ($background_url !== '') : ?>style="--tht-landing-film-card-bg: url('<?php echo e($background_url); ?>');"<?php endif; ?>
                    data-aos="fade-up"
                    data-aos-delay="<?php echo e((string) ($package_index * 45)); ?>"
                >
                    <div class="tht-landing-film-price-card__topline">
                        <span class="tht-landing-film-price-card__number">0<?php echo e((string) ($package_index + 1)); ?></span>
                        <span class="tht-landing-film-price-card__icon" aria-hidden="true"><i class="fa-solid <?php echo e($presentation['icon']); ?>"></i></span>
                    </div>

                    <?php if ($is_featured) : ?>
                        <span class="tht-landing-film-price-card__badge"><?php echo e($package['badge']); ?></span>
                    <?php endif; ?>

                    <h3><?php echo e($package['name'] ?? ''); ?></h3>
                    <p class="tht-landing-film-price-card__summary"><?php echo e($presentation['summary']); ?></p>

                    <div class="tht-landing-film-price-card__price">
                        <strong><?php echo e($price_label); ?></strong>
                        <span><?php echo e($package['billing_label'] ?? ''); ?></span>
                    </div>

                    <ul class="tht-landing-film-price-card__highlights">
                        <?php foreach ($presentation['highlights'] as $highlight) : ?>
                            <li><i class="fa-solid fa-check" aria-hidden="true"></i><span><?php echo e($highlight); ?></span></li>
                        <?php endforeach; ?>
                    </ul>

                    <?php if ($sample_video_url !== '') : ?>
                        <a
                            class="tht-landing-film-price-card__sample glightbox"
                            href="<?php echo e($sample_video_url); ?>"
                            data-type="video"
                            data-gallery="tht-landing-film-package-samples"
                            data-title="Video mẫu — <?php echo e($package['name'] ?? ''); ?>"
                        >
                            <span><i class="fa-solid fa-play" aria-hidden="true"></i> Xem video mẫu</span>
                            <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
                        </a>
                    <?php endif; ?>

                    <button
                        type="button"
                        class="tht-landing-film-price-card__detail"
                        data-landing-modal-open="#tht-landing-contact-modal"
                        data-film-package-detail
                        data-film-package-template="<?php echo e($detail_template_id); ?>"
                        data-film-package-name="<?php echo e($package['name'] ?? ''); ?>"
                    >
                        <span>Xem chi tiết</span>
                        <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
                    </button>
                </article>

                <template id="<?php echo e($detail_template_id); ?>">
                    <div class="tht-landing-film-package-detail" data-package-name="<?php echo e($package['name'] ?? ''); ?>">
                        <div class="tht-landing-film-package-detail__hero">
                            <div>
                                <span>Gói sản xuất phim doanh nghiệp</span>
                                <h4><?php echo e($package['name'] ?? ''); ?></h4>
                                <p><?php echo e($presentation['summary']); ?></p>
                            </div>
                            <div class="tht-landing-film-package-detail__price">
                                <strong><?php echo e($price_label); ?></strong>
                                <span><?php echo e($package['billing_label'] ?? ''); ?></span>
                            </div>
                        </div>

                        <div class="tht-landing-film-package-detail__list">
                            <?php foreach ($package['features'] ?? [] as $feature) : ?>
                                <article>
                                    <span class="tht-landing-film-package-detail__check" aria-hidden="true"><i class="fa-solid fa-check"></i></span>
                                    <div>
                                        <h5><?php echo e($feature['label'] ?? ''); ?></h5>
                                        <p><?php echo e($feature['value'] ?? ''); ?></p>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>

                        <?php if (!empty($package['note'])) : ?>
                            <p class="tht-landing-film-package-detail__note"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><?php echo e($package['note']); ?></p>
                        <?php endif; ?>

                        <button type="button" class="tht-landing-film-package-detail__consult" data-film-package-consult>
                            <span>Nhận tư vấn gói <?php echo e($package['name'] ?? ''); ?></span>
                            <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
                        </button>
                    </div>
                </template>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($pricing['general_notes'])) : ?>
            <div class="tht-landing-film-pricing__notes" data-aos="fade-up">
                <strong><i class="fa-solid fa-circle-info" aria-hidden="true"></i> Lưu ý báo giá</strong>
                <ul>
                    <?php foreach ($pricing['general_notes'] as $note) : ?>
                        <li><?php echo e($note); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($managed_plans)) : ?>
            <div class="tht-landing-film-pricing__managed" data-aos="fade-up">
                <strong>Gói dịch vụ đang được quản trị cập nhật</strong>
                <ul>
                    <?php foreach ($managed_plans as $managed_plan) : ?>
                        <li>
                            <span><?php echo e($managed_plan['name'] ?? ''); ?></span>
                            <?php if (isset($managed_plan['list_price']) && $managed_plan['list_price'] !== null) : ?>
                                <b><?php echo e(number_format((float) $managed_plan['list_price'], 0, ',', '.').'đ'); ?></b>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</section>
