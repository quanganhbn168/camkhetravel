<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('bni_invitation.label', 'THƯ MỜI');
        $this->migrator->add('bni_invitation.event_label', 'LỄ CHUYỂN GIAO');
        $this->migrator->add('bni_invitation.greeting', 'Trân trọng kính mời');
        $this->migrator->add('bni_invitation.default_guest_name', 'Anh/Chị chủ doanh nghiệp');
        $this->migrator->add('bni_invitation.content_title', 'Nội dung chương trình');
        $this->migrator->add('bni_invitation.content', '<p>Ban tổ chức trân trọng kính mời anh/chị tham dự và cùng lan tỏa những giá trị kết nối trong sự kiện Lễ chuyển giao.</p>');
        $this->migrator->add('bni_invitation.schedule_title', 'Lịch trình sự kiện');
        $this->migrator->add('bni_invitation.note_title', 'Lưu ý tham dự');
        $this->migrator->add('bni_invitation.note_content', '<ul><li>Dress code: Trang phục lịch sự, phù hợp không khí sự kiện.</li><li>Vui lòng có mặt trước giờ bắt đầu để hoàn tất check-in.</li></ul>');
        $this->migrator->add('bni_invitation.rsvp_title', 'Xác nhận tham dự');
        $this->migrator->add('bni_invitation.rsvp_description', 'Anh/chị vui lòng xác nhận thông tin tham dự để Ban tổ chức chuẩn bị đón tiếp chu đáo.');
        $this->migrator->add('bni_invitation.contact_title', 'Liên hệ tham dự');
        $this->migrator->add('bni_invitation.contact_description', 'Nếu cần hỗ trợ thêm về lịch trình hoặc địa điểm, anh/chị vui lòng liên hệ đầu mối của chapter.');
    }
};
