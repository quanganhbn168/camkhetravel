<?php

namespace App\Http\Controllers;

use App\Models\ContentItem;
use App\Models\MediaAsset;
use App\Models\SiteSetting;
use App\Models\Term;
use App\Services\WordPress\WordPressMediaUrlMapper;
use App\Support\Seo\ArchiveDefinition;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class ArchiveController extends Controller
{
    public function __construct(private readonly WordPressMediaUrlMapper $mediaUrlMapper) {}

    public function blog(?string $pagination = null): View
    {
        return $this->show('blog'.($pagination ? '/'.$pagination : ''));
    }

    public function service(string $term): View
    {
        return $this->show('danh-muc-dich-vu/'.$term);
    }

    public function portfolio(string $term): View
    {
        return $this->show('danh-muc-du-an/'.$term);
    }

    public function landing(string $term): View
    {
        return $this->show('landing-cate/'.$term);
    }

    public function show(string $path): View
    {
        $definition = ArchiveDefinition::find($path);
        abort_unless($definition, 404);

        $canonicalPath = $definition['page'] > 1
            ? rtrim($definition['path'], '/').'/page/'.$definition['page'].'/'
            : $definition['path'];
        $term = Term::query()
            ->where('slug', $definition['term'])
            ->whereHas('taxonomy', fn (Builder $query) => $query
                ->where('source', 'wordpress')
                ->where('key', $definition['taxonomy']))
            ->firstOrFail();
        $termIds = $this->descendantIds($term);
        $query = ContentItem::query()
            ->where('source', 'wordpress')
            ->where('status', 'published')
            ->where('type', $definition['content_type'])
            ->whereHas('terms', fn (Builder $terms) => $terms->whereIn('terms.id', $termIds))
            ->orderByDesc('published_at')
            ->orderByDesc('id');
        $perPage = $definition['taxonomy'] === 'category' ? 18 : 24;
        $items = $query->paginate($perPage, ['*'], 'page', $definition['page']);

        if ($definition['page'] > $items->lastPage()) {
            abort(404);
        }

        $media = MediaAsset::query()
            ->where('source', 'wordpress')
            ->whereIn('source_id', $items->getCollection()->pluck('featured_media_source_id')->filter())
            ->get()
            ->keyBy('source_id');
        $siteName = SiteSetting::query()
            ->where('group', 'wordpress')
            ->where('key', 'blogname')
            ->value('value') ?: config('app.name');
        $siteDescription = SiteSetting::query()
            ->where('group', 'wordpress')
            ->where('key', 'blogdescription')
            ->value('value') ?: '';
        $rankMathSetting = SiteSetting::query()
            ->where('group', 'wordpress')
            ->where('key', 'rank-math-options-titles')
            ->first();
        $rankMath = is_array($rankMathSetting?->decodedValue())
            ? $rankMathSetting->decodedValue()
            : [];
        $title = $term->name.($definition['page'] > 1 ? ' - Trang '.$definition['page'] : '').' - '.$siteName;
        $canonicalUrl = rtrim((string) config('app.url'), '/').$canonicalPath;
        $isEmpty = $items->isEmpty();
        $description = trim(strip_tags((string) $term->description))
            ?: $term->name.' - '.$siteDescription;

        if (mb_strlen($description) > 160) {
            $truncated = mb_substr($description, 0, 160);
            $lastSpace = mb_strrpos($truncated, ' ');
            $description = rtrim($lastSpace === false ? $truncated : mb_substr($truncated, 0, $lastSpace));
        }

        $structuredData = $isEmpty ? null : [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $title,
            'description' => $description,
            'url' => $canonicalUrl,
            'mainEntity' => [
                '@type' => 'ItemList',
                'itemListElement' => $items->getCollection()
                    ->values()
                    ->map(fn (ContentItem $item, int $index): array => [
                        '@type' => 'ListItem',
                        'position' => (($items->currentPage() - 1) * $items->perPage()) + $index + 1,
                        'url' => rtrim((string) config('app.url'), '/').$item->canonical_path,
                        'name' => $item->title,
                    ])
                    ->all(),
            ],
        ];

        return view('legacy.archive', [
            'term' => $term,
            'items' => $items,
            'media' => $media,
            'title' => $title,
            'description' => $description,
            'canonicalUrl' => $isEmpty ? null : $canonicalUrl,
            'ogImageUrl' => $this->mediaUrlMapper->absoluteUrl(
                $this->mediaUrlMapper->localizeUrl($rankMath['open_graph_image'] ?? null),
            ),
            'structuredData' => $structuredData,
            'robots' => ['index', 'follow'],
            'previousUrl' => $definition['page'] > 1
                ? $this->pageUrl($definition['path'], $definition['page'] - 1)
                : null,
            'nextUrl' => $definition['page'] < $items->lastPage()
                ? $this->pageUrl($definition['path'], $definition['page'] + 1)
                : null,
        ]);
    }

    private function descendantIds(Term $term): array
    {
        $ids = [$term->id];
        $frontier = [$term->id];

        while ($frontier !== []) {
            $frontier = Term::query()->whereIn('parent_id', $frontier)->pluck('id')->all();
            $ids = [...$ids, ...$frontier];
        }

        return array_values(array_unique($ids));
    }

    private function pageUrl(string $basePath, int $page): string
    {
        $path = $page <= 1 ? $basePath : rtrim($basePath, '/').'/page/'.$page.'/';

        return rtrim((string) config('app.url'), '/').$path;
    }
}
