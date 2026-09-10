<?php
$landing = $args['landing'] ?? [];
$section = $landing['contact_section'] ?? [];
$contact = $landing['contact'] ?? [];
$action = \App\Support\Localization\LocalizedUrl::route('contact.store');
$zalo_url = \App\Support\Landing\LandingView::zaloUrl($contact);
?>
<section class="tht-landing-contact ads-contact" id="lien-he" aria-labelledby="ads-contact-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8 ads-contact__grid">
        <div class="ads-contact__copy" data-aos="fade-right">
            <p class="tht-landing-eyebrow"><?php echo e($section['eyebrow'] ?? ''); ?></p>
            <h2 id="ads-contact-title"><?php echo \App\Support\Landing\LandingView::safeHtml($section['title'] ?? '', ['br' => []]); ?></h2>
            <p><?php echo e($section['body'] ?? ''); ?></p>
            <div>
                <a href="tel:<?php echo e(preg_replace('/[^0-9+]/', '', (string) ($contact['hotline_1'] ?? ''))); ?>"><i class="fa-solid fa-phone"></i><?php echo e($contact['hotline_display'] ?? ''); ?></a>
                <a href="mailto:<?php echo e($contact['email'] ?? ''); ?>"><i class="fa-solid fa-envelope"></i><?php echo e($contact['email'] ?? ''); ?></a>
            </div>
        </div>
        <div class="ads-lead-form" data-aos="fade-left">
            <div class="tht-landing-zalo-contact">
                <p class="tht-landing-zalo-contact__copy">Cần phản hồi nhanh? Nhắn Zalo trực tiếp với THT Media.</p>
                <a class="tht-landing-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer"><span class="tht-landing-zalo-icon" aria-hidden="true"></span><span>NHẮN ZALO TƯ VẤN</span></a>
            </div>
            <div class="ads-contact__form-divider"><span>Hoặc để lại thông tin</span></div>
            <form action="<?php echo e($action); ?>" method="POST" autocomplete="on" data-landing-lead-form>
                <x-landing.lead-fields :landing-page="$landingPage ?? null" :service="$service ?? null" block-id="ads-contact" return-anchor="lien-he" />
                <div class="ads-form-grid">
                    <input type="text" name="name" value="{{ old('name') }}" required autocomplete="name" maxlength="255" placeholder="Họ và tên *">
                    <input type="tel" name="phone" value="{{ old('phone') }}" required autocomplete="tel" maxlength="32" placeholder="Số điện thoại *">
                </div>
                <textarea name="message" rows="4" maxlength="5000" placeholder="Mô tả nhu cầu cần tư vấn (tùy chọn)">{{ old('message') }}</textarea>
                <button type="submit"><?php echo e($section['cta'] ?? 'GỬI YÊU CẦU TƯ VẤN'); ?><i class="fa-solid fa-arrow-right"></i></button>
            </form>
        </div>
    </div>
</section>
