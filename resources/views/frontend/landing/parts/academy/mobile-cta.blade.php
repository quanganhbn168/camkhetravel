<?php
$contact = $args['landing']['contact'] ?? [];
$zalo_url = \App\Support\Landing\LandingView::zaloUrl($contact);
?>
<aside class="academy-mobile-cta"><a class="academy-mobile-cta__zalo" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer"><span class="tht-landing-zalo-icon" aria-hidden="true"></span>Nhắn Zalo</a><a href="tel:<?php echo e(preg_replace('/[^0-9+]/', '', (string) ($contact['hotline_1'] ?? ''))); ?>"><i class="fa-solid fa-phone"></i>Gọi tư vấn</a></aside>
