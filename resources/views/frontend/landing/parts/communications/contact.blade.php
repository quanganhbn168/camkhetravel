<?php

$landing = $args['landing'] ?? [];
$section = $landing['contact_section'] ?? [];
$contact = $landing['contact'] ?? [];
$phone1 = preg_replace('/[^0-9+]/', '', (string) ($contact['hotline_1'] ?? ''));
$phone2 = preg_replace('/[^0-9+]/', '', (string) ($contact['hotline_2'] ?? ''));
$zalo_url = \App\Support\Landing\LandingView::zaloUrl($contact);

// Background image check
$bg_image = !empty($section['bg_image']) ? \App\Support\Landing\LandingRegistry::assetUrl($section['bg_image']) : '';
$bg_style = !empty($bg_image) ? ' style="background-image: linear-gradient(rgba(255, 255, 255, 0.93), rgba(255, 255, 255, 0.93)), url(' . e($bg_image) . '); background-size: cover; background-position: center;"' : '';

$form_action = \App\Support\Localization\LocalizedUrl::route('contact.store');
?>

<section class="tht-landing-section tht-landing-contact" id="lien-he" aria-labelledby="tht-landing-contact-title"<?php echo $bg_style; ?>>
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <div class="tht-landing-contact__grid">
            <!-- Left Column: Contact details -->
            <div class="tht-landing-contact__info" data-aos="fade-up">
                <p class="tht-landing-eyebrow">Kết nối với chúng tôi</p>
                <h2 id="tht-landing-contact-title" class="mb-4"><?php echo e($section['title'] ?? 'ĐĂNG KÝ TƯ VẤN'); ?></h2>
                <p><?php echo e($section['body'] ?? ''); ?></p>

                <div class="tht-landing-contact__list">
                    <div class="tht-landing-contact__item">
                        <i class="fa-solid fa-phone" aria-hidden="true"></i>
                        <div>
                            <h5>Hotline hỗ trợ</h5>
                            <p>
                                <a href="tel:<?php echo e($phone1); ?>"><?php echo e($contact['hotline_1']); ?></a>
                                <?php if (! empty($contact['hotline_2'])) : ?>
                                     / <a href="tel:<?php echo e($phone2); ?>"><?php echo e($contact['hotline_2']); ?></a>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>

                    <div class="tht-landing-contact__item">
                        <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                        <div>
                            <h5>Email liên hệ</h5>
                            <p><a href="mailto:<?php echo e($contact['email']); ?>"><?php echo e($contact['email']); ?></a></p>
                        </div>
                    </div>

                    <div class="tht-landing-contact__item">
                        <i class="fa-solid fa-globe" aria-hidden="true"></i>
                        <div>
                            <h5>Website chính thức</h5>
                            <p><a href="<?php echo e($contact['website'] ?? url('/')); ?>" target="_blank" rel="noopener noreferrer"><?php echo e(str_replace(['https://', 'http://'], '', $contact['website'] ?? url('/'))); ?></a></p>
                        </div>
                    </div>

                    <?php if (! empty($contact['address_1'])) : ?>
                        <div class="tht-landing-contact__item">
                            <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                            <div>
                                <h5>Văn phòng làm việc</h5>
                                <p><?php echo e($contact['address_1']); ?></p>
                                <?php if (! empty($contact['address_2'])) : ?>
                                    <p class="mt-1"><?php echo e($contact['address_2']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Column: Registration Form -->
            <div class="tht-landing-contact__form-card" data-aos="fade-up" data-aos-delay="100">
                <div class="tht-landing-zalo-contact">
                    <p class="tht-landing-zalo-contact__copy">Nhắn Zalo để được phản hồi nhanh hơn về nhu cầu của anh/chị.</p>
                    <a class="tht-landing-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer">
                        <span class="tht-landing-zalo-icon" aria-hidden="true"></span>
                        <span>NHẮN ZALO TƯ VẤN</span>
                    </a>
                </div>
                <div class="tht-landing-contact__form-divider"><span>Hoặc để lại thông tin</span></div>
                <form class="tht-landing-contact__form" action="<?php echo e($form_action); ?>" method="POST" autocomplete="on">
                    <x-landing.lead-fields :landing-page="$landingPage ?? null" :service="$service ?? null" block-id="communications-contact" return-anchor="lien-he" />
                    <?php if ($errors->any()) : ?>
                        <div class="tht-landing-form-errors" role="alert">
                            <?php foreach ($errors->all() as $error) : ?><p><?php echo e($error); ?></p><?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <div class="tht-landing-form-group">
                        <label for="tht-landing-fullname">Họ và tên <span class="text-red-600">*</span></label>
                        <input type="text" id="tht-landing-fullname" name="name" value="{{ old('name') }}" required autocomplete="name" maxlength="255" placeholder="Nguyễn Văn A">
                    </div>



                    <div class="tht-landing-form-group">
                        <label for="tht-landing-phone">Số điện thoại <span class="text-red-600">*</span></label>
                        <input type="tel" id="tht-landing-phone" name="phone" value="{{ old('phone') }}" required autocomplete="tel" maxlength="32" placeholder="0987654321">
                    </div>



                    <div class="tht-landing-form-group">
                        <label for="tht-landing-message">Mô tả nhu cầu</label>
                        <textarea id="tht-landing-message" name="message" rows="4" maxlength="5000" placeholder="Mô tả sơ lược nhu cầu cần tư vấn...">{{ old('message') }}</textarea>
                    </div>

                    <button type="submit" class="tht-landing-button tht-landing-button--primary tht-landing-contact__btn">
                        <?php echo e($section['cta'] ?? 'NHẬN TƯ VẤN MIỄN PHÍ'); ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
