@props([
    'contact' => [],
    'section' => [],
    'zaloUrl' => null,
])

@php
    $formAction = (string) (($section['form_action'] ?? null) ?: ($contact['global_form_action'] ?? '#'));
@endphp

<div class="tht-landing-contact-modal modal" id="tht-landing-contact-modal" tabindex="-1" aria-labelledby="tht-landing-contact-modal-title" aria-hidden="true">
    <div class="tht-landing-contact-modal__dialog">
        <div class="tht-landing-contact-modal__content">
            <div class="tht-landing-contact-modal__header">
                <div>
                    <span class="tht-landing-modal__eyebrow">THT MEDIA</span>
                    <h2 id="tht-landing-contact-modal-title">{{ $section['title'] ?? 'Nhận tư vấn miễn phí' }}</h2>
                    <p>{{ $section['body'] ?? 'Để lại thông tin để THT Media tư vấn phương án phù hợp.' }}</p>
                </div>
                <button type="button" class="tht-landing-modal-close" data-landing-modal-close aria-label="Đóng"></button>
            </div>
            <div class="tht-landing-contact-modal__body">
                <div class="tht-landing-zalo-contact">
                    <p class="tht-landing-zalo-contact__copy">Nhắn Zalo để nhận phản hồi nhanh từ đội ngũ THT Media.</p>
                    <a class="tht-landing-zalo-cta" href="{{ $zaloUrl }}" target="_blank" rel="noopener noreferrer">
                        <span class="tht-landing-zalo-icon" aria-hidden="true"></span>
                        <span>NHẮN ZALO TƯ VẤN</span>
                    </a>
                </div>
                <div class="tht-landing-contact__form-divider"><span>Hoặc để lại thông tin</span></div>
                <form class="tht-landing-contact__form" action="{{ $formAction }}" method="POST" autocomplete="on">
                    <input type="hidden" name="source_url" value="{{ url()->current() }}">
                    <div class="grid gap-4">
                        <label class="tht-landing-form-group">Họ và tên <input type="text" name="fullname" required autocomplete="name" placeholder="Nguyễn Văn A"></label>
                        <label class="tht-landing-form-group">Số điện thoại <input type="tel" name="phone" required autocomplete="tel" placeholder="0987 654 321"></label>
                        <label class="tht-landing-form-group">Mô tả nhu cầu <textarea name="message" rows="4" required placeholder="Mô tả sơ lược nhu cầu cần tư vấn..."></textarea></label>
                    </div>
                    <button type="submit" class="tht-landing-button tht-landing-button--primary w-full">{{ $section['cta'] ?? 'NHẬN TƯ VẤN MIỄN PHÍ' }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
