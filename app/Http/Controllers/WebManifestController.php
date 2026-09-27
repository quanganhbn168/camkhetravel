<?php

namespace App\Http\Controllers;

use App\Settings\WebsiteSettings;
use Illuminate\Http\JsonResponse;

class WebManifestController extends Controller
{
    public function __invoke(WebsiteSettings $website): JsonResponse
    {
        $brand = file_get_contents(resource_path('css/brand.css'));
        preg_match('/--site-primary\s*:\s*(#[a-f0-9]{6})\b/i', $brand, $primary);
        $name = trim($website->site_name) ?: 'Website';

        return response()->json([
            'name' => $name,
            'short_name' => $name,
            'start_url' => '/',
            'display' => 'standalone',
            'background_color' => '#ffffff',
            'theme_color' => $primary[1] ?? '#ffffff',
            'icons' => [
                ['src' => '/android-chrome-192x192.png', 'sizes' => '192x192', 'type' => 'image/png'],
                ['src' => '/android-chrome-512x512.png', 'sizes' => '512x512', 'type' => 'image/png'],
            ],
        ])->header('Content-Type', 'application/manifest+json');
    }
}
