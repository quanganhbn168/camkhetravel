<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('homepage.faq_title', ['vi' => 'Câu hỏi thường gặp']);
        $this->migrator->add('homepage.faq_description', ['vi' => 'Trước khi sản xuất TVC doanh nghiệp']);
        $this->migrator->add('homepage.faq_items', [
            [
                'question' => ['vi' => 'Sản xuất một TVC mất bao lâu?'],
                'answer' => ['vi' => 'Thông thường từ 7 - 20 ngày tùy độ phức tạp. TVC đơn giản có thể nhanh hơn, còn TVC có concept riêng, diễn viên, nhiều bối cảnh và hậu kỳ nâng cao sẽ cần nhiều thời gian hơn.'],
            ],
            [
                'question' => ['vi' => 'Doanh nghiệp chưa có ý tưởng thì có làm được không?'],
                'answer' => ['vi' => 'Có. THT Media sẽ tư vấn concept, kịch bản, thông điệp, phong cách hình ảnh và cách triển khai phù hợp với mục tiêu truyền thông.'],
            ],
            [
                'question' => ['vi' => 'TVC có dùng để chạy quảng cáo Facebook, TikTok không?'],
                'answer' => ['vi' => 'Có. Video có thể được cắt thành nhiều phiên bản 16:9, 1:1 và 9:16 để dùng cho Facebook Ads, TikTok Ads, Reels, Shorts và landing page.'],
            ],
            [
                'question' => ['vi' => 'Chi phí TVC phụ thuộc vào những yếu tố nào?'],
                'answer' => ['vi' => 'Chi phí phụ thuộc vào concept, số ngày quay, thiết bị, bối cảnh, diễn viên, flycam, motion graphic, voice, nhạc bản quyền và số lượng phiên bản bàn giao.'],
            ],
        ]);
    }
};
