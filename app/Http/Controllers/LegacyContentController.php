<?php

namespace App\Http\Controllers;

use App\Models\ContentItem;
use App\Models\Redirect;
use App\Models\SiteSetting;
use App\Services\WordPress\WordPressMediaUrlMapper;
use App\Support\Seo\SeoMetadataBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class LegacyContentController extends Controller
{
    public function __construct(private readonly WordPressMediaUrlMapper $mediaUrlMapper) {}

    public function home(SeoMetadataBuilder $seo): View
    {
        $frontPageId = (int) SiteSetting::query()
            ->where('group', 'wordpress')
            ->where('key', 'page_on_front')
            ->value('value');
        $item = ContentItem::query()
            ->where('source', 'wordpress')
            ->where('status', 'published')
            ->when(
                $frontPageId > 0,
                fn ($query) => $query->where('source_id', $frontPageId),
                fn ($query) => $query->where('type', 'page')->orderBy('source_id'),
            )
            ->first();

        if (! $item) {
            abort(404);
        }

        return view('legacy.content', [
            'item' => $item,
            'seo' => $seo->forContent($item, '/'),
            'bodyHtml' => $this->mediaUrlMapper->absoluteLocalMediaUrls($item->body),
        ]);
    }

    public function show(Request $request, string $path, SeoMetadataBuilder $seo): View|RedirectResponse|Response
    {
        $canonicalPath = '/'.trim($path, '/').'/';
        $item = ContentItem::query()
            ->where('source', 'wordpress')
            ->where('status', 'published')
            ->where('canonical_path', $canonicalPath)
            ->orderByRaw("CASE type WHEN 'landing' THEN 1 WHEN 'page' THEN 2 WHEN 'post' THEN 3 WHEN 'service' THEN 4 WHEN 'us_portfolio' THEN 5 ELSE 9 END")
            ->first();

        if (! $item) {
            $redirect = Redirect::query()
                ->where('is_active', true)
                ->whereIn('from_path', array_values(array_unique([
                    $request->getPathInfo(),
                    $canonicalPath,
                    rtrim($canonicalPath, '/'),
                ])))
                ->first();

            if ($redirect) {
                $redirect->increment('hits');
                $redirect->forceFill(['last_hit_at' => now()])->save();

                return redirect()->away($redirect->to_url, $redirect->status_code);
            }

            abort(404);
        }

        $frontPageId = (int) SiteSetting::query()
            ->where('group', 'wordpress')
            ->where('key', 'page_on_front')
            ->value('value');

        if ($frontPageId > 0 && (int) $item->source_id === $frontPageId) {
            $target = '/';

            if ($request->getQueryString()) {
                $target .= '?'.$request->getQueryString();
            }

            return redirect()->away($target, 301);
        }

        $requestPath = (string) parse_url($request->getRequestUri(), PHP_URL_PATH);
        $specialPaths = ['/404-not-found/', '/search/', '/under-construction/', '/test/'];

        if (! in_array($canonicalPath, $specialPaths, true) && ! str_ends_with($requestPath, '/')) {
            $target = $canonicalPath;

            if ($request->getQueryString()) {
                $target .= '?'.$request->getQueryString();
            }

            return redirect()->away($target, 301);
        }

        if ($canonicalPath === '/404-not-found/') {
            $metadata = $seo->forContent($item);

            return response()->view('legacy.content', [
                'item' => $item,
                'seo' => $metadata
                    ->withRobots(['index', 'follow'])
                    ->withoutStructuredData(),
                'bodyHtml' => $this->mediaUrlMapper->absoluteLocalMediaUrls($item->body),
                'emitCanonical' => false,
            ], 404);
        }

        if (in_array($canonicalPath, ['/search/', '/under-construction/', '/test/'], true)) {
            $metadata = $seo->forContent($item);

            return view('legacy.content', [
                'item' => $item,
                'seo' => $metadata
                    ->withRobots(['index', 'follow'])
                    ->withoutStructuredData(),
                'bodyHtml' => $this->mediaUrlMapper->absoluteLocalMediaUrls($item->body),
                'emitCanonical' => false,
            ]);
        }

        return view('legacy.content', [
            'item' => $item,
            'seo' => $seo->forContent($item),
            'bodyHtml' => $this->mediaUrlMapper->absoluteLocalMediaUrls($item->body),
        ]);
    }
}
