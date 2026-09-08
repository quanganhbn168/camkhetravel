@php($data = $block['data'])

<x-service-consultation-form
    :content="$landingPage"
    :heading="$data['title'] ?? 'Đăng ký nhận hỗ trợ'"
    :description="$data['description'] ?? null"
    :button-label="$data['button_label'] ?? 'Gửi thông tin đăng ký'"
    :block-id="$block['id']"
/>
