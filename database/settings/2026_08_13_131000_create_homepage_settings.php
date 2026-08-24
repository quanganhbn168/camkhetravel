<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('homepage.about_eyebrow', ['vi' => 'Về THT Media']);
        $this->migrator->add('homepage.about_title', ['vi' => 'THT Media đồng hành để mục tiêu truyền thông được triển khai thành trải nghiệm thật.']);
        $this->migrator->add('homepage.about_content', ['vi' => 'Chúng tôi kết nối định hướng, nội dung, hình ảnh và điểm chạm thương hiệu trong một quy trình rõ ràng — từ brief đến bàn giao.']);
        $this->migrator->add('homepage.commitments', ['vi' => "Giải pháp bám sát mục tiêu và bối cảnh thực tế\nMột đầu mối phối hợp xuyên suốt quá trình triển khai\nPhạm vi công việc và đầu ra được thống nhất rõ ràng"]);
        $this->migrator->add('homepage.capabilities', ['vi' => "Tư vấn định hướng nội dung và hình thức triển khai\nTổ chức sản xuất hình ảnh, video và nội dung truyền thông\nPhối hợp các hạng mục sự kiện và nhận diện thương hiệu\nQuản lý đầu việc, tiến độ và tài sản bàn giao"]);
        $this->migrator->add('homepage.consultation_title', ['vi' => 'Anh/chị đang chuẩn bị một dự án truyền thông?']);
        $this->migrator->add('homepage.consultation_content', ['vi' => 'Hãy gửi mục tiêu, phạm vi và thời gian dự kiến. THT Media sẽ liên hệ để cùng làm rõ hướng triển khai phù hợp.']);
    }
};
