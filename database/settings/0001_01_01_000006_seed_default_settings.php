<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('website.site_name', 'DVTEC');
        $this->migrator->add('website.tagline', 'Giải pháp truyền thông và nội dung');
        $this->migrator->add('website.company_name', 'DVTEC');
        $this->migrator->add('website.contact_email', '');
        $this->migrator->add('website.hotline', '');
        $this->migrator->add('website.contact_phone', '');
        $this->migrator->add('website.address', '');
        $this->migrator->add('website.facebook_url', '');
        $this->migrator->add('website.zalo_url', '');
        $this->migrator->add('website.youtube_url', '');
        $this->migrator->add('website.seo_title', 'DVTEC');
        $this->migrator->add('website.seo_description', 'DVTEC cung cấp giải pháp truyền thông, nội dung và sản xuất hình ảnh cho doanh nghiệp.');
        $this->migrator->add('website.seo_keywords', 'DVTEC, truyền thông, nội dung, sản xuất hình ảnh');
        $this->migrator->add('website.logo_media_id', null);
        $this->migrator->add('website.favicon_media_id', null);
        $this->migrator->add('website.seo_image_media_id', null);
        $this->migrator->add('website.company_profile_media_id', null);
        $this->migrator->add('website.about_image_media_id', null);
        $this->migrator->add('website.contact_image_media_id', null);
        $this->migrator->add('website.banner_media_id', null);
        $this->migrator->add('website.header_menu_id', null);
        $this->migrator->add('website.footer_menu_id', null);
        $this->migrator->add('website.footer_background_media_id', null);
        $this->migrator->add('website.google_maps_embed_url', null);
        $this->migrator->add('website.google_maps_url', null);
        $this->migrator->add('website.phones', []);
        $this->migrator->add('website.branches', []);

        $this->migrator->add('homepage.about_eyebrow', ['vi' => 'Về DVTEC']);
        $this->migrator->add('homepage.about_title', ['vi' => 'DVTEC đồng hành để mục tiêu truyền thông được triển khai thành trải nghiệm thật.']);
        $this->migrator->add('homepage.about_content', ['vi' => 'Chúng tôi kết nối định hướng, nội dung, hình ảnh và điểm chạm thương hiệu trong một quy trình rõ ràng.']);
        $this->migrator->add('homepage.stats', []);
        $this->migrator->add('homepage.commitments', ['vi' => "Bám sát mục tiêu và bối cảnh thực tế\nMột đầu mối phối hợp xuyên suốt\nPhạm vi và đầu ra được thống nhất rõ ràng"]);
        $this->migrator->add('homepage.capabilities', ['vi' => "Tư vấn định hướng nội dung\nTổ chức sản xuất hình ảnh và video\nPhối hợp sự kiện và nhận diện thương hiệu\nQuản lý tiến độ và tài sản bàn giao"]);
        $this->migrator->add('homepage.consultation_title', ['vi' => 'Anh/chị đang chuẩn bị một dự án truyền thông?']);
        $this->migrator->add('homepage.consultation_content', ['vi' => 'Hãy gửi mục tiêu, phạm vi và thời gian dự kiến để DVTEC tư vấn hướng triển khai phù hợp.']);
        $this->migrator->add('homepage.faq_title', ['vi' => 'Câu hỏi thường gặp']);
        $this->migrator->add('homepage.faq_description', ['vi' => 'Thông tin cần biết trước khi bắt đầu dự án.']);
        $this->migrator->add('homepage.faq_items', []);

        $this->migrator->add('company.tax_code', '');
        $this->migrator->add('company.representative', '');
        $this->migrator->add('company.founded_year', null);
        $this->migrator->add('company.business_license', '');

        $this->migrator->add('about.default_image_media_id', null);
        $this->migrator->add('about.page_stats', []);
        $this->migrator->add('about.page_label', []);
        $this->migrator->add('about.page_title', []);
        $this->migrator->add('about.page_intro', []);
        $this->migrator->add('about.story_title', []);
        $this->migrator->add('about.story', []);
        $this->migrator->add('about.story_image_media_id', null);
        $this->migrator->add('about.video_source', '');
        $this->migrator->add('about.video_youtube_url', '');
        $this->migrator->add('about.video_media_id', null);
        $this->migrator->add('about.video_poster_media_id', null);
        $this->migrator->add('about.history', []);
        $this->migrator->add('about.history_title', []);
        $this->migrator->add('about.history_description', []);
        $this->migrator->add('about.history_timeline', []);
        $this->migrator->add('about.mission', []);
        $this->migrator->add('about.vision', []);
        $this->migrator->add('about.core_values', []);
        $this->migrator->add('about.core_values_image_media_id', null);
        $this->migrator->add('about.principles_title', []);
        $this->migrator->add('about.services_title', []);
        $this->migrator->add('about.services_link_label', []);
        $this->migrator->add('about.stats_title', []);
        $this->migrator->add('about.team_title', []);
        $this->migrator->add('about.team_description', []);
        $this->migrator->add('about.team_image_media_id', null);
        $this->migrator->add('about.office_title', []);
        $this->migrator->add('about.office_description', []);
        $this->migrator->add('about.office_image_media_id', null);
        $this->migrator->add('about.office_gallery', []);
        $this->migrator->add('about.cta_title', []);
        $this->migrator->add('about.cta_button_label', []);

        $this->migrator->add('design.color_primary', '#ee6b2d');
        $this->migrator->add('design.color_primary_hover', '#d84d20');
        $this->migrator->add('design.color_ink', '#10233e');
        $this->migrator->add('design.color_surface', '#f5f7fa');
        $this->migrator->add('design.color_muted', '#e7edf5');
        $this->migrator->add('design.font_size_base', '1rem');
        $this->migrator->add('design.font_size_body', '1rem');
        $this->migrator->add('design.font_size_small', '0.875rem');
        $this->migrator->add('design.font_size_h1', 'clamp(2.25rem, 4.5vw, 4rem)');
        $this->migrator->add('design.font_size_h2', 'clamp(1.75rem, 3vw, 2.75rem)');
        $this->migrator->add('design.font_size_h3', '1.25rem');
        $this->migrator->add('design.font_size_stat', 'clamp(2.25rem, 4vw, 3.75rem)');

        $this->migrator->add('tracking.google_analytics_code', null);
        $this->migrator->add('tracking.google_tag_manager_head_code', null);
        $this->migrator->add('tracking.google_tag_manager_body_code', null);
        $this->migrator->add('tracking.meta_pixel_code', null);
        $this->migrator->add('tracking.tiktok_pixel_code', null);
        $this->migrator->add('tracking.head_code', null);
        $this->migrator->add('tracking.body_open_code', null);
        $this->migrator->add('tracking.body_close_code', null);
    }
};
