<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class BniInvitationSettings extends Settings
{
    public string $label;

    public string $event_label;

    public string $greeting;

    public string $default_guest_name;

    public string $content_title;

    public string $content;

    public string $schedule_title;

    public string $note_title;

    public string $note_content;

    public string $rsvp_title;

    public string $rsvp_description;

    public string $contact_title;

    public string $contact_description;

    public static function group(): string
    {
        return 'bni_invitation';
    }
}
