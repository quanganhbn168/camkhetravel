<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AboutSettings extends Settings
{
    public array $page_label;

    public array $page_title;

    public array $page_intro;

    public array $story_title = [];

    public array $story;

    public ?int $story_image_media_id = null;

    public string $video_source = '';

    public string $video_youtube_url = '';

    public ?int $video_media_id = null;

    public ?int $video_poster_media_id = null;

    public array $history;

    public array $history_title = [];

    public array $history_description = [];

    public array $history_timeline = [];

    public array $mission;

    public array $vision;

    public array $core_values;

    public ?int $core_values_image_media_id = null;

    public array $principles_title = [];

    public array $services_title = [];

    public array $services_link_label = [];

    public array $stats_title = [];

    public array $team_title = [];

    public array $team_description = [];

    public ?int $team_image_media_id = null;

    public array $office_title = [];

    public array $office_description = [];

    public ?int $office_image_media_id = null;

    public array $cta_title = [];

    public array $cta_button_label = [];

    public static function group(): string
    {
        return 'about';
    }
}
