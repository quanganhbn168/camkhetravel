<?php

$landing = $args['landing'] ?? [];
$section = $landing['contact_section'] ?? [];
$contact = $landing['contact'] ?? [];
$action = ! empty($landing['form_action']) ? $landing['form_action'] : ($contact['global_form_action'] ?? '#');
$zalo_url = \App\Support\Landing\LandingView::zaloUrl($contact);
?>
<div class="modal fade ads-modal" id="ads-consultation-modal" tabindex="-1" aria-labelledby="ads-consultation-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <span class="ads-modal__eyebrow">THT MEDIA</span>
                    <h2 class="modal-title" id="ads-consultation-modal-title"><?php echo e($section['title'] ?? 'Nhận tư vấn quảng cáo miễn phí'); ?></h2>
                    <p>Để lại thông tin để đội ngũ THT Media tư vấn phương án quảng cáo phù hợp.</p>
                </div>
                <button type="button" class="tht-landing-modal-close" data-landing-modal-close aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="tht-landing-zalo-contact"><p class="tht-landing-zalo-contact__copy">Nhắn Zalo để nhận phản hồi nhanh từ đội ngũ THT Media.</p><a class="tht-landing-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer"><span class="tht-landing-zalo-icon" aria-hidden="true"></span><span>NHẮN ZALO TƯ VẤN</span></a></div>
                <div class="ads-contact__form-divider"><span>Hoặc để lại thông tin</span></div>
                <form class="ads-modal-form" data-tht-landing-fallback-form action="<?php echo e($action); ?>" method="POST" autocomplete="on">
                    <input type="hidden" name="source_url" value="<?php echo e(url()->current()); ?>">
                    <input type="hidden" name="request_type" value="ads_campaign_consultation">
                    <div class="ads-modal-form__grid">
                        <label for="ads-modal-fullname"><span>Họ và tên <b>*</b></span><input id="ads-modal-fullname" type="text" name="fullname" required autocomplete="name" placeholder="Nguyễn Văn A"></label>
                        <label for="ads-modal-phone"><span>Số điện thoại <b>*</b></span><input id="ads-modal-phone" type="tel" name="phone" required autocomplete="tel" placeholder="0987 654 321"></label>
                        <label class="ads-modal-form__wide" for="ads-modal-message"><span>Mô tả nhu cầu <b>*</b></span><textarea id="ads-modal-message" name="message" rows="3" required placeholder="Mô tả ngắn nhu cầu hoặc mục tiêu quảng cáo của bạn..."></textarea></label>
                    </div>
                    <button type="submit"><?php echo e($section['cta'] ?? 'Nhận tư vấn miễn phí'); ?><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
                    <small>Bằng việc gửi thông tin, bạn đồng ý để THT Media liên hệ tư vấn về giải pháp quảng cáo phù hợp.</small>
                </form>
            </div>
        </div>
    </div>
</div>
