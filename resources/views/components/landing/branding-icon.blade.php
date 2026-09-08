@props(['name' => 'arrow'])
<svg {{ $attributes->class(['branding-icon']) }} viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($name)
        @case('profile')
            <path d="M16 6C12 3 6 3 2 5v23c4-2 10-2 14 1 4-3 10-3 14-1V5c-4-2-10-2-14 1Z"/><path d="M16 6v23M5 2c4-1 8 0 11 2 3-2 7-3 11-2M6 17h6M6 20h6M6 23h6M20 9h6M20 13h6M20 17h6M20 21h6"/><circle cx="9" cy="9" r="2"/><path d="M6 14v-1a3 3 0 0 1 6 0v1Z"/>
            @break
        @case('website')
            <circle cx="15" cy="15" r="13"/><ellipse cx="15" cy="15" rx="6" ry="13"/><path d="M2 15h26M5 7c6 3 14 3 20 0M5 23c4-2 9-3 13-2M15 2v26"/><path d="m22 20 8 5-4 1-1 4-3-10Z" fill="currentColor" stroke="white" stroke-width=".8"/>
            @break
        @case('photo')
            <path d="M3 8h6l2-4h10l2 4h6a1 1 0 0 1 1 1v18a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9a1 1 0 0 1 1-1Z"/><circle cx="16" cy="18" r="7"/><path d="M25 11h2M5 5h3"/>
            @break
        @case('video')
            <path d="M3 12h26v17H3ZM3 12 1 6 27 1l2 6-26 5ZM7 5l-2 6M15 3l-2 6M23 2l-2 6M13 17l8 4-8 5Z"/>
            @break
        @case('building')
            <path d="M3 29V5l12-3v27M15 10l13-4v23M1 29h29M7 7v2m4-3v2M7 13v2m4-3v2M7 19v2m4-3v2M19 13v2m5-4v2M19 20v2m5-4v2M8 29v-4h3v4M20 29v-4h3v4"/>
            @break
        @case('team')
            <circle cx="16" cy="7" r="4"/><circle cx="5" cy="12" r="3"/><circle cx="27" cy="12" r="3"/><path d="M9 29V18a7 7 0 0 1 14 0v11ZM2 29V20a4 4 0 0 1 6-3M30 29V20a4 4 0 0 0-6-3M14 14l2 4 2-4M16 18v8"/>
            @break
        @case('badge')
            <path d="m16 2 4 3 5 1 1 5 3 5-3 4-1 5-5 1-4 3-4-3-5-1-1-5-3-4 3-5 1-5 5-1Z"/><circle cx="16" cy="15" r="7"/><path d="m12 15 3 3 5-6M8 25l-2 6 6-2m12-4 2 6-6-2"/>
            @break
        @case('box')
            <path d="m16 2 13 7v14l-13 7L3 23V9Zm0 28V16M3 9l13 7 13-7M9 6l13 7v7l-4 2v-7L6 8"/>
            @break
        @case('phone')
            <path d="m8 3 5 7-4 4c2 4 5 7 9 9l4-4 7 5-3 5C13 30 2 19 3 6Z"/>
            @break
        @case('email')
            <rect x="3" y="6" width="26" height="20" rx="3"/><path d="m4 8 12 10L28 8"/>
            @break
        @case('pin')
            <path d="M26 12c0 8-10 18-10 18S6 20 6 12a10 10 0 0 1 20 0Z"/><circle cx="16" cy="12" r="4"/>
            @break
        @case('play')
            <circle cx="16" cy="16" r="13"/><path d="m13 10 9 6-9 6Z"/>
            @break
        @default
            <path d="M4 16h24M21 9l7 7-7 7"/>
    @endswitch
</svg>
