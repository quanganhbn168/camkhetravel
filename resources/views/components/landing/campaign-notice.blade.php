@props(['state' => null, 'message' => null])

@if ($state === 'upcoming')
    <div class="landing-campaign-notice">Chương trình chưa bắt đầu. Thời gian chính thức được hiển thị tại phần đếm ngược.</div>
@elseif ($state === 'expired' && ($landingPage->expired_behavior ?? 'show_message') === 'show_message')
    <div class="landing-campaign-notice landing-campaign-notice--expired">{{ $message ?: 'Chương trình đã kết thúc. Anh/chị vẫn có thể để lại thông tin để được tư vấn.' }}</div>
@endif
