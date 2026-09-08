<?php
$landing = $args['landing'] ?? [];
$section = $landing['contact_section'] ?? [];
$contact = $landing['contact'] ?? [];
$action = !empty($landing['form_action']) ? $landing['form_action'] : ($contact['global_form_action'] ?? '#');
$zalo_url = \App\Support\Landing\LandingView::zaloUrl($contact);
?>
<section class="academy-section academy-cta-section" id="dang-ky" aria-labelledby="academy-contact-title">
    <div class="academy-container mx-auto w-full max-w-7xl px-4 lg:px-8 academy-cta-box">
        <div class="academy-cta-content" data-aos="fade-right"><span class="academy-label"><?php echo e($section['eyebrow'] ?? ''); ?></span><h2 id="academy-contact-title"><?php echo e($section['title'] ?? ''); ?></h2><p><?php echo e($section['body'] ?? ''); ?></p><ul><li><i class="fa-solid fa-circle-check" aria-hidden="true"></i>Tư vấn lộ trình theo trình độ hiện tại</li><li><i class="fa-solid fa-circle-check" aria-hidden="true"></i>Định hướng theo đúng mục tiêu học tập</li><li><i class="fa-solid fa-circle-check" aria-hidden="true"></i>Trải nghiệm lớp học trước khi quyết định</li></ul></div>
        <div class="academy-lead-form" data-aos="fade-left"><div class="academy-zalo-contact"><p class="academy-zalo-contact__copy">Cần phản hồi nhanh hơn? Nhắn Zalo trực tiếp với THT Academy.</p><a class="academy-zalo-cta" href="<?php echo e($zalo_url); ?>" target="_blank" rel="noopener noreferrer"><span class="tht-landing-zalo-icon" aria-hidden="true"></span><span>NHẮN ZALO TƯ VẤN</span></a></div><div class="academy-contact__form-divider"><span>Hoặc để lại thông tin</span></div><form data-tht-landing-fallback-form action="<?php echo e($action); ?>" method="POST">
            <input type="hidden" name="source_url" value="<?php echo e(url()->current()); ?>">
            <input type="text" name="fullname" required placeholder="Họ và tên">
            <input type="tel" name="phone" required placeholder="Số điện thoại">
            <textarea name="message" rows="4" required placeholder="Mô tả nhu cầu học tập của bạn *"></textarea>
            <button type="submit"><i class="fa-solid fa-paper-plane" aria-hidden="true"></i>GỬI YÊU CẦU TƯ VẤN</button>
        </form></div>
    </div>
</section>
