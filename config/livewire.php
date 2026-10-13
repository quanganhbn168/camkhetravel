<?php

/*
 * Chỉ ghi đè phần tải tệp tạm của Livewire. Mặc định Livewire giới hạn 12 MB mỗi tệp, nên upload
 * trong admin (thư viện media/Curator, tệp hồ sơ) bị chặn trước khi tới giới hạn 256 MB của Curator.
 * Toàn bộ khoá còn lại giữ như mặc định của Livewire vì mảng này được gộp ở mức khoá cấp một.
 */
return [
    'temporary_file_upload' => [
        'disk' => env('LIVEWIRE_TEMPORARY_FILE_UPLOAD_DISK'),
        // max tính bằng KB: 262144 KB = 256 MB.
        'rules' => ['required', 'file', 'max:262144'],
        'directory' => null,
        'middleware' => null,
        'preview_mimes' => [
            'png', 'gif', 'bmp', 'svg', 'wav', 'mp4',
            'mov', 'avi', 'wmv', 'mp3', 'm4a',
            'jpg', 'jpeg', 'mpga', 'webp', 'wma',
        ],
        // Tệp 256 MB trên mạng chậm cần nhiều hơn 5 phút mặc định.
        'max_upload_time' => 15,
        'cleanup' => true,
    ],
];
