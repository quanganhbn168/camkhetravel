<?php

namespace App\Http\Controllers;

use App\Support\Seo\SitemapBuilder;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemap(SitemapBuilder $sitemap): Response
    {
        return response($sitemap->build()->render(), 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(): Response
    {
        $lines = app()->environment('production')
            ? ['User-agent: *', 'Allow: /', 'Sitemap: '.url('/sitemap.xml')]
            : ['User-agent: *', 'Disallow: /'];

        return response(implode("\n", $lines)."\n", 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
