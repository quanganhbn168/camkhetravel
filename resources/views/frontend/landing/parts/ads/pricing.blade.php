<?php
$section = $args['landing']['pricing'] ?? [];
$zalo_url = \App\Support\Landing\LandingView::zaloUrl($args['landing']['contact'] ?? []);
if (empty($section['management_fee_rules'])) return;
$money = static fn ($value): string => number_format((float) $value, 0, ',', '.') . 'đ';
?>
<section class="ads-pricing" data-landing-block="pricing" id="bang-gia" aria-labelledby="ads-pricing-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <div class="ads-section-head" data-aos="fade-up"><p class="tht-landing-eyebrow"><?php echo e($section['eyebrow'] ?? ''); ?></p><h2 id="ads-pricing-title"><?php echo e($section['title'] ?? ''); ?></h2><p><?php echo e($section['description'] ?? ''); ?></p></div>
        <div class="ads-fee-grid">
            <?php foreach ($section['management_fee_rules'] as $index => $rule) :
                $from = (float) ($rule['budget_from'] ?? 0);
                $to = $rule['budget_to'] ?? null;
                $budget = $to === null ? 'Từ ' . number_format($from / 1000000, 0, ',', '.') . ' triệu' : ($from <= 0 ? 'Dưới ' . number_format(((float) $to) / 1000000, 0, ',', '.') . ' triệu' : number_format($from / 1000000, 0, ',', '.') . '–' . number_format(((float) $to) / 1000000, 0, ',', '.') . ' triệu');
                $fee = ($rule['fee_type'] ?? '') === 'percentage' ? number_format((float) $rule['fee_value'], 0, ',', '.') . '%' : $money($rule['fee_value']);
            ?>
                <article data-aos="fade-up" data-aos-delay="<?php echo e((string) (($index % 3) * 40)); ?>"><span>Ngân sách quảng cáo</span><h3><?php echo e($budget); ?></h3><div><small>Phí quản lý</small><strong><?php echo e($fee); ?></strong></div></article>
            <?php endforeach; ?>
        </div>
        <div class="ads-pricing-details" data-aos="fade-up">
            <article><span><i class="fa-solid fa-layer-group" aria-hidden="true"></i></span><div><h3>Chiến dịch bổ sung</h3><p>Tối đa 3 chiến dịch/tháng. Từ chiến dịch thứ <?php echo e((string) ($section['extra_campaign_fee']['from_campaign_number'] ?? 4)); ?>: cộng <strong><?php echo e($money($section['extra_campaign_fee']['fee_per_campaign'] ?? 0)); ?>/chiến dịch</strong>.</p></div></article>
            <article><span><i class="fa-solid fa-handshake" aria-hidden="true"></i></span><div><h3>Khách hàng bên ngoài</h3><p>Phí <strong><?php echo e((string) ($section['external_customer_fee']['fee_value'] ?? 15)); ?>%</strong> áp dụng trên <?php echo e(mb_strtolower($section['external_customer_fee']['applies_to'] ?? 'tổng giá trị dự án')); ?>.</p></div></article>
        </div>
        <div class="ads-reporting" data-aos="fade-up"><div><p class="tht-landing-eyebrow">Báo cáo & phối hợp</p><h3>Minh bạch trong suốt chiến dịch</h3></div><ul><?php foreach (($section['reporting'] ?? []) as $item) : ?><li><i class="fa-solid fa-circle-check" aria-hidden="true"></i><?php echo e($item); ?></li><?php endforeach; ?></ul></div>
        <div class="ads-pricing-notes"><ul><?php foreach (($section['notes'] ?? []) as $note) : ?><li><?php echo e($note); ?></li><?php endforeach; ?></ul><a class="tht-landing-button tht-landing-button--primary tht-landing-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer">NHẮN ZALO TƯ VẤN <span class="tht-landing-zalo-icon" aria-hidden="true"></span></a></div>
    </div>
</section>
