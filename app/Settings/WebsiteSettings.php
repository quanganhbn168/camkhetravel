<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class WebsiteSettings extends Settings
{
    public string $site_name;

    public string $tagline;

    public string $company_name;

    public string $contact_email;

    public string $hotline;

    public string $address;

    public string $facebook_url;

    public string $zalo_url;

    public string $youtube_url;

    public string $seo_title;

    public string $seo_description;

    public string $seo_keywords;

    public ?int $logo_media_id;

    public ?int $favicon_media_id = null;

    public ?int $seo_image_media_id;

    public ?int $company_profile_media_id = null;

    public ?int $about_image_media_id = null;

    public ?int $contact_image_media_id = null;

    public static function group(): string
    {
        return 'website';
    }
}
