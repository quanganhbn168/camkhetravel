<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('website.site_name', 'THT Media');
        $this->migrator->add('website.tagline', 'Truyền thông, sản xuất nội dung và sự kiện');
        $this->migrator->add('website.company_name', 'THT Media');
        $this->migrator->add('website.contact_email', '');
        $this->migrator->add('website.hotline', '');
        $this->migrator->add('website.address', '');
        $this->migrator->add('website.facebook_url', '');
        $this->migrator->add('website.zalo_url', '');
        $this->migrator->add('website.youtube_url', '');
        $this->migrator->add('website.seo_title', 'THT Media');
        $this->migrator->add('website.seo_description', 'THT Media cung cấp giải pháp truyền thông, sản xuất nội dung và tổ chức sự kiện.');
        $this->migrator->add('website.seo_keywords', 'THT Media, truyền thông, quay phim, tổ chức sự kiện');
        $this->migrator->add('website.logo_media_id', null);
        $this->migrator->add('website.seo_image_media_id', null);
    }
};
