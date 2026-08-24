<?php

return [
    'connection' => 'wordpress',
    'source_url' => rtrim((string) env('WP_SOURCE_URL', 'https://thtmedia.com.vn'), '/'),
    'local_path' => env('WP_SOURCE_PATH', base_path('../thtmedia.com.vn')),
    'media_disk' => env('WP_MEDIA_DISK', 'public'),
    'media_directory' => trim((string) env('WP_MEDIA_DIRECTORY', 'media/wordpress'), '/'),
    'fallback_media_path' => trim((string) env(
        'WP_FALLBACK_MEDIA_PATH',
        '2023/01/1672729584.webp',
    ), '/'),
    'missing_media_replacements' => [
        '2026/01/THT-WEBSITE-1.png' => '2025/12/THT-WEBSITE-1.png',
        '2026/01/THT-WEBSITE-2.png' => '2025/12/THT-WEBSITE-2.png',
        '2026/01/z7370147893780_d31f96e124bf77f06380827b26c42025-1.jpg' => '2026/01/z7370147893780_d31f96e124bf77f06380827b26c42025.jpg',
        '2026/01/z7370147893780_d31f96e124bf77f06380827b26c42025-2.jpg' => '2026/01/z7370147893780_d31f96e124bf77f06380827b26c42025.jpg',
    ],
    'broken_external_media_replacements' => [
        'https://shophoatuoanh.com/wp-content/uploads/2021/07/hoa-cuoi-cam-tay-hoa-mau-don-scaled.jpg' => '2023/03/DSC04656-scaled.jpg',
        'https://scontent.fhan15-1.fna.fbcdn.net/v/t39.30808-6/339014793_247579711040333_4379554594348714642_n.jpg?_nc_cat=108&ccb=1-7&_nc_sid=8bfeb9&_nc_ohc=dUwoLQwIrDYAX-PF-My&_nc_ht=scontent.fhan15-1.fna&oh=00_AfBX74IFVpBGjGpqj8HboTKvdTobzc5e6ytNDDoDEHW8VA&oe=64630596' => '2023/05/QVM-313-scaled.jpg',
        'https://cleverads.vn/blog/wp-content/uploads/2022/12/digital-marrketing-2023-300x169.png' => '2023/05/Aesthetic-and-Minimalist-Modern-Cafe-and-Coffee-Shop-Facebook-Post-2-scaled.jpg',
        'https://cleverads.vn/blog/wp-content/uploads/2022/12/digital-marrketing-2023-1-300x169.png' => '2023/05/Aesthetic-and-Minimalist-Modern-Cafe-and-Coffee-Shop-Facebook-Post-2-scaled.jpg',
        'https://cleverads.vn/blog/wp-content/uploads/2022/12/digital-marrketing-2023-1024x577.png' => '2023/05/Aesthetic-and-Minimalist-Modern-Cafe-and-Coffee-Shop-Facebook-Post-2-scaled.jpg',
        'https://cleverads.vn/blog/wp-content/uploads/2022/12/digital-marrketing-2023-2-1024x577.png' => '2023/05/Aesthetic-and-Minimalist-Modern-Cafe-and-Coffee-Shop-Facebook-Post-2-scaled.jpg',
        'https://cleverads.vn/blog/wp-content/uploads/2022/12/digital-marrketing-2023-3-1024x577.png' => '2023/05/Aesthetic-and-Minimalist-Modern-Cafe-and-Coffee-Shop-Facebook-Post-2-scaled.jpg',
        'https://ttdn.vn/Uploads/Images/2023/11/4/3/bao-vat-quoc-gia-2.jpg' => '2023/11/z4893744766421_a337d3b7c6fd33e852153dcf618944d6-scaled.jpg',
        'https://ttdn.vn/Uploads/Images/2023/11/4/3/bao-vat-quoc-gia-3.jpg' => '2023/11/z4893744766421_a337d3b7c6fd33e852153dcf618944d6-scaled.jpg',
        'https://advertisingvietnam.com/cdn-cgi/image/width=1440,height=756,quality=90,fit=cover,format=auto/https://media-api.advertisingvietnam.com/oapi/v1/media?uuid=c32d84f6-4896-44de-a845-562da9d7a9b5' => '2025/12/THT-WEBSITE-1.png',
        'https://advertisingvietnam.com/cdn-cgi/image/width=1440,height=756,quality=90,fit=cover,format=auto/https://media-api.advertisingvietnam.com/oapi/v1/media?uuid=d88fc643-418a-4dc7-a055-9f4bb933bef1' => '2025/11/IMG_20251107_121232-Edit-scaled.jpg',
        'https://wemax.vn/wp-content/uploads/2023/12/san-xuat-video-quang-cao.jpg' => '2026/01/396569_684062.webp',
        'https://thtmedia.com.vn/wp-content/themes/Impreza-child/xay-tiktok/images/trangbi.webp' => '2025/12/THT-WEBSITE-1.png',
    ],
    'broken_external_media_patterns' => [
        '~https://cleverads\.vn/blog/wp-content/uploads/2022/12/digital-marrketing-2023[^"\'\s,<]*\.png~iu' => '2023/05/Aesthetic-and-Minimalist-Modern-Cafe-and-Coffee-Shop-Facebook-Post-2-scaled.jpg',
    ],

    /*
    | These bases mirror the currently indexed WordPress permalink structure.
    | They are migration inputs, not the deployment domain of the Laravel app.
    */
    'paths' => [
        'post' => '',
        'page' => '',
        'landing' => '',
        'service' => trim((string) env('WP_SOURCE_SERVICE_BASE', 'service'), '/'),
        'us_portfolio' => trim((string) env('WP_SOURCE_PORTFOLIO_BASE', 'du-an'), '/'),
    ],

    'content_types' => [
        'post',
        'page',
        'landing',
        'partner',
        'service',
        'us_portfolio',
        'us_testimonial',
        'video',
    ],

    'public_types' => [
        'post',
        'page',
        'landing',
        'service',
        'us_portfolio',
    ],
];
