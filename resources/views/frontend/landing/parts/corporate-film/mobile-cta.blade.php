<?php

$contact = $args['landing']['contact'] ?? [];
$phone = preg_replace('/[^0-9+]/', '', (string) ($contact['hotline_1'] ?? ''));
$zalo_icon = \App\Support\Landing\LandingRegistry::assetUrl('assets/images/icons8-zalo.svg');
?>

<aside class="tht-landing-mobile-cta md:hidden" aria-label="Liên hệ nhanh">
    <a href="tel:<?php echo e($phone); ?>">
        <i class="fa-solid fa-phone" aria-hidden="true"></i>
        <span>Gọi tư vấn</span>
    </a>
    <a href="<?php echo e($contact['zalo_url'] ?? '#'); ?>" target="_blank" rel="noopener noreferrer">
        <img class="tht-landing-zalo-icon" src="<?php echo e($zalo_icon); ?>" alt="" width="24" height="24" loading="eager">
        <span>Chat Zalo</span>
    </a>
</aside>
