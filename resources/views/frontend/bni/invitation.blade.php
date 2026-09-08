@extends('layouts.plan')

@use(App\Support\Localization\LocalizedUrl)

@section('body_class', 'bni-experience-page bni-plan-page bni-invitation-redesign')
@section('main_id', 'bni-invitation-main')
@section('main_class', 'bni-invitation-main')

@section('content')
    @php
        /*
        |--------------------------------------------------------------------------
        | DATA CŨ - GIỮ NGUYÊN CÁCH DÙNG
        |--------------------------------------------------------------------------
        | Blade này tiếp tục dùng:
        | $event, $invitationContent, $guestName, $invitation, $chapter,
        | $scheduleDays, $heroImageUrl, $directionsUrl, $isInvitationTemplate
        |
        | Ảnh cần copy vào:
        | public/images/bni/background_thumoi.jpg
        | public/images/bni/BNI5@4x.png
        | public/images/bni/BNI6@4x.png
        | public/images/bni/BNI7@4x.png
        | public/images/bni/BNI8@4x.png
        */

        $contacts = collect($invitationContent['contacts'] ?? []);
        $primaryContact = $contacts->first();
        $location = collect([$event->venue, $event->address])->filter()->implode(', ');

        $startAt = $event->starts_at;
        $endAt = $event->ends_at;

        $eventTime = $startAt?->format('H:i') ?: '09:00';
        $eventDate = $startAt?->format('d.m') ?: '03.10';
        $eventYear = $startAt?->format('Y') ?: now()->format('Y');
        $eventWeekday = $startAt?->translatedFormat('l') ?: 'Thứ bảy';

        $eventLabel = $invitationContent['event_label'] ?? $event->title ?? 'Lễ chuyển giao liên chapter';
        $heroLabel = $invitationContent['label'] ?? 'Thư mời';
        $greeting = $invitationContent['greeting'] ?? 'Trân trọng kính mời';
        $displayGuestName = filled($guestName ?? null) ? $guestName : 'QUÝ DOANH NGHIỆP / ĐỐI TÁC';

        $heroBackground = $heroImageUrl ?: asset('images/bni/background_thumoi.jpg');

        $primaryPhone = data_get($primaryContact, 'phone')
            ?: ($invitationContent['hotline'] ?? '0900 123 456');

        $primaryPhoneUrl = data_get($primaryContact, 'phone_url')
            ?: ('tel:' . preg_replace('/\s+/', '', $primaryPhone));

        $chapterLogos = [
            [
                'name' => 'BNI Famous',
                'image' => asset('images/bni/BNI5@4x.png'),
            ],
            [
                'name' => 'BNI KBG',
                'image' => asset('images/bni/BNI6@4x.png'),
            ],
            [
                'name' => 'BNI Impact',
                'image' => asset('images/bni/BNI7@4x.png'),
            ],
            [
                'name' => 'BNI KinhBac',
                'image' => asset('images/bni/BNI8@4x.png'),
            ],
        ];

    @endphp

    <style>
        :root {
            --bni-red: #cf202f;
            --bni-red-dark: #9f0c16;
            --bni-red-deep: #77060c;
            --bni-ink: #202124;
            --bni-muted: #6b6b70;
            --bni-line: rgba(207, 32, 47, .18);
            --bni-cream: #fffaf7;
            --bni-white: #ffffff;
            --bni-shadow: 0 18px 45px rgba(93, 14, 20, .09);
        }

        html {
            scroll-behavior: smooth;
        }

        .bni-invite-page,
        .bni-invite-page * {
            box-sizing: border-box;
        }

        .bni-invite-page {
            position: relative;
            overflow: hidden;
            color: var(--bni-ink);
            background:
                radial-gradient(circle at 90% 11%, rgba(207, 32, 47, .09), transparent 24rem),
                radial-gradient(circle at 4% 82%, rgba(207, 32, 47, .06), transparent 28rem),
                #fffdfb;
            font-family: var(--site-font-body);
        }

        .bni-invite-shell {
            width: min(1180px, calc(100% - 40px));
            margin-inline: auto;
        }

        .bni-invite-narrow {
            width: min(1040px, calc(100% - 40px));
            margin-inline: auto;
        }

        /* =========================
           HEADER
        ========================== */
        .bni-invite-topbar {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(255, 255, 255, .94);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(207, 32, 47, .08);
        }

        .bni-invite-topbar__inner {
            min-height: 78px;
            display: grid;
            grid-template-columns: 160px 1fr auto;
            align-items: center;
            gap: 26px;
        }

        .bni-invite-brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--bni-red);
            text-decoration: none;
        }

        .bni-invite-brand__logo {
            display: block;
            width: 84px;
            height: auto;
        }

        .bni-invite-nav {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 30px;
        }

        .bni-invite-nav a {
            position: relative;
            color: #3b3b3d;
            text-decoration: none;
            text-transform: uppercase;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .035em;
            transition: .2s ease;
        }

        .bni-invite-nav a::after {
            content: "";
            position: absolute;
            left: 0;
            right: 100%;
            bottom: -8px;
            height: 2px;
            background: var(--bni-red);
            transition: .22s ease;
        }

        .bni-invite-nav a:hover {
            color: var(--bni-red);
        }

        .bni-invite-nav a:hover::after {
            right: 0;
        }

        .bni-invite-button {
            display: inline-flex;
            min-height: 48px;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 0 22px;
            border: 1px solid var(--bni-red);
            border-radius: 10px;
            background: var(--bni-red);
            color: #fff;
            text-decoration: none;
            text-transform: uppercase;
            font-size: 13px;
            font-weight: 900;
            letter-spacing: .02em;
            box-shadow: 0 12px 26px rgba(207, 32, 47, .19);
            transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
        }

        .bni-invite-button:hover {
            color: #fff;
            background: #b91220;
            transform: translateY(-2px);
            box-shadow: 0 16px 32px rgba(207, 32, 47, .26);
        }

        .bni-invite-button--ghost {
            color: var(--bni-red);
            background: rgba(255, 255, 255, .88);
            box-shadow: none;
        }

        .bni-invite-button--ghost:hover {
            color: #fff;
        }

        /* =========================
           HERO
        ========================== */
        .bni-invite-hero {
            position: relative;
            min-height: 690px;
            display: flex;
            align-items: center;
            isolation: isolate;
            overflow: hidden;
            background: #fff8f4;
        }

        .bni-invite-hero__bg {
            position: absolute;
            inset: 0;
            z-index: -3;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        .bni-invite-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -2;
            background:
                linear-gradient(90deg, rgba(255, 255, 255, .98) 0%, rgba(255, 255, 255, .92) 36%, rgba(255, 255, 255, .43) 59%, rgba(255, 255, 255, .02) 100%),
                linear-gradient(180deg, rgba(255, 255, 255, .15), rgba(255, 244, 240, .1));
        }

        .bni-invite-hero::after {
            content: "";
            position: absolute;
            inset: auto 0 0;
            z-index: -1;
            height: 170px;
            background: linear-gradient(180deg, transparent, #fffdfb);
        }

        .bni-invite-hero__content {
            width: min(670px, 58%);
            padding: 84px 0 115px;
            text-align: center;
        }

        .bni-invite-hero__label {
            width: fit-content;
            max-width: 100%;
            margin: 0 auto;
            color: var(--bni-red);
            font-size: clamp(36px, 4.2vw, 68px);
            font-weight: 950;
            line-height: .92;
            letter-spacing: -.045em;
            text-align: center;
        }

        .bni-invite-hero__greeting {
            margin: 9px 0 13px;
            color: var(--bni-red);
            font-family: var(--site-font-display);
            font-size: clamp(20px, 2vw, 31px);
            font-style: italic;
            line-height: 1;
        }

        .bni-invite-hero__guest {
            margin: 0 0 16px;
            color: var(--bni-red);
            text-transform: uppercase;
            font-size: clamp(20px, 2.4vw, 34px);
            font-weight: 900;
            line-height: 1.15;
        }

        .bni-invite-hero__guest small {
            display: block;
            margin-top: 8px;
            color: #353539;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: .04em;
        }

        .bni-invite-hero__event-prefix {
            margin: 8px 0 0;
            color: var(--bni-red);
            text-transform: uppercase;
            font-size: 23px;
            font-weight: 950;
            letter-spacing: .16em;
        }

        .bni-invite-hero__title {
            max-width: 720px;
            margin: 0;
            color: #ca0e1f;
            text-transform: uppercase;
            font-size: clamp(44px, 5.6vw, 83px);
            font-style: italic;
            font-weight: 1000;
            line-height: .88;
            letter-spacing: -.055em;
            text-shadow:
                0 3px 0 #fff,
                0 7px 0 rgba(128, 0, 10, .11);
            transform: skewX(-5deg);
        }

        .bni-invite-hero__subtitle {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-top: 16px;
            color: var(--bni-red);
            text-transform: uppercase;
            font-size: 23px;
            font-weight: 950;
            font-style: italic;
            letter-spacing: .04em;
        }

        .bni-invite-hero__subtitle::before,
        .bni-invite-hero__subtitle::after {
            content: "";
            width: 58px;
            height: 2px;
            background: var(--bni-red);
        }

        .bni-invite-date {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
            margin-top: 35px;
        }

        .bni-invite-date__part {
            min-width: 120px;
            padding: 0 22px;
            text-align: center;
            border-right: 2px solid var(--bni-red);
        }

        .bni-invite-date__part:first-child {
            padding-left: 0;
        }

        .bni-invite-date__part:last-child {
            border-right: 0;
        }

        .bni-invite-date strong {
            display: block;
            color: var(--bni-red);
            font-size: 29px;
            font-weight: 950;
            line-height: 1;
        }

        .bni-invite-date span {
            display: block;
            margin-bottom: 7px;
            color: #4e4e51;
            font-size: 13px;
            font-weight: 800;
        }

        .bni-invite-hero__actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 16px;
            margin-top: 35px;
        }

        /* =========================
           FLOATING FACTS
        ========================== */
        .bni-invite-facts {
            position: relative;
            z-index: 3;
            margin-top: -82px;
        }

        .bni-invite-facts__grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
        }

        .bni-invite-fact {
            min-height: 205px;
            padding: 27px 22px 23px;
            text-align: center;
            border: 1px solid rgba(207, 32, 47, .09);
            border-radius: 22px;
            background: rgba(255, 255, 255, .96);
            box-shadow: var(--bni-shadow);
        }

        .bni-invite-fact__icon {
            width: 66px;
            height: 66px;
            display: grid;
            place-items: center;
            margin: 0 auto 16px;
            border-radius: 999px;
            background: linear-gradient(145deg, #db1a2c, #a5000e);
            color: #fff;
            box-shadow: 0 12px 24px rgba(207, 32, 47, .18);
        }

        .bni-invite-fact__icon svg {
            width: 31px;
            height: 31px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.9;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .bni-invite-fact dt {
            margin: 0 0 8px;
            color: var(--bni-red);
            text-transform: uppercase;
            font-size: 16px;
            font-weight: 950;
        }

        .bni-invite-fact dd,
        .bni-invite-fact small,
        .bni-invite-fact a {
            margin: 0;
            color: #4f4f53;
            font-size: 14px;
            line-height: 1.55;
        }

        .bni-invite-fact a {
            color: var(--bni-red);
            font-weight: 800;
            text-decoration: none;
        }

        /* =========================
           COMMON SECTION
        ========================== */
        .bni-invite-section {
            position: relative;
            padding: 74px 0;
        }

        .bni-invite-section--tight {
            padding-top: 44px;
        }

        .bni-invite-heading {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            margin-bottom: 35px;
            text-align: center;
        }

        .bni-invite-heading::before,
        .bni-invite-heading::after {
            content: "";
            width: min(130px, 16vw);
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--bni-red));
        }

        .bni-invite-heading::after {
            background: linear-gradient(90deg, var(--bni-red), transparent);
        }

        .bni-invite-heading h2 {
            margin: 0;
            color: var(--bni-red);
            text-transform: uppercase;
            font-size: clamp(25px, 3vw, 38px);
            font-weight: 950;
            line-height: 1;
        }

        /* =========================
           INTRO
        ========================== */
        .bni-invite-intro__card {
            display: grid;
            grid-template-columns: 1fr 1.08fr;
            overflow: hidden;
            border: 1px solid rgba(207, 32, 47, .12);
            border-radius: 24px;
            background: rgba(255, 255, 255, .96);
            box-shadow: var(--bni-shadow);
        }

        .bni-invite-intro__copy {
            padding: 45px 44px;
        }

        .bni-invite-intro__copy h2 {
            position: relative;
            margin: 0 0 26px;
            color: var(--bni-red);
            text-transform: uppercase;
            font-size: clamp(18px, 1.8vw, 24px);
            font-weight: 950;
            line-height: 1.25;
        }

        .bni-invite-intro__copy h2::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: -13px;
            width: 90px;
            height: 3px;
            border-radius: 999px;
            background: var(--bni-red);
        }

        .bni-invite-rich-copy,
        .bni-invite-intro__copy p {
            color: #4f4f53;
            font-size: 15px;
            line-height: 1.9;
        }

        .bni-invite-rich-copy > *:first-child {
            margin-top: 0;
        }

        .bni-invite-rich-copy > *:last-child {
            margin-bottom: 0;
        }

        .bni-invite-intro__visual {
            position: relative;
            min-height: 400px;
            overflow: hidden;
        }

        .bni-invite-intro__visual img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .bni-invite-intro__visual::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(255,255,255,.12), transparent 28%);
            pointer-events: none;
        }

        /* =========================
           SCHEDULE
        ========================== */
        .bni-invite-schedule {
            background:
                radial-gradient(circle at 50% 20%, rgba(207, 32, 47, .05), transparent 31rem);
        }

        .bni-invite-schedule__grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 38px;
            align-items: start;
        }

        .bni-invite-day {
            position: relative;
            padding: 42px 32px 26px;
            border: 1px solid rgba(207, 32, 47, .22);
            border-radius: 20px;
            background: rgba(255, 255, 255, .94);
            box-shadow: 0 14px 34px rgba(93, 14, 20, .06);
        }

        .bni-invite-day__title {
            position: absolute;
            top: -20px;
            left: 50%;
            min-width: 72%;
            transform: translateX(-50%);
            padding: 13px 22px;
            border-radius: 8px;
            background: linear-gradient(180deg, #da1728, #b70d19);
            color: #fff;
            text-align: center;
            text-transform: uppercase;
            font-size: 15px;
            font-weight: 950;
            box-shadow: 0 8px 18px rgba(207, 32, 47, .18);
        }

        .bni-invite-timeline {
            position: relative;
            display: grid;
            gap: 0;
            margin-top: 8px;
        }

        .bni-invite-timeline::before {
            content: "";
            position: absolute;
            top: 18px;
            bottom: 18px;
            left: 7px;
            width: 2px;
            background: var(--bni-red);
        }

        .bni-invite-timeline__item {
            position: relative;
            display: grid;
            grid-template-columns: 17px 75px 1fr;
            gap: 15px;
            padding: 13px 0;
        }

        .bni-invite-timeline__dot {
            position: relative;
            z-index: 2;
            width: 14px;
            height: 14px;
            margin-top: 4px;
            border: 3px solid #fff;
            border-radius: 999px;
            background: var(--bni-red);
            box-shadow: 0 0 0 2px var(--bni-red);
        }

        .bni-invite-timeline time {
            color: var(--bni-red);
            font-size: 15px;
            font-weight: 950;
        }

        .bni-invite-timeline h4 {
            margin: 0;
            color: #454549;
            font-size: 14px;
            font-weight: 750;
            line-height: 1.35;
        }

        .bni-invite-timeline p {
            margin: 5px 0 0;
            color: #858589;
            font-size: 12px;
            line-height: 1.5;
        }

        .bni-invite-empty {
            grid-column: 1 / -1;
            margin: 0;
            padding: 35px;
            border: 1px dashed rgba(207, 32, 47, .25);
            border-radius: 18px;
            color: #666;
            text-align: center;
            background: #fff;
        }

        /* =========================
           CHAPTERS
        ========================== */
        .bni-invite-chapters__card {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            padding: 26px 28px;
            border: 1px solid rgba(207, 32, 47, .17);
            border-radius: 22px;
            background: rgba(255, 255, 255, .95);
            box-shadow: var(--bni-shadow);
        }

        .bni-invite-chapter {
            min-width: 0;
            padding: 15px 14px;
            text-align: center;
            border-radius: 16px;
            background: linear-gradient(180deg, #fff, #fffbf9);
        }

        .bni-invite-chapter img {
            width: 100%;
            height: 100px;
            object-fit: contain;
            display: block;
        }

        .bni-invite-chapter strong {
            display: block;
            margin-top: 7px;
            color: #4f4f53;
            font-size: 13px;
            font-weight: 850;
        }

        /* =========================
           DRESS CODE
        ========================== */
        .bni-invite-dresscode {
            display: flex;
            justify-content: center;
            gap: clamp(32px, 7vw, 95px);
        }

        .bni-invite-dresscode__item {
            text-align: center;
        }

        .bni-invite-dresscode__circle {
            width: 128px;
            height: 128px;
            display: grid;
            place-items: center;
            border: 4px solid #fff;
            border-radius: 999px;
            box-shadow: 0 9px 26px rgba(0,0,0,.11);
        }

        .bni-invite-dresscode__vest-icon {
            display: block;
            width: 72px;
            height: 72px;
            background: currentColor;
            -webkit-mask: url('/images/bni/dress-code-vest-line.webp') center / contain no-repeat;
            mask: url('/images/bni/dress-code-vest-line.webp') center / contain no-repeat;
        }

        .bni-invite-dresscode__circle--red {
            color: #fff;
            background: var(--bni-red);
        }

        .bni-invite-dresscode__circle--white {
            color: #8e8e91;
            background: #fff;
        }

        .bni-invite-dresscode__circle--black {
            color: #fff;
            background: #242426;
        }

        .bni-invite-dresscode strong {
            display: block;
            margin-top: 13px;
            text-transform: uppercase;
            font-size: 20px;
            font-weight: 950;
        }

        /* =========================
           RSVP
        ========================== */
        .bni-invite-rsvp-description {
            width: min(720px, 100%);
            margin: -8px auto 24px;
            color: #57575a;
            font-size: 14px;
            line-height: 1.75;
            text-align: center;
        }

        .bni-invite-rsvp-form {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
            padding: 28px;
            border: 1px solid rgba(207, 32, 47, .1);
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 14px 35px rgba(93, 14, 20, .06);
        }

        .bni-invite-rsvp-form fieldset,
        .bni-invite-rsvp-form .bni-field--full,
        .bni-invite-rsvp-form .bni-form-status {
            grid-column: 1 / -1;
        }

        .bni-invite-rsvp-form fieldset {
            margin: 0;
            padding: 0;
            border: 0;
        }

        .bni-invite-rsvp-form legend,
        .bni-invite-rsvp-form label {
            display: block;
            margin-bottom: 7px;
            color: #353539;
            font-size: 13px;
            font-weight: 850;
        }

        .bni-invite-rsvp-form input[type="text"],
        .bni-invite-rsvp-form input[type="tel"],
        .bni-invite-rsvp-form input[type="email"],
        .bni-invite-rsvp-form input[type="number"],
        .bni-invite-rsvp-form textarea {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid #e3d7d7;
            border-radius: 10px;
            outline: none;
            background: #fff;
            font: inherit;
        }

        .bni-invite-rsvp-form input:focus,
        .bni-invite-rsvp-form textarea:focus {
            border-color: var(--bni-red);
            box-shadow: 0 0 0 3px rgba(207, 32, 47, .09);
        }

        .bni-invite-rsvp-options {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .bni-invite-rsvp-options label {
            margin: 0;
            padding: 11px 14px;
            border: 1px solid #eadcdc;
            border-radius: 10px;
            background: #fffafa;
            cursor: pointer;
        }

        .bni-invite-rsvp-options input {
            margin-right: 7px;
            accent-color: var(--bni-red);
        }

        .bni-form-error {
            margin: 6px 0 0;
            color: #b00012;
            font-size: 12px;
            font-weight: 700;
        }

        /* =========================
           CONTACTS
        ========================== */
        .bni-invite-contact-strip {
            margin-top: 24px;
            padding: 22px 24px;
            border: 1px solid rgba(207,32,47,.09);
            border-radius: 16px;
            background: rgba(255,255,255,.87);
        }

        .bni-invite-contact-strip__people {
            display: flex;
            flex-wrap: wrap;
            gap: 12px 25px;
            justify-content: center;
        }

        .bni-invite-contact-strip a,
        .bni-invite-contact-strip span {
            color: #5a5a5e;
            text-decoration: none;
            font-size: 13px;
        }

        .bni-invite-contact-strip strong {
            color: var(--bni-red);
        }

        /* =========================
           BOTTOM CTA
        ========================== */
        .bni-invite-final {
            position: relative;
            overflow: hidden;
            padding: 41px 20px 47px;
            text-align: center;
            color: #fff;
            background:
                radial-gradient(ellipse at center bottom, rgba(255, 76, 76, .9), transparent 45%),
                linear-gradient(180deg, #9a020b, #c20b18 62%, #7e0008);
        }

        .bni-invite-final::before,
        .bni-invite-final::after {
            content: "";
            position: absolute;
            left: -10%;
            right: -10%;
            bottom: 9px;
            height: 2px;
            transform: rotate(-3deg);
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.9), transparent);
            box-shadow:
                0 -10px 0 rgba(255, 104, 104, .45),
                0 10px 0 rgba(255, 104, 104, .3),
                0 20px 0 rgba(255, 104, 104, .16);
        }

        .bni-invite-final::after {
            transform: rotate(3deg);
        }

        .bni-invite-final__inner {
            position: relative;
            z-index: 2;
        }

        .bni-invite-final h2 {
            margin: 0 0 19px;
            text-transform: uppercase;
            font-size: clamp(26px, 3.4vw, 42px);
            font-weight: 950;
        }

        .bni-invite-final .bni-invite-button {
            min-width: 250px;
            background: #fff;
            color: var(--bni-red);
            border-color: #fff;
            box-shadow: 0 12px 30px rgba(0,0,0,.13);
        }

        .bni-invite-final .bni-invite-button:hover {
            background: #fff7f7;
            color: var(--bni-red);
        }

        /* =========================
           RESPONSIVE
        ========================== */
        @media (max-width: 1050px) {
            .bni-invite-topbar__inner {
                grid-template-columns: auto 1fr auto;
            }

            .bni-invite-nav {
                gap: 16px;
            }

            .bni-invite-nav a {
                font-size: 11px;
            }

            .bni-invite-hero__content {
                width: 66%;
            }

            .bni-invite-facts__grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .bni-invite-chapters__card {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 820px) {
            .bni-invite-topbar {
                position: relative;
            }

            .bni-invite-topbar__inner {
                min-height: 68px;
                grid-template-columns: 1fr auto;
            }

            .bni-invite-nav {
                display: none;
            }

            .bni-invite-brand__logo {
                width: 72px;
            }

            .bni-invite-topbar .bni-invite-button {
                min-height: 42px;
                padding: 0 15px;
                font-size: 11px;
            }

            .bni-invite-hero {
                min-height: 660px;
                align-items: flex-start;
            }

            .bni-invite-hero::before {
                background:
                    linear-gradient(180deg, rgba(255,255,255,.98) 0%, rgba(255,255,255,.88) 48%, rgba(255,255,255,.30) 74%, rgba(255,255,255,.04) 100%);
            }

            .bni-invite-hero__bg {
                object-position: 66% center;
            }

            .bni-invite-hero__content {
                width: 100%;
                padding: 58px 0 180px;
            }

            .bni-invite-hero__title {
                max-width: 590px;
            }

            .bni-invite-facts {
                margin-top: -92px;
            }

            .bni-invite-intro__card {
                grid-template-columns: 1fr;
            }

            .bni-invite-intro__visual {
                min-height: 310px;
                order: -1;
            }

            .bni-invite-schedule__grid {
                grid-template-columns: 1fr;
                gap: 55px;
            }

            .bni-invite-dresscode__circle {
                width: 110px;
                height: 110px;
            }
        }

        @media (max-width: 620px) {
            .bni-invite-shell,
            .bni-invite-narrow {
                width: min(100% - 28px, 1180px);
            }

            .bni-invite-hero {
                min-height: 620px;
            }

            .bni-invite-hero__content {
                padding-top: 45px;
            }

            .bni-invite-hero__event-prefix {
                font-size: 17px;
            }

            .bni-invite-hero__subtitle {
                font-size: 17px;
            }

            .bni-invite-hero__subtitle::before,
            .bni-invite-hero__subtitle::after {
                width: 32px;
            }

            .bni-invite-date {
                margin-top: 28px;
            }

            .bni-invite-date__part {
                min-width: 0;
                flex: 1 1 0;
                padding: 0 12px;
            }

            .bni-invite-date strong {
                font-size: 23px;
            }

            .bni-invite-date span {
                font-size: 11px;
            }

            .bni-invite-hero__actions {
                gap: 10px;
            }

            .bni-invite-button {
                min-height: 45px;
                padding-inline: 16px;
                font-size: 11px;
            }

            .bni-invite-facts {
                margin-top: -70px;
            }

            .bni-invite-facts__grid,
            .bni-invite-chapters__card {
                grid-template-columns: 1fr;
            }

            .bni-invite-fact {
                min-height: auto;
                padding-block: 22px;
            }

            .bni-invite-section {
                padding: 58px 0;
            }

            .bni-invite-intro__copy {
                padding: 31px 24px;
            }

            .bni-invite-intro__visual {
                min-height: 240px;
            }

            .bni-invite-heading {
                gap: 7px;
            }

            .bni-invite-heading::before,
            .bni-invite-heading::after {
                width: 28px;
            }

            .bni-invite-day {
                padding-inline: 22px;
            }

            .bni-invite-timeline__item {
                grid-template-columns: 17px 62px 1fr;
                gap: 10px;
            }

            .bni-invite-dresscode {
                gap: 14px;
            }

            .bni-invite-dresscode__circle {
                width: 88px;
                height: 88px;
            }

            .bni-invite-dresscode__vest-icon {
                width: 49px;
                height: 49px;
            }

            .bni-invite-dresscode strong {
                font-size: 15px;
            }

            .bni-invite-rsvp-form {
                grid-template-columns: 1fr;
                padding: 20px;
            }

            .bni-invite-rsvp-form fieldset,
            .bni-invite-rsvp-form .bni-field--full,
            .bni-invite-rsvp-form .bni-form-status {
                grid-column: auto;
            }
        }
    </style>

    <div class="bni-invite-page">
        {{-- Nếu layouts.plan đã render header riêng và anh không muốn 2 header,
             chỉ cần xoá block <header> này. --}}
        <header class="bni-invite-topbar">
            <div class="bni-invite-shell bni-invite-topbar__inner">
                <a class="bni-invite-brand" href="{{ url('/') }}" aria-label="BNI">
                    <img class="bni-invite-brand__logo" src="{{ asset('bni-logo-red.svg') }}" alt="BNI">
                </a>

                <nav class="bni-invite-nav" aria-label="Điều hướng thư mời">
                    <a href="#trang-chu">Trang chủ</a>
                    <a href="#gioi-thieu">Giới thiệu</a>
                    <a href="#chuong-trinh">Chương trình</a>
                    <a href="#thanh-phan">Thành phần</a>
                    <a href="#lien-he">Liên hệ</a>
                </nav>

                <a class="bni-invite-button" href="#rsvp">Xác nhận tham dự</a>
            </div>
        </header>

        <main>
            {{-- HERO --}}
            <section class="bni-invite-hero" id="trang-chu" aria-labelledby="bni-invite-title">
                <img
                    class="bni-invite-hero__bg"
                    src="{{ $heroBackground }}"
                    alt=""
                    aria-hidden="true"
                >

                <div class="bni-invite-shell">
                    <div class="bni-invite-hero__content">
                        <p class="bni-invite-hero__label">{{ $heroLabel }}</p>
                        <p class="bni-invite-hero__greeting">{{ $greeting }}</p>

                        <p class="bni-invite-hero__guest">
                            {{ $displayGuestName }}
                            @if ($chapter)
                                <small>{{ $chapter->name }}</small>
                            @endif
                        </p>

                        <p class="bni-invite-hero__event-prefix">Tới tham dự chương trình</p>

                        <p>
                            <img class="bni-invite-hero__event-logo" src="{{ asset('images/bni/le-chuyen-giao.png') }}" alt="Lễ chuyển giao">
                        </p>

                        <div class="bni-invite-date" aria-label="Thời gian diễn ra">
                            <div class="bni-invite-date__part">
                                <span>Thời gian</span>
                                <strong>{{ $eventTime }}</strong>
                            </div>
                            <div class="bni-invite-date__part">
                                <span>{{ ucfirst($eventWeekday) }}</span>
                                <strong>{{ $eventDate }}</strong>
                            </div>
                            <div class="bni-invite-date__part">
                                <span>Năm</span>
                                <strong>{{ $eventYear }}</strong>
                            </div>
                        </div>

                        <div class="bni-invite-hero__actions">
                            <a class="bni-invite-button" href="#rsvp">Xác nhận tham dự</a>
                            <a class="bni-invite-button bni-invite-button--ghost" href="#chuong-trinh">
                                Xem nội dung
                                <span aria-hidden="true">→</span>
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            {{-- 4 THÔNG TIN NHANH --}}
            <section class="bni-invite-facts" aria-label="Thông tin sự kiện">
                <div class="bni-invite-shell">
                    <dl class="bni-invite-facts__grid">
                        <div class="bni-invite-fact">
                            <div class="bni-invite-fact__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="8"></circle>
                                    <path d="M12 7v5l3 2"></path>
                                </svg>
                            </div>
                            <dt>Thời gian</dt>
                            <dd>
                                {{ $eventTime }}, {{ ucfirst($eventWeekday) }}<br>
                                <strong>{{ $startAt?->format('d/m/Y') ?: 'Đang cập nhật' }}</strong>
                            </dd>
                            @if ($endAt)
                                <small>Đến {{ $endAt->translatedFormat('H:i · d/m/Y') }}</small>
                            @endif
                        </div>

                        <div class="bni-invite-fact">
                            <div class="bni-invite-fact__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    <rect x="4" y="5" width="16" height="15" rx="2"></rect>
                                    <path d="M8 3v4M16 3v4M4 9h16"></path>
                                    <path d="M8 13h3M13 13h3M8 16h3"></path>
                                </svg>
                            </div>
                            <dt>Hình thức</dt>
                            <dd>{{ $eventLabel }}</dd>
                            @if ($event->venue)
                                <small>{{ $event->venue }}</small>
                            @endif
                        </div>

                        <div class="bni-invite-fact">
                            <div class="bni-invite-fact__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    <circle cx="9" cy="8" r="3"></circle>
                                    <circle cx="16" cy="9" r="2.5"></circle>
                                    <path d="M3.5 19c.6-3.4 2.7-5.2 5.5-5.2s4.9 1.8 5.5 5.2"></path>
                                    <path d="M14.5 14.8c2.8 0 4.6 1.4 5 4.2"></path>
                                </svg>
                            </div>
                            <dt>Đối tượng</dt>
                            <dd>{{ $invitationContent['audience'] ?? 'Quý doanh nghiệp, đối tác, khách mời' }}</dd>
                        </div>

                        <div class="bni-invite-fact">
                            <div class="bni-invite-fact__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    <path d="M7 4h3l1.2 4-2 1.4a15 15 0 0 0 5.4 5.4l1.4-2L20 14v3c0 1.1-.9 2-2 2C10.8 19 5 13.2 5 6c0-1.1.9-2 2-2z"></path>
                                </svg>
                            </div>
                            <dt>Liên hệ</dt>
                            <dd>
                                Hotline<br>
                                <a href="{{ $primaryPhoneUrl }}">{{ $primaryPhone }}</a>
                            </dd>
                        </div>
                    </dl>
                </div>
            </section>

            {{-- GIỚI THIỆU --}}
            <section class="bni-invite-section bni-invite-section--tight" id="gioi-thieu">
                <div class="bni-invite-narrow">
                    <div class="bni-invite-intro__card">
                        <div class="bni-invite-intro__copy">
                            <h2>{{ $invitationContent['content_title'] ?? 'Giới thiệu chương trình' }}</h2>

                            @if (filled($invitationContent['content'] ?? null))
                                <div class="bni-invite-rich-copy">
                                    {!! $invitationContent['content'] !!}
                                </div>
                            @else
                                <p>
                                    Lễ Chuyển giao Liên Chapter là dấu mốc quan trọng trong hành trình
                                    phát triển của cộng đồng BNI, ghi nhận những thành tựu đã đạt được,
                                    kết nối các thế hệ lãnh đạo và mở ra một nhiệm kỳ mới.
                                </p>
                                <p>
                                    Sự kiện là dịp để giao lưu, mở rộng quan hệ, gia tăng cơ hội hợp tác
                                    và cùng nhau kiến tạo những giá trị bền vững.
                                </p>
                            @endif
                        </div>

                        <div class="bni-invite-intro__visual">
                            <img src="{{ $heroBackground }}" alt="{{ $event->title }}">
                        </div>
                    </div>
                </div>
            </section>

            {{-- CHƯƠNG TRÌNH --}}
            <section class="bni-invite-section bni-invite-schedule" id="chuong-trinh">
                <div class="bni-invite-narrow">
                    <div class="bni-invite-heading">
                        <h2>{{ $invitationContent['schedule_title'] ?? 'Nội dung chương trình' }}</h2>
                    </div>

                    <div class="bni-invite-schedule__grid">
                        @forelse ($scheduleDays as $day)
                            <article class="bni-invite-day">
                                <h3 class="bni-invite-day__title">
                                    {{ $day['label'] ?? ('Ngày ' . ($day['number'] ?? $loop->iteration)) }}
                                </h3>

                                <div class="bni-invite-timeline">
                                    @foreach ($day['items'] as $item)
                                        <div class="bni-invite-timeline__item">
                                            <span class="bni-invite-timeline__dot" aria-hidden="true"></span>

                                            <time>
                                                {{ $item['time'] ?: '—' }}
                                            </time>

                                            <div>
                                                <h4>{{ $item['title'] }}</h4>
                                                @if (filled($item['description'] ?? null))
                                                    <p>{{ $item['description'] }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </article>
                        @empty
                            <p class="bni-invite-empty">
                                Lịch trình sự kiện sẽ được Ban tổ chức cập nhật.
                            </p>
                        @endforelse
                    </div>
                </div>
            </section>

            {{-- THÀNH PHẦN THAM DỰ / 4 CHAPTER --}}
            <section class="bni-invite-section bni-invite-section--tight" id="thanh-phan">
                <div class="bni-invite-narrow">
                    <div class="bni-invite-heading">
                        <h2>Thành phần tham dự</h2>
                    </div>

                    <div class="bni-invite-chapters__card">
                        @foreach ($chapterLogos as $chapterLogo)
                            <div class="bni-invite-chapter">
                                <img
                                    src="{{ $chapterLogo['image'] }}"
                                    alt="{{ $chapterLogo['name'] }}"
                                    loading="lazy"
                                >
                                <strong>{{ $chapterLogo['name'] }}</strong>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- TRANG PHỤC --}}
            <section class="bni-invite-section bni-invite-section--tight">
                <div class="bni-invite-narrow">
                    <div class="bni-invite-heading">
                        <h2>Trang phục</h2>
                    </div>

                    <div class="bni-invite-dresscode">
                        @foreach ([
                            ['name' => 'Đỏ', 'class' => 'red'],
                            ['name' => 'Trắng', 'class' => 'white'],
                            ['name' => 'Đen', 'class' => 'black'],
                        ] as $dress)
                            <div class="bni-invite-dresscode__item">
                                <div class="bni-invite-dresscode__circle bni-invite-dresscode__circle--{{ $dress['class'] }}">
                                    <span class="bni-invite-dresscode__vest-icon" aria-hidden="true"></span>
                                </div>
                                <strong>{{ $dress['name'] }}</strong>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- RSVP --}}
            <section class="bni-invite-section bni-invite-section--tight" id="rsvp">
                <div class="bni-invite-narrow">
                    <div class="bni-invite-heading">
                        <h2>{{ $invitationContent['rsvp_title'] ?? 'Xác nhận tham dự' }}</h2>
                    </div>

                    <p class="bni-invite-rsvp-description">
                        {{ $invitationContent['rsvp_description']
                            ?? 'Vui lòng xác nhận tham dự để Ban Tổ Chức chủ động sắp xếp và đón tiếp chu đáo.' }}
                    </p>

                    @if ($invitation)
                        <form
                                class="bni-invite-rsvp-form"
                                method="POST"
                                action="{{ LocalizedUrl::route('bni.invitations.rsvp', ['invitation' => $invitation]) }}"
                                data-bni-ajax-form
                            >
                                @csrf

                                <fieldset>
                                    <legend>Trạng thái tham dự</legend>

                                    <div class="bni-invite-rsvp-options">
                                        <label>
                                            <input
                                                type="radio"
                                                name="rsvp_status"
                                                value="attending"
                                                @checked(old('rsvp_status', $invitation->rsvp_status) === 'attending')
                                            >
                                            Tôi sẽ tham dự
                                        </label>

                                        <label>
                                            <input
                                                type="radio"
                                                name="rsvp_status"
                                                value="declined"
                                                @checked(old('rsvp_status', $invitation->rsvp_status) === 'declined')
                                            >
                                            Tôi chưa thể tham dự
                                        </label>
                                    </div>

                                    @error('rsvp_status')
                                        <p class="bni-form-error">{{ $message }}</p>
                                    @enderror
                                </fieldset>

                                <div>
                                    <label for="guest-count">Số người tham dự</label>
                                    <input
                                        id="guest-count"
                                        type="number"
                                        name="guest_count"
                                        min="1"
                                        max="10"
                                        value="{{ old('guest_count', $invitation->guest_count) }}"
                                        required
                                    >
                                    @error('guest_count')
                                        <p class="bni-form-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="rsvp-note">Lời nhắn với Ban tổ chức</label>
                                    <textarea
                                        id="rsvp-note"
                                        name="rsvp_note"
                                        rows="3"
                                    >{{ old('rsvp_note', $invitation->rsvp_note) }}</textarea>
                                    @error('rsvp_note')
                                        <p class="bni-form-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="bni-field--full">
                                    <button class="bni-invite-button" type="submit">
                                        Gửi phản hồi
                                    </button>
                                </div>
                                <p class="bni-form-status" data-bni-form-status role="status" aria-live="polite" hidden></p>
                        </form>
                    @else
                        <form
                                class="bni-invite-rsvp-form"
                                method="POST"
                                action="{{ LocalizedUrl::route('bni.invitations.template.rsvp') }}"
                                data-bni-ajax-form
                                data-reset-on-success="true"
                            >
                                @csrf

                                <div>
                                    <label for="template-full-name">Họ và tên</label>
                                    <input
                                        id="template-full-name"
                                        type="text"
                                        name="full_name"
                                        value="{{ old('full_name') }}"
                                        required
                                    >
                                    @error('full_name')
                                        <p class="bni-form-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="template-phone">Số điện thoại</label>
                                    <input
                                        id="template-phone"
                                        type="tel"
                                        name="phone"
                                        value="{{ old('phone') }}"
                                        required
                                    >
                                    @error('phone')
                                        <p class="bni-form-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="template-email">Email</label>
                                    <input
                                        id="template-email"
                                        type="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                    >
                                    @error('email')
                                        <p class="bni-form-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="template-note">Lời nhắn với Ban tổ chức</label>
                                    <textarea
                                        id="template-note"
                                        name="note"
                                        rows="3"
                                    >{{ old('note') }}</textarea>
                                    @error('note')
                                        <p class="bni-form-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="bni-field--full">
                                    <button class="bni-invite-button" type="submit">
                                        Gửi xác nhận
                                    </button>
                                </div>
                                <p class="bni-form-status" data-bni-form-status role="status" aria-live="polite" hidden></p>
                        </form>
                    @endif

                    <div class="bni-invite-contact-strip" id="lien-he">
                        @if ($contacts->isNotEmpty())
                            <div class="bni-invite-contact-strip__people">
                                @foreach ($contacts as $contact)
                                    <span>
                                        @if (filled(data_get($contact, 'name')))
                                            <strong>{{ data_get($contact, 'name') }}</strong>
                                        @endif

                                        @if (filled(data_get($contact, 'position')))
                                            · {{ data_get($contact, 'position') }}
                                        @endif

                                        @if (filled(data_get($contact, 'phone_url')))
                                            · <a href="{{ data_get($contact, 'phone_url') }}">{{ data_get($contact, 'phone') }}</a>
                                        @elseif (filled(data_get($contact, 'phone')))
                                            · {{ data_get($contact, 'phone') }}
                                        @endif

                                        @if (filled(data_get($contact, 'email_url')))
                                            · <a href="{{ data_get($contact, 'email_url') }}">{{ data_get($contact, 'email') }}</a>
                                        @endif

                                        @if (filled(data_get($contact, 'zalo_url')))
                                            · <a href="{{ data_get($contact, 'zalo_url') }}" target="_blank" rel="noopener">Zalo</a>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        @elseif ($isInvitationTemplate)
                            <div class="bni-invite-contact-strip__people">
                                <span>Đầu mối liên hệ sẽ hiển thị theo từng chapter khi phát hành thư mời.</span>
                            </div>
                        @endif

                        @if ($location)
                            <div class="bni-invite-contact-strip__people" style="margin-top: 10px;">
                                <span>
                                    {{ $location }}
                                    @if ($directionsUrl)
                                        · <a href="{{ $directionsUrl }}" target="_blank" rel="noopener noreferrer">
                                            Chỉ đường ↗
                                        </a>
                                    @endif
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </section>

            {{-- FINAL CTA --}}
            <section class="bni-invite-final">
                <div class="bni-invite-final__inner">
                    <h2>Hân hạnh đón tiếp quý đối tác</h2>
                    <a class="bni-invite-button" href="#rsvp">
                        Xác nhận ngay
                        <span aria-hidden="true">→</span>
                    </a>
                </div>
            </section>
        </main>
    </div>
@endsection
