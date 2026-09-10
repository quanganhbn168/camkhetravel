@extends('layouts.plan')

@use(App\Support\Localization\LocalizedUrl)

@section('body_class', 'bni-experience-page bni-plan-page bni-invitation-redesign')
@section('main_id', 'bni-invitation-main')
@section('main_class', 'bni-invitation-main')

@section('content')
    @php
        $contacts = collect($invitationContent['contacts'] ?? []);
        $primaryContact = $contacts->first();
        $location = collect([$event->venue, $event->address])->filter()->implode(', ');

        $startAt = $event->starts_at;
        $endAt = $event->ends_at;

        $eventTitle = filled($event->title) ? $event->title : ($invitationContent['event_label'] ?? 'Sự kiện BNI');
        $eventTime = $startAt?->format('H:i');
        $eventDate = $startAt?->format('d.m');
        $eventYear = $startAt?->format('Y');
        $eventWeekday = $startAt?->translatedFormat('l');
        $eventContent = filled($event->content) ? $event->content : ($invitationContent['content'] ?? null);
        $heroLabel = $invitationContent['label'] ?? 'Thư mời';
        $greeting = $invitationContent['greeting'] ?? 'Trân trọng kính mời';
        $eventPrefix = $invitationContent['event_prefix'] ?? 'Tới tham dự chương trình chào mừng';
        $displayGuestName = filled($guestName ?? null)
            ? $guestName
            : ($invitationContent['default_guest_name'] ?? 'Anh/Chị chủ doanh nghiệp');
        $eventTypeLabel = match ($event->type) {
            'handover' => 'Lễ chuyển giao',
            'pickleball' => 'Pickleball',
            default => null,
        };
        $eventTypeImage = $event->type === 'handover'
            ? asset('images/bni/le-chuyen-giao.png')
            : null;
        $eventTitleDetail = $eventTitle;

        if (filled($eventTypeLabel)) {
            $strippedTitle = preg_replace('/^\s*' . preg_quote($eventTypeLabel, '/') . '\s*/iu', '', $eventTitle);
            $eventTitleDetail = filled($strippedTitle) ? $strippedTitle : $eventTitle;
        }

        $heroBackground = $heroImageUrl ?: asset('images/bni/background-thumoi.jpg');
        $heroLocation = collect([$event->venue, $event->address])->filter()->first();

        $primaryPhone = data_get($primaryContact, 'phone');
        $primaryPhoneUrl = data_get($primaryContact, 'phone_url')
            ?: (filled($primaryPhone) ? 'tel:' . preg_replace('/\s+/', '', $primaryPhone) : null);

    @endphp

    <style>
        @font-face {
            font-family: 'UTM Edwardian';
            src: url('{{ asset('fonts/UTM Edwardian/UTM EdwardianB.ttf') }}') format('truetype');
            font-style: normal;
            font-weight: 400;
            font-display: swap;
        }

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
            display: none;
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
            min-height: min(100svh, 900px);
            display: flex;
            align-items: center;
            isolation: isolate;
            overflow: hidden;
            background: #c90013;
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
                linear-gradient(180deg, rgba(72, 0, 8, .2), rgba(160, 0, 14, .02) 42%, rgba(58, 0, 8, .18)),
                linear-gradient(90deg, rgba(72, 0, 8, .08), transparent 50%, rgba(72, 0, 8, .1));
        }

        .bni-invite-hero::after {
            content: "";
            position: absolute;
            inset: auto 0 0;
            z-index: -1;
            height: 130px;
            background: linear-gradient(180deg, transparent, rgba(93, 0, 10, .2));
        }

        .bni-invite-hero__content {
            width: min(780px, 100%);
            margin-inline: 0;
            padding: 34px 0 92px;
            text-align: left;
        }

        .bni-invite-hero__brand-strip {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: clamp(14px, 2.6vw, 38px);
            width: min(100%, 730px);
            margin: 0 auto 48px;
            color: #fff;
        }

        .bni-invite-hero__brand-main {
            display: block;
            flex: 0 0 auto;
            width: clamp(108px, 17vw, 170px);
            height: auto;
        }

        .bni-invite-hero__chapter-brand {
            display: grid;
            justify-items: center;
            gap: 3px;
            min-width: 44px;
            color: #fff;
            text-align: center;
            text-transform: uppercase;
        }

        .bni-invite-hero__chapter-brand strong {
            font-size: clamp(16px, 2vw, 24px);
            font-weight: 950;
            line-height: .82;
            letter-spacing: -.08em;
        }

        .bni-invite-hero__chapter-brand small {
            max-width: 76px;
            font-size: clamp(7px, .85vw, 10px);
            font-weight: 900;
            line-height: 1;
            letter-spacing: .02em;
        }

        .bni-invite-hero__label {
            width: fit-content;
            max-width: 100%;
            margin: 0;
            color: #fff;
            font-family: 'UTM Edwardian', cursive;
            font-size: clamp(46px, 5.5vw, 82px);
            font-weight: 400;
            line-height: .8;
            letter-spacing: 0;
            text-align: center;
        }

        .bni-invite-hero__greeting {
            margin: 9px 0 13px;
            color: #fff;
            font-family: var(--site-font-display);
            font-size: clamp(20px, 2vw, 31px);
            font-style: italic;
            line-height: 1;
        }

        .bni-invite-hero__guest {
            margin: 0 0 16px;
            color: #fff;
            text-transform: uppercase;
            font-size: clamp(20px, 2.4vw, 34px);
            font-weight: 900;
            line-height: 1.15;
        }

        .bni-invite-hero__guest small {
            display: block;
            margin-top: 8px;
            color: rgba(255, 255, 255, .82);
            font-size: 14px;
            font-weight: 700;
            letter-spacing: .04em;
        }

        .bni-invite-hero__event-prefix {
            margin: 8px 0 18px;
            color: #fff;
            text-transform: uppercase;
            font-size: clamp(17px, 2.1vw, 27px);
            font-weight: 950;
            letter-spacing: .04em;
        }

        .bni-invite-hero__event-type {
            margin: 18px 0 0;
            color: #fff;
            font-size: clamp(34px, 4.8vw, 64px);
            font-weight: 400;
            line-height: .82;
        }

        .bni-invite-hero__event-image {
            display: block;
            width: min(650px, 100%);
            height: auto;
            margin: 0;
        }

        .bni-invite-hero__event-title {
            max-width: 100%;
            margin: 14px 0 0;
            color: #fff;
            text-transform: uppercase;
            font-size: clamp(30px, 4.2vw, 58px);
            font-style: italic;
            font-weight: 1000;
            line-height: .95;
            letter-spacing: -.045em;
            text-shadow: 0 3px 0 rgba(128, 0, 10, .35), 0 7px 0 rgba(128, 0, 10, .18);
            transform: skewX(-5deg);
        }

        .bni-invite-hero__event-kicker {
            margin: 14px 0 0;
            color: #fff;
            text-transform: uppercase;
            font-size: 13px;
            font-weight: 900;
            letter-spacing: .14em;
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
            color: #fff;
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
            background: #fff;
        }

        .bni-invite-date {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 0;
            margin-top: 28px;
        }

        .bni-invite-date__part {
            min-width: 120px;
            padding: 0 22px;
            text-align: center;
            border-right: 2px solid rgba(255, 255, 255, .92);
        }

        .bni-invite-date__part:first-child {
            padding-left: 0;
        }

        .bni-invite-date__part:last-child {
            border-right: 0;
        }

        .bni-invite-date strong {
            display: block;
            color: #fff;
            font-size: clamp(29px, 3.2vw, 46px);
            font-weight: 950;
            line-height: 1;
        }

        .bni-invite-date span {
            display: block;
            margin-bottom: 7px;
            color: rgba(255, 255, 255, .92);
            font-size: 15px;
            font-weight: 800;
        }

        .bni-invite-date__label--hidden {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        .bni-invite-hero__actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 16px;
            margin-top: 35px;
            display: none;
        }

        .bni-invite-hero__location {
            display: inline-flex;
            align-items: center;
            justify-content: flex-start;
            gap: 9px;
            margin: 20px 0 0;
            color: #fff;
            font-size: clamp(17px, 2.1vw, 27px);
            font-weight: 900;
            letter-spacing: .01em;
            text-transform: uppercase;
        }

        .bni-invite-hero__location svg {
            width: 22px;
            height: 22px;
            fill: currentColor;
        }

        .bni-invite-hero__event-title--handover,
        .bni-invite-hero__event-kicker--handover {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
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

        .bni-invite-chapter__placeholder {
            min-height: 100px;
            display: grid;
            place-items: center;
            align-content: center;
            gap: 5px;
            color: var(--bni-red);
            border: 1px dashed rgba(207, 32, 47, .3);
            border-radius: 12px;
        }

        .bni-invite-chapter__placeholder span {
            font-size: 22px;
            font-weight: 950;
        }

        .bni-invite-chapter__placeholder small {
            color: var(--bni-muted);
            font-size: 11px;
        }

        .bni-invite-chapter strong {
            display: block;
            margin-top: 7px;
            color: #4f4f53;
            font-size: 13px;
            font-weight: 850;
        }

        /* =========================
           ATTENDANCE NOTES
        ========================== */
        .bni-invite-note__card {
            padding: 30px 34px;
            border: 1px solid rgba(207, 32, 47, .14);
            border-radius: 22px;
            background: rgba(255, 255, 255, .96);
            box-shadow: var(--bni-shadow);
        }

        .bni-invite-note__card .bni-invite-heading {
            margin-bottom: 24px;
        }

        .bni-invite-note__card .bni-invite-rich-copy {
            max-width: 820px;
            margin-inline: auto;
        }

        .bni-invite-note__card .bni-invite-rich-copy > *:first-child {
            margin-top: 0;
        }

        .bni-invite-note__card .bni-invite-rich-copy > *:last-child {
            margin-bottom: 0;
        }

        /* =========================
           LEGACY DRESS CODE STYLES
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

        .bni-invite-rsvp-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(270px, .85fr);
            gap: 20px;
            align-items: stretch;
        }

        .bni-invite-rsvp-qr {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-width: 0;
            padding: 28px 24px;
            border: 1px solid rgba(207, 32, 47, .14);
            border-radius: 14px;
            background:
                radial-gradient(circle at 50% 0, rgba(207, 32, 47, .08), transparent 13rem),
                #fff8f5;
            text-align: center;
        }

        .bni-invite-rsvp-qr__eyebrow {
            margin: 0 0 8px;
            color: var(--bni-red);
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .bni-invite-rsvp-qr h3 {
            margin: 0 0 18px;
            color: #2f2f33;
            font-size: clamp(20px, 2.2vw, 27px);
            line-height: 1.2;
        }

        .bni-invite-rsvp-qr img {
            display: block;
            width: min(250px, 100%);
            aspect-ratio: 1;
            padding: 10px;
            border: 1px solid #eadcdc;
            border-radius: 12px;
            background: #fff;
            object-fit: contain;
        }

        .bni-invite-rsvp-qr__caption {
            max-width: 300px;
            margin: 17px 0 0;
            color: #57575a;
            font-size: 14px;
            line-height: 1.55;
        }

        .bni-invite-rsvp-qr__hint {
            margin: 8px 0 0;
            color: #858589;
            font-size: 12px;
            line-height: 1.5;
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
                    linear-gradient(180deg, rgba(72, 0, 8, .2), rgba(160, 0, 14, .02) 42%, rgba(58, 0, 8, .2));
            }

            .bni-invite-hero__bg {
                object-position: 60% center;
            }

            .bni-invite-hero__content {
                width: 100%;
                padding: 28px 0 120px;
            }

            .bni-invite-hero__brand-strip {
                gap: 10px;
                margin-bottom: 36px;
            }

            .bni-invite-hero__brand-main {
                width: 104px;
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

            .bni-invite-rsvp-layout {
                grid-template-columns: 1fr;
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
                min-height: max(760px, 100svh);
            }

            .bni-invite-hero__content {
                padding-top: 28px;
            }

            .bni-invite-hero__event-prefix {
                font-size: 16px;
            }

            .bni-invite-hero__event-type {
                font-size: 43px;
            }

            .bni-invite-hero__label {
                font-size: 60px;
            }

            .bni-invite-hero__greeting {
                font-size: 21px;
            }

            .bni-invite-hero__brand-strip {
                gap: 7px;
                margin-bottom: 32px;
            }

            .bni-invite-hero__brand-main {
                width: 96px;
            }

            .bni-invite-hero__chapter-brand {
                min-width: 39px;
            }

            .bni-invite-hero__chapter-brand strong {
                font-size: 17px;
            }

            .bni-invite-hero__chapter-brand small {
                max-width: 55px;
                font-size: 7px;
            }

            .bni-invite-hero__event-image {
                width: min(100%, 620px);
            }

            .bni-invite-hero__location {
                font-size: 15px;
            }

            .bni-invite-hero__location svg {
                width: 18px;
                height: 18px;
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
                        <div class="bni-invite-hero__brand-strip" aria-label="BNI và các chapter tham dự">
                            <img
                                class="bni-invite-hero__brand-main"
                                src="{{ asset('bni-logo.svg') }}"
                                alt="BNI Accelerator"
                            >

                            @foreach ($eventChapters as $eventChapter)
                                <span class="bni-invite-hero__chapter-brand">
                                    <strong aria-hidden="true">BNI</strong>
                                    <small>{{ $eventChapter['short_name'] ?: $eventChapter['name'] }}</small>
                                </span>
                            @endforeach
                        </div>

                        <p class="bni-invite-hero__label">{{ $heroLabel }}</p>
                        <p class="bni-invite-hero__greeting">{{ $greeting }}</p>

                        <p class="bni-invite-hero__guest">
                            {{ $displayGuestName }}
                            @if ($chapter)
                                <small>{{ $chapter->name }}</small>
                            @endif
                        </p>

                        <p class="bni-invite-hero__event-prefix">{{ $eventPrefix }}</p>
                        @if ($eventTypeImage)
                            <img class="bni-invite-hero__event-image" src="{{ $eventTypeImage }}" alt="{{ $eventTypeLabel }}">
                        @elseif (filled($eventTypeLabel))
                            <p class="bni-invite-hero__event-type">{{ $eventTypeLabel }}</p>
                        @endif
                        <h1 class="bni-invite-hero__event-title{{ $event->type === 'handover' ? ' bni-invite-hero__event-title--handover' : '' }}" id="bni-invite-title">{{ $eventTitleDetail }}</h1>
                        @if (filled($event->kicker))
                            <p class="bni-invite-hero__event-kicker{{ $event->type === 'handover' ? ' bni-invite-hero__event-kicker--handover' : '' }}">{{ $event->kicker }}</p>
                        @endif

                        <div class="bni-invite-date" aria-label="Thời gian diễn ra">
                            <div class="bni-invite-date__part">
                                <span class="bni-invite-date__label--hidden">Thời gian</span>
                                <strong>{{ $eventTime ?: '—' }}</strong>
                            </div>
                            <div class="bni-invite-date__part">
                                <span>{{ $eventWeekday ? ucfirst($eventWeekday) . ' ngày' : 'Ngày diễn ra' }}</span>
                                <strong>{{ $eventDate ?: '—' }}</strong>
                            </div>
                            <div class="bni-invite-date__part">
                                <span class="bni-invite-date__label--hidden">Năm</span>
                                <strong>{{ $eventYear ?: '—' }}</strong>
                            </div>
                        </div>

                        @if (filled($heroLocation))
                            <p class="bni-invite-hero__location">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M12 2a7 7 0 0 0-7 7c0 5.2 7 13 7 13s7-7.8 7-13a7 7 0 0 0-7-7Zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5Z"></path>
                                </svg>
                                <span>{{ $heroLocation }}</span>
                            </p>
                        @endif

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
                                {{ $eventTime ?: 'Đang cập nhật' }}{{ $eventWeekday ? ', ' . ucfirst($eventWeekday) : '' }}<br>
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
                            <dt>Địa chỉ</dt>
                            <dd>{{ $location ?: 'Đang cập nhật' }}</dd>
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
                                @if ($primaryPhoneUrl)
                                    Hotline<br>
                                    <a href="{{ $primaryPhoneUrl }}">{{ $primaryPhone }}</a>
                                @else
                                    Đang cập nhật
                                @endif
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

                            @if (filled($eventContent))
                                <div class="bni-invite-rich-copy">
                                    {!! $eventContent !!}
                                </div>
                            @else
                                <p>Nội dung chương trình sẽ được Ban tổ chức cập nhật.</p>
                            @endif
                        </div>

                        <div class="bni-invite-intro__visual">
                            <img src="{{ $heroBackground }}" alt="{{ $eventTitle }}">
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
                        @forelse ($eventChapters as $eventChapter)
                            <div class="bni-invite-chapter">
                                @if ($eventChapter['image_url'])
                                    <img
                                        src="{{ $eventChapter['image_url'] }}"
                                        alt="{{ $eventChapter['name'] }}"
                                        loading="lazy"
                                    >
                                @else
                                    <div class="bni-invite-chapter__placeholder" aria-hidden="true">
                                        <span>{{ $eventChapter['short_name'] }}</span>
                                        <small>Chưa gắn logo</small>
                                    </div>
                                @endif
                                <strong>{{ $eventChapter['name'] }}</strong>
                            </div>
                        @empty
                            <p class="bni-invite-empty">Chapter tham dự sẽ hiển thị sau khi được gắn vào sự kiện trong CMS BNI.</p>
                        @endforelse
                    </div>
                </div>
            </section>

            {{-- LƯU Ý THAM DỰ --}}
            @if (filled($invitationContent['note_content'] ?? null))
                <section class="bni-invite-section bni-invite-section--tight" id="luu-y" aria-labelledby="bni-invite-note-title">
                    <div class="bni-invite-narrow">
                        <div class="bni-invite-note__card">
                            <div class="bni-invite-heading">
                                <h2 id="bni-invite-note-title">{{ $invitationContent['note_title'] ?? 'Lưu ý tham dự' }}</h2>
                            </div>
                            <div class="bni-invite-rich-copy">
                                {!! $invitationContent['note_content'] !!}
                            </div>
                        </div>
                    </div>
                </section>
            @endif

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

                    <div class="bni-invite-rsvp-layout">
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

                        <aside class="bni-invite-rsvp-qr" aria-labelledby="bni-invite-rsvp-qr-title">
                            <p class="bni-invite-rsvp-qr__eyebrow">Đăng ký tham dự</p>
                            <h3 id="bni-invite-rsvp-qr-title">Quét mã QR để đăng ký</h3>
                            <img
                                src="{{ asset('images/bni/qr_dang_ky.jpg') }}"
                                alt="Mã QR đăng ký tham dự sự kiện BNI"
                                loading="lazy"
                            >
                            <p class="bni-invite-rsvp-qr__caption">
                                Dùng camera điện thoại để quét mã và gửi thông tin đăng ký nhanh.
                            </p>
                            <p class="bni-invite-rsvp-qr__hint">Hoặc điền form bên cạnh để xác nhận.</p>
                        </aside>
                    </div>

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
