<?php

$landing = $args['landing'] ?? [];
$section = $landing['contact_section'] ?? [];
$contact = $landing['contact'] ?? [];
$phone = preg_replace('/[^0-9+]/', '', (string) ($contact['hotline_1'] ?? ''));
$zalo_icon = \App\Support\Landing\LandingRegistry::assetUrl('assets/images/icons8-zalo.svg');
?>

<section class="tht-landing-contact" id="lien-he" aria-labelledby="tht-landing-contact-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <div class="tht-landing-contact__inner" data-aos="fade-up">
            <p class="tht-landing-eyebrow"><?php echo e($section['eyebrow'] ?? ''); ?></p>
            <h2 id="tht-landing-contact-title"><?php echo e($section['title'] ?? ''); ?></h2>
            <p><?php echo e($section['body'] ?? ''); ?></p>
            <div class="tht-landing-actions tht-landing-contact__actions">
                <a class="tht-landing-button tht-landing-button--light" href="tel:<?php echo e($phone); ?>">
                    <i class="fa-solid fa-phone" aria-hidden="true"></i>
                    <?php echo e($section['cta'] ?? 'Gọi tư vấn'); ?>: <?php echo e($contact['hotline_display'] ?? ''); ?>
                </a>
                <a class="tht-landing-button tht-landing-button--outline-light" href="<?php echo e($contact['zalo_url'] ?? '#'); ?>" target="_blank" rel="noopener noreferrer">
                    <img class="tht-landing-zalo-icon" src="<?php echo e($zalo_icon); ?>" alt="" width="24" height="24" loading="lazy">
                    <?php echo e($section['zalo'] ?? 'Trao đổi qua Zalo'); ?>
                </a>
            </div>
            <a class="tht-landing-contact__email" href="mailto:<?php echo e($contact['email'] ?? ''); ?>">
                <?php echo e($contact['email'] ?? ''); ?>
            </a>
        </div>
    </div>
</section>
