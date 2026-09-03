<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\JsonResponse;

final class BniManifestController
{
    public function __invoke(): JsonResponse
    {
        $scope = '/le-chuyen-giao';
        $icon = fn (int $size): array => [
            'src' => asset("bni-icon-{$size}x{$size}.png"),
            'sizes' => "{$size}x{$size}",
            'type' => 'image/png',
            'purpose' => 'any maskable',
        ];

        return response()->json([
            'id' => $scope,
            'name' => 'BNI Vietnam',
            'short_name' => 'BNI',
            'description' => 'Không gian sự kiện và kết nối cộng đồng BNI Vietnam.',
            'start_url' => $scope,
            'scope' => $scope,
            'display' => 'standalone',
            'orientation' => 'portrait-primary',
            'background_color' => '#fff8f1',
            'theme_color' => '#cf2031',
            'icons' => [$icon(192), $icon(512)],
            'shortcuts' => [
                [
                    'name' => 'Lịch trình',
                    'short_name' => 'Lịch trình',
                    'url' => $scope.'#lich-trinh',
                    'icons' => [$icon(192)],
                ],
                [
                    'name' => 'Thư mời BNI',
                    'short_name' => 'Thư mời',
                    'url' => $scope.'/thu-moi',
                    'icons' => [$icon(192)],
                ],
                [
                    'name' => 'Thư viện ảnh BNI',
                    'short_name' => 'Hình ảnh',
                    'url' => $scope.'/thu-vien-anh',
                    'icons' => [$icon(192)],
                ],
                [
                    'name' => 'BNI Pickleball',
                    'short_name' => 'Pickleball',
                    'url' => $scope.'/pickleball',
                    'icons' => [$icon(192)],
                ],
            ],
        ], 200, [
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
