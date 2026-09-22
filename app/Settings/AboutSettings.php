<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AboutSettings extends Settings
{
    public ?int $default_image_media_id = null;

    public array $page_stats = [];

    public string $page_intro = '';

    public string $story_title = '';

    public string $story = '';

    public ?int $story_image_media_id = null;

    public string $video_source = '';

    public string $video_youtube_url = '';

    public ?int $video_media_id = null;

    public ?int $video_poster_media_id = null;

    public string $history = '';

    public string $history_title = '';

    public string $history_description = '';

    public array $history_timeline = [];

    public string $mission = '';

    public string $vision = '';

    public string $core_values = '';

    public ?int $core_values_image_media_id = null;

    public string $principles_title = '';

    public string $services_title = '';

    public string $services_link_label = '';

    public string $stats_title = '';

    public string $office_title = '';

    public string $office_description = '';

    public ?int $office_image_media_id = null;

    public array $office_gallery = [];

    public string $cta_title = '';

    public string $cta_button_label = '';

    public static function group(): string
    {
        return 'about';
    }
}
