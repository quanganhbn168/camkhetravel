<?php

$contact = $args['landing']['contact'] ?? [];
$phone = preg_replace('/[^0-9+]/', '', (string) ($contact['hotline_1'] ?? ''));
$zalo_icon = 'https://thtmedia.com.vn/wp-content/uploads/2024/02/stick_zalo.png';
$zalo_url = \App\Support\Landing\LandingView::zaloUrl($contact);
?>

<aside class="tht-landing-mobile-cta md:hidden" aria-label="Liên hệ nhanh">
    <a class="tht-landing-mobile-cta__zalo" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer">
        <img class="tht-landing-zalo-icon" src="<?php echo e($zalo_icon); ?>" alt="" width="24" height="24" loading="eager">
        <span>Nhắn Zalo</span>
    </a>
    <a href="tel:<?php echo e($phone); ?>">
        <i class="fa-solid fa-phone" aria-hidden="true"></i>
        <span>Gọi tư vấn</span>
    </a>
</aside>
