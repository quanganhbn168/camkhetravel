<?php

$solutions = $args['landing']['solutions'] ?? [];
$zalo_url = \App\Support\Landing\LandingView::zaloUrl($args['landing']['contact'] ?? []);

if (empty($solutions['items'])) {
    return;
}
?>

<section class="tht-landing-section tht-landing-solutions" id="giai-phap" aria-labelledby="tht-landing-solutions-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <div class="tht-landing-section-heading" data-aos="fade-up">
            <p class="tht-landing-eyebrow"><?php echo e($solutions['eyebrow'] ?? 'THT MEDIA SOLUTIONS'); ?></p>
            <h2 id="tht-landing-solutions-title" class="mb-3"><?php echo e($solutions['title'] ?? ''); ?></h2>
            <p class="text-slate-400" style="max-width: 800px; color: #94a3b8; font-size: 1.1rem;">
                <?php echo e($solutions['body'] ?? ''); ?>
            </p>
        </div>

        <!-- Desktop Tabs Layout (Visible on Desktop only) -->
        <div class="tht-landing-solutions__tabs" x-data="{ activeTab: 0 }" data-aos="fade-up" data-aos-delay="100">
            <!-- Navigation -->
            <nav class="tht-landing-solutions__nav" role="tablist">
                <?php foreach ($solutions['items'] as $index => $item) : ?>
                    <button
                        class="tht-landing-solutions__btn"
                        role="tab"
                        :class="{ 'active': activeTab === <?php echo $index; ?> }"
                        @click="activeTab = <?php echo $index; ?>"
                        :aria-selected="activeTab === <?php echo $index; ?> ? 'true' : 'false'"
                    >
                        <span><?php echo e($item['title']); ?></span>
                        <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                    </button>
                <?php endforeach; ?>
            </nav>

            <!-- Tab Panes -->
            <div class="tht-landing-solutions__content">
                <?php foreach ($solutions['items'] as $index => $item) :
                    $pane_bg = !empty($item['bg_image']) ? \App\Support\Landing\LandingRegistry::assetUrl($item['bg_image']) : '';
                    $pane_style = !empty($pane_bg) ? ' style="background-image: linear-gradient(rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.85)), url(' . e($pane_bg) . '); background-size: cover; background-position: center;"' : '';
                ?>
                    <div
                        class="tht-landing-solutions__pane<?php echo !empty($pane_bg) ? ' has-bg' : ''; ?>"
                        role="tabpanel"
                        x-show="activeTab === <?php echo $index; ?>"
                        x-cloak
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform translate-y-4"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        <?php echo $pane_style; ?>
                    >
                        <h3><?php echo e($item['title']); ?></h3>
                        <div class="tht-landing-solutions__list">
                            <?php foreach ($item['details'] as $detail) : ?>
                                <div class="tht-landing-solutions__item">
                                    <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                                    <span><?php echo e($detail); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Mobile Accordion Layout (Visible on Mobile/Tablet only) -->
        <div class="tht-landing-solutions__accordion" x-data="{ activeAccordion: 0 }" data-aos="fade-up" data-aos-delay="100">
            <?php foreach ($solutions['items'] as $index => $item) :
                $pane_bg = !empty($item['bg_image']) ? \App\Support\Landing\LandingRegistry::assetUrl($item['bg_image']) : '';
                $pane_style = !empty($pane_bg) ? ' style="background-image: linear-gradient(rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.85)), url(' . e($pane_bg) . '); background-size: cover; background-position: center;"' : '';
            ?>
                <div class="tht-landing-solutions__accordion-item mb-3">
                    <button
                        class="tht-landing-solutions__accordion-btn"
                        :class="{ 'active': activeAccordion === <?php echo $index; ?> }"
                        @click="activeAccordion = (activeAccordion === <?php echo $index; ?> ? -1 : <?php echo $index; ?>)"
                    >
                        <span><?php echo e($item['title']); ?></span>
                        <i class="fa-solid fa-chevron-down" :style="activeAccordion === <?php echo $index; ?> ? 'transform: rotate(180deg)' : ''" aria-hidden="true"></i>
                    </button>

                    <div
                        class="tht-landing-solutions__accordion-content<?php echo !empty($pane_bg) ? ' has-bg' : ''; ?>"
                        x-show="activeAccordion === <?php echo $index; ?>"
                        x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        <?php echo $pane_style; ?>
                    >
                        <div class="tht-landing-solutions__accordion-body p-4">
                            <div class="tht-landing-solutions__list">
                                <?php foreach ($item['details'] as $detail) : ?>
                                    <div class="tht-landing-solutions__item">
                                        <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                                        <span><?php echo e($detail); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="tht-landing-solutions__cta-box" data-aos="fade-up" data-aos-delay="100">
            <a class="tht-landing-button tht-landing-button--primary tht-landing-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer">
                <?php echo e($solutions['zalo_cta'] ?? 'NHẮN ZALO TƯ VẤN'); ?>
                <span class="tht-landing-zalo-icon" aria-hidden="true"></span>
            </a>
        </div>
    </div>
</section>
