<?php

$landing = $args['landing'] ?? [];
$section = $landing['contact_section'] ?? [];
$contact = $landing['contact'] ?? [];
$action = ! empty($landing['form_action']) ? $landing['form_action'] : ($contact['global_form_action'] ?? '#');
$zalo_url = \App\Support\Landing\LandingView::zaloUrl($contact);
?>
<div class="modal fade academy-modal" id="academy-enrollment-modal" tabindex="-1" aria-labelledby="academy-enrollment-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <span class="academy-modal__eyebrow">THT ACADEMY</span>
                    <h2 class="modal-title" id="academy-enrollment-modal-title"><?php echo e($section['title'] ?? 'Đăng ký học'); ?></h2>
                    <p>Để lại thông tin để đội ngũ THT Academy tư vấn lộ trình phù hợp với trình độ và mục tiêu của bạn.</p>
                </div>
                <button type="button" class="tht-landing-modal-close" data-landing-modal-close aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="academy-zalo-contact"><p class="academy-zalo-contact__copy">Nhắn Zalo để nhận phản hồi nhanh từ THT Academy.</p><a class="academy-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer"><span class="tht-landing-zalo-icon" aria-hidden="true"></span><span>NHẮN ZALO TƯ VẤN</span></a></div>
                <div class="academy-contact__form-divider"><span>Hoặc để lại thông tin</span></div>
                <form class="academy-modal-form" data-tht-landing-fallback-form action="<?php echo e($action); ?>" method="POST" autocomplete="on">
                    <input type="hidden" name="source_url" value="<?php echo e(url()->current()); ?>">
                    <input type="hidden" name="request_type" value="academy_course_consultation">
                    <div class="academy-modal-form__grid">
                        <label for="academy-modal-fullname"><span>Họ và tên <b>*</b></span><input id="academy-modal-fullname" type="text" name="fullname" required autocomplete="name" placeholder="Nguyễn Văn A"></label>
                        <label for="academy-modal-phone"><span>Số điện thoại <b>*</b></span><input id="academy-modal-phone" type="tel" name="phone" required autocomplete="tel" placeholder="0987 654 321"></label>
                        <label class="academy-modal-form__wide" for="academy-modal-message"><span>Mô tả nhu cầu <b>*</b></span><textarea id="academy-modal-message" name="message" rows="3" required placeholder="Mô tả ngắn nhu cầu học tập hoặc mục tiêu của bạn..."></textarea></label>
                    </div>
                    <button type="submit"><i class="fa-solid fa-paper-plane" aria-hidden="true"></i><?php echo e($section['cta'] ?? 'Đăng ký học'); ?></button>
                    <small>Bằng việc gửi thông tin, bạn đồng ý để THT Academy liên hệ tư vấn về khóa học phù hợp.</small>
                </form>
            </div>
        </div>
    </div>
</div>
