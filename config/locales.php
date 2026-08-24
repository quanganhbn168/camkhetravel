<?php

return [
    /*
     * Bootstrap fallback only. Runtime languages are managed in the
     * `languages` table through Filament; do not edit this list to add a
     * language in a deployed environment.
     */
    'default' => 'vi',

    'supported' => [
        'vi' => ['label' => 'Tiếng Việt', 'native' => 'VI', 'og_locale' => 'vi_VN'],
        'en' => ['label' => 'English', 'native' => 'EN', 'og_locale' => 'en_US'],
        'zh' => ['label' => '中文', 'native' => '中文', 'og_locale' => 'zh_CN'],
        'ko' => ['label' => '한국어', 'native' => '한국어', 'og_locale' => 'ko_KR'],
    ],
];
