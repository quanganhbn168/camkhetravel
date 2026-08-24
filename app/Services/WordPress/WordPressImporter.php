<?php

namespace App\Services\WordPress;

use App\Models\ContentItem;
use App\Models\ImportRun;
use App\Models\MediaAsset;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\SiteSetting;
use App\Models\Taxonomy;
use App\Models\Term;
use Carbon\CarbonImmutable;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class WordPressImporter
{
    private ConnectionInterface $source;

    private array $counts = [];

    private array $warnings = [];

    public function __construct(private readonly WordPressValueDecoder $decoder)
    {
        $this->source = DB::connection(config('wordpress.connection', 'wordpress'));
    }

    public function import(bool $skipMedia = false): ImportRun
    {
        $run = ImportRun::create([
            'source' => 'wordpress',
            'status' => 'running',
            'started_at' => now(),
            'counts' => [],
            'warnings' => [],
        ]);

        try {
            $this->source->getPdo();

            $this->counts['settings'] = $this->importSettings();
            $this->counts['taxonomies'] = $this->importTaxonomies();
            $this->counts['terms'] = $this->importTerms();
            $this->counts['content_items'] = $this->importContent();
            $this->linkContentParents();
            $this->linkTermParents();
            $this->counts['term_relationships'] = $this->importTermRelationships();

            if (! $skipMedia) {
                $this->counts['media_assets'] = $this->importMedia();
            }

            $menuCounts = $this->importMenus();
            $this->counts = [...$this->counts, ...$menuCounts];
            $this->auditImportedData();

            $run->update([
                'status' => 'completed',
                'finished_at' => now(),
                'counts' => $this->counts,
                'warnings' => $this->warnings,
            ]);
        } catch (Throwable $exception) {
            $run->update([
                'status' => 'failed',
                'finished_at' => now(),
                'counts' => $this->counts,
                'warnings' => $this->warnings,
                'error_message' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        return $run->fresh();
    }

    private function importSettings(): int
    {
        $optionNames = [
            'blogname',
            'blogdescription',
            'siteurl',
            'home',
            'permalink_structure',
            'category_base',
            'tag_base',
            'show_on_front',
            'page_on_front',
            'page_for_posts',
            'rank-math-options-general',
            'rank-math-options-titles',
            'rank-math-options-sitemap',
        ];

        $options = $this->source->table('options')
            ->whereIn('option_name', $optionNames)
            ->get(['option_name', 'option_value']);

        foreach ($options as $option) {
            $decoded = $this->decoder->decode($option->option_value);
            $isStructured = is_array($decoded);

            SiteSetting::updateOrCreate(
                ['group' => 'wordpress', 'key' => $option->option_name],
                [
                    'type' => $isStructured ? 'json' : 'string',
                    'value' => $isStructured ? $this->encode($decoded) : (string) $decoded,
                    'is_public' => in_array($option->option_name, ['blogname', 'blogdescription'], true),
                ],
            );
        }

        return $options->count();
    }

    private function importTaxonomies(): int
    {
        $hierarchical = [
            'category',
            'danh-muc-dich-vu',
            'danh-muc-doi-tac',
            'danh-muc-video',
            'landing-cate',
            'nav_menu',
            'us_portfolio_category',
            'us_testimonial_category',
        ];

        $labels = [
            'category' => 'Chuyên mục bài viết',
            'post_tag' => 'Thẻ bài viết',
            'danh-muc-dich-vu' => 'Danh mục dịch vụ',
            'danh-muc-doi-tac' => 'Danh mục đối tác',
            'danh-muc-video' => 'Danh mục video',
            'landing-cate' => 'Danh mục landing page',
            'us_portfolio_category' => 'Danh mục dự án',
            'us_portfolio_tag' => 'Thẻ dự án',
            'us_testimonial_category' => 'Nhóm đánh giá',
            'nav_menu' => 'Menu WordPress',
        ];

        $keys = $this->source->table('term_taxonomy')
            ->distinct()
            ->orderBy('taxonomy')
            ->pluck('taxonomy');

        foreach ($keys as $key) {
            Taxonomy::updateOrCreate(
                ['source' => 'wordpress', 'key' => $key],
                [
                    'label' => $labels[$key] ?? $key,
                    'is_hierarchical' => in_array($key, $hierarchical, true),
                    'is_public' => $key !== 'nav_menu',
                ],
            );
        }

        return $keys->count();
    }

    private function importTerms(): int
    {
        $taxonomies = Taxonomy::where('source', 'wordpress')->pluck('id', 'key');
        $count = 0;

        $this->source->table('term_taxonomy as tt')
            ->join('terms as t', 't.term_id', '=', 'tt.term_id')
            ->orderBy('tt.term_taxonomy_id')
            ->select([
                'tt.term_taxonomy_id',
                'tt.term_id',
                'tt.taxonomy',
                'tt.description',
                'tt.parent',
                'tt.count',
                't.name',
                't.slug',
                't.term_group',
            ])
            ->chunk(250, function (Collection $rows) use ($taxonomies, &$count): void {
                foreach ($rows as $row) {
                    $taxonomyId = $taxonomies[$row->taxonomy] ?? null;

                    if (! $taxonomyId) {
                        continue;
                    }

                    Term::updateOrCreate(
                        [
                            'taxonomy_id' => $taxonomyId,
                            'source_id' => $row->term_taxonomy_id,
                        ],
                        [
                            'name' => $row->name,
                            'slug' => $row->slug,
                            'description' => $row->description ?: null,
                            'parent_source_id' => $row->parent ?: null,
                            'source_count' => (int) $row->count,
                            'legacy_meta' => [
                                'term_id' => (int) $row->term_id,
                                'term_group' => (int) $row->term_group,
                            ],
                        ],
                    );

                    $count++;
                }
            });

        return $count;
    }

    private function importContent(): int
    {
        $types = config('wordpress.content_types', []);
        $postLookup = $this->source->table('posts')
            ->whereIn('post_type', $types)
            ->get(['ID', 'post_parent', 'post_name', 'post_type', 'post_status'])
            ->keyBy('ID');
        $count = 0;

        $this->source->table('posts')
            ->whereIn('post_type', $types)
            ->whereNotIn('post_status', ['auto-draft', 'trash', 'inherit'])
            ->orderBy('ID')
            ->chunkById(150, function (Collection $posts) use ($postLookup, &$count): void {
                $meta = $this->postMetaFor($posts->pluck('ID')->all());

                foreach ($posts as $post) {
                    $postMeta = $meta[(int) $post->ID] ?? [];
                    $canonicalPath = $this->canonicalPath($post, $postLookup);
                    $seo = $this->seoData($postMeta);
                    $isPublishedPublic = $post->post_status === 'publish' && $canonicalPath !== null;

                    $existing = ContentItem::query()
                        ->where('source', 'wordpress')
                        ->where('source_id', $post->ID)
                        ->first();
                    $payload = [
                        'type' => $post->post_type,
                        'status' => $this->contentStatus($post->post_status),
                        'title' => $post->post_title ?: '(Không có tiêu đề)',
                        'slug' => $post->post_name ?: 'wordpress-'.$post->ID,
                        'canonical_path' => $canonicalPath,
                        'legacy_url' => $this->legacyUrl($post, $canonicalPath),
                        'parent_source_id' => $post->post_parent ?: null,
                        'author_source_id' => $post->post_author ?: null,
                        'menu_order' => (int) $post->menu_order,
                        'excerpt' => $post->post_excerpt ?: null,
                        'body' => $post->post_content ?: null,
                        'featured_media_source_id' => $this->integerMeta($postMeta, '_thumbnail_id'),
                        'published_at' => $this->wordpressTimestamp($post->post_date_gmt, $post->post_date),
                        'content_modified_at' => $this->wordpressTimestamp($post->post_modified_gmt, $post->post_modified),
                        ...$seo,
                        'needs_seo_review' => $isPublishedPublic
                            && (blank($seo['seo_title']) || blank($seo['seo_description'])),
                        'legacy_meta' => $this->jsonSafe($postMeta),
                    ];

                    $sourceSnapshot = $payload;
                    $lockedFields = $this->lockedFields(
                        $existing,
                        $existing?->seo_source === 'manual' ? array_keys($seo) : [],
                    );

                    foreach ($lockedFields as $field) {
                        unset($payload[$field]);
                    }

                    if (in_array('seo_source', $lockedFields, true)) {
                        unset($payload['needs_seo_review']);
                    }

                    $payload['source_snapshot'] = $this->jsonSafe($sourceSnapshot);
                    $payload['import_locked_fields'] = $lockedFields;

                    ContentItem::updateOrCreate(
                        ['source' => 'wordpress', 'source_id' => $post->ID],
                        $payload,
                    );

                    $count++;
                }
            }, 'ID', 'ID');

        return $count;
    }

    private function linkContentParents(): void
    {
        $items = ContentItem::where('source', 'wordpress')
            ->whereNotNull('parent_source_id')
            ->get(['id', 'parent_id', 'parent_source_id', 'import_locked_fields']);
        $ids = ContentItem::where('source', 'wordpress')->pluck('id', 'source_id');

        foreach ($items as $item) {
            if (in_array('parent_id', (array) $item->import_locked_fields, true)) {
                continue;
            }

            $parentId = $ids[$item->parent_source_id] ?? null;

            if ($item->parent_id !== $parentId) {
                $item->update(['parent_id' => $parentId]);
            }
        }
    }

    private function linkTermParents(): void
    {
        $terms = Term::whereHas('taxonomy', fn ($query) => $query->where('source', 'wordpress'))->get();
        $bySourceTerm = [];

        foreach ($terms as $term) {
            $sourceTermId = data_get($term->legacy_meta, 'term_id');

            if ($sourceTermId) {
                $bySourceTerm[$term->taxonomy_id.':'.$sourceTermId] = $term->id;
            }
        }

        foreach ($terms as $term) {
            if (! $term->parent_source_id) {
                continue;
            }

            $parentId = $bySourceTerm[$term->taxonomy_id.':'.$term->parent_source_id] ?? null;

            if ($term->parent_id !== $parentId) {
                $term->update(['parent_id' => $parentId]);
            }
        }
    }

    private function importTermRelationships(): int
    {
        $contentIds = ContentItem::where('source', 'wordpress')->pluck('id', 'source_id');
        $termIds = Term::whereHas('taxonomy', fn ($query) => $query->where('source', 'wordpress'))
            ->pluck('id', 'source_id');

        DB::table('content_item_term')->whereIn('content_item_id', $contentIds->values())->delete();
        $count = 0;

        $this->source->table('term_relationships')
            ->orderBy('object_id')
            ->orderBy('term_taxonomy_id')
            ->chunk(500, function (Collection $relationships) use ($contentIds, $termIds, &$count): void {
                $rows = [];

                foreach ($relationships as $relationship) {
                    $contentId = $contentIds[$relationship->object_id] ?? null;
                    $termId = $termIds[$relationship->term_taxonomy_id] ?? null;

                    if (! $contentId || ! $termId) {
                        continue;
                    }

                    $rows[] = [
                        'content_item_id' => $contentId,
                        'term_id' => $termId,
                        'position' => (int) $relationship->term_order,
                    ];
                }

                if ($rows !== []) {
                    DB::table('content_item_term')->insertOrIgnore($rows);
                    $count += count($rows);
                }
            });

        return $count;
    }

    private function importMedia(): int
    {
        $count = 0;
        $sourceUrl = config('wordpress.source_url');

        $this->source->table('posts')
            ->where('post_type', 'attachment')
            ->orderBy('ID')
            ->chunkById(150, function (Collection $attachments) use ($sourceUrl, &$count): void {
                $meta = $this->postMetaFor($attachments->pluck('ID')->all());

                foreach ($attachments as $attachment) {
                    $attachmentMeta = $meta[(int) $attachment->ID] ?? [];
                    $path = (string) ($this->firstMeta($attachmentMeta, '_wp_attached_file') ?? '');
                    $details = $this->firstMeta($attachmentMeta, '_wp_attachment_metadata');
                    $details = is_array($details) ? $details : [];

                    $existing = MediaAsset::query()
                        ->where('source', 'wordpress')
                        ->where('source_id', $attachment->ID)
                        ->first();
                    $payload = [
                        'parent_source_id' => $attachment->post_parent ?: null,
                        'slug' => $attachment->post_name ?: null,
                        'title' => $attachment->post_title ?: null,
                        'alt_text' => $this->stringMeta($attachmentMeta, '_wp_attachment_image_alt'),
                        'caption' => $attachment->post_excerpt ?: null,
                        'description' => $attachment->post_content ?: null,
                        'mime_type' => $attachment->post_mime_type ?: null,
                        'source_url' => $path !== ''
                            ? $sourceUrl.'/wp-content/uploads/'.ltrim($path, '/')
                            : ($attachment->guid ?: null),
                        'source_path' => $path !== '' ? 'wp-content/uploads/'.ltrim($path, '/') : null,
                        'width' => isset($details['width']) ? (int) $details['width'] : null,
                        'height' => isset($details['height']) ? (int) $details['height'] : null,
                        'file_size' => isset($details['filesize']) ? (int) $details['filesize'] : null,
                        'metadata' => $this->jsonSafe([
                            'attachment' => $details,
                            'wordpress_meta' => $attachmentMeta,
                        ]),
                        'published_at' => $this->wordpressTimestamp($attachment->post_date_gmt, $attachment->post_date),
                    ];
                    $sourceSnapshot = $payload;
                    $lockedFields = $this->lockedFields($existing);

                    foreach ($lockedFields as $field) {
                        unset($payload[$field]);
                    }

                    $payload['source_snapshot'] = $this->jsonSafe($sourceSnapshot);
                    $payload['import_locked_fields'] = $lockedFields;

                    MediaAsset::updateOrCreate(
                        ['source' => 'wordpress', 'source_id' => $attachment->ID],
                        $payload,
                    );

                    $count++;
                }
            }, 'ID', 'ID');

        return $count;
    }

    private function importMenus(): array
    {
        $locations = $this->menuLocations();
        $menuRows = $this->source->table('term_taxonomy as tt')
            ->join('terms as t', 't.term_id', '=', 'tt.term_id')
            ->where('tt.taxonomy', 'nav_menu')
            ->get(['tt.term_taxonomy_id', 'tt.term_id', 't.name']);
        $menuMap = [];

        foreach ($menuRows as $row) {
            $menu = Menu::updateOrCreate(
                ['source' => 'wordpress', 'source_id' => $row->term_taxonomy_id],
                [
                    'name' => $row->name,
                    'location' => $locations[(int) $row->term_id] ?? null,
                    'is_active' => true,
                ],
            );
            $menuMap[(int) $row->term_taxonomy_id] = $menu;
        }

        $items = $this->source->table('posts as p')
            ->join('term_relationships as tr', 'tr.object_id', '=', 'p.ID')
            ->join('term_taxonomy as tt', 'tt.term_taxonomy_id', '=', 'tr.term_taxonomy_id')
            ->where('p.post_type', 'nav_menu_item')
            ->where('tt.taxonomy', 'nav_menu')
            ->orderBy('p.menu_order')
            ->get([
                'p.ID',
                'p.post_title',
                'p.menu_order',
                'tt.term_taxonomy_id',
            ]);
        $meta = $this->postMetaFor($items->pluck('ID')->all());
        $content = ContentItem::where('source', 'wordpress')->get()->keyBy('source_id');
        $itemCount = 0;

        foreach ($items as $item) {
            $menu = $menuMap[(int) $item->term_taxonomy_id] ?? null;

            if (! $menu) {
                continue;
            }

            $itemMeta = $meta[(int) $item->ID] ?? [];
            $objectId = $this->integerMeta($itemMeta, '_menu_item_object_id');
            $linkedContent = $objectId ? ($content[$objectId] ?? null) : null;
            $url = $this->stringMeta($itemMeta, '_menu_item_url') ?: $linkedContent?->legacy_url;
            $classes = $this->firstMeta($itemMeta, '_menu_item_classes');

            MenuItem::updateOrCreate(
                ['menu_id' => $menu->id, 'source_id' => $item->ID],
                [
                    'parent_source_id' => $this->integerMeta($itemMeta, '_menu_item_menu_item_parent'),
                    'linked_source_id' => $objectId,
                    'linked_source_type' => $this->stringMeta($itemMeta, '_menu_item_object'),
                    'label' => $item->post_title ?: $linkedContent?->title ?: 'Menu item '.$item->ID,
                    'url' => $url,
                    'target' => $this->stringMeta($itemMeta, '_menu_item_target'),
                    'css_classes' => is_array($classes) ? implode(' ', array_filter($classes)) : (string) $classes,
                    'position' => (int) $item->menu_order,
                    'legacy_meta' => $this->jsonSafe($itemMeta),
                ],
            );

            $itemCount++;
        }

        $this->linkMenuParents();

        return ['menus' => $menuRows->count(), 'menu_items' => $itemCount];
    }

    private function linkMenuParents(): void
    {
        Menu::where('source', 'wordpress')->with('items')->get()->each(function (Menu $menu): void {
            $ids = $menu->items->pluck('id', 'source_id');

            foreach ($menu->items as $item) {
                $parentId = $item->parent_source_id ? ($ids[$item->parent_source_id] ?? null) : null;

                if ($item->parent_id !== $parentId) {
                    $item->update(['parent_id' => $parentId]);
                }
            }
        });
    }

    private function menuLocations(): array
    {
        $options = $this->source->table('options')
            ->where('option_name', 'like', 'theme_mods_%')
            ->pluck('option_value');
        $locations = [];

        foreach ($options as $value) {
            $decoded = $this->decoder->decode($value);

            foreach ((array) data_get($decoded, 'nav_menu_locations', []) as $location => $termId) {
                $locations[(int) $termId] = $location;
            }
        }

        return $locations;
    }

    private function postMetaFor(array $postIds): array
    {
        if ($postIds === []) {
            return [];
        }

        $grouped = [];
        $rows = $this->source->table('postmeta')
            ->whereIn('post_id', $postIds)
            ->orderBy('meta_id')
            ->get(['post_id', 'meta_key', 'meta_value']);

        foreach ($rows as $row) {
            $grouped[(int) $row->post_id][$row->meta_key][] = $this->decoder->decode($row->meta_value);
        }

        return $grouped;
    }

    private function seoData(array $meta): array
    {
        $title = $this->stringMeta($meta, 'rank_math_title');
        $description = $this->stringMeta($meta, 'rank_math_description');
        $canonical = $this->stringMeta($meta, 'rank_math_canonical_url');
        $robots = $this->firstMeta($meta, 'rank_math_robots');
        $schemas = [];

        foreach ($meta as $key => $values) {
            if (str_starts_with($key, 'rank_math_schema_')) {
                $schemas[$key] = count($values) === 1 ? $values[0] : $values;
            }
        }

        if (is_string($robots) && $robots !== '') {
            $robots = array_values(array_filter(array_map('trim', explode(',', $robots))));
        }

        return [
            'seo_title' => $title,
            'seo_description' => $description,
            'seo_canonical_url' => $canonical,
            'seo_robots' => is_array($robots) ? $this->jsonSafe($robots) : null,
            'focus_keyword' => $this->stringMeta($meta, 'rank_math_focus_keyword'),
            'seo_score' => $this->integerMeta($meta, 'rank_math_seo_score'),
            'is_pillar_content' => $this->truthy($this->firstMeta($meta, 'rank_math_pillar_content')),
            'exclude_from_sitemap' => $this->truthy($this->firstMeta($meta, 'rank_math_exclude_sitemap')),
            'og_title' => $this->stringMeta($meta, 'rank_math_facebook_title'),
            'og_description' => $this->stringMeta($meta, 'rank_math_facebook_description'),
            'og_image_url' => $this->stringMeta($meta, 'rank_math_facebook_image')
                ?: $this->stringMeta($meta, 'rank_math_og_content_image'),
            'twitter_title' => $this->stringMeta($meta, 'rank_math_twitter_title'),
            'twitter_description' => $this->stringMeta($meta, 'rank_math_twitter_description'),
            'twitter_image_url' => $this->stringMeta($meta, 'rank_math_twitter_image'),
            'structured_data' => $schemas !== [] ? $this->jsonSafe($schemas) : null,
            'seo_source' => ($title || $description || $canonical || $robots) ? 'rank_math' : 'template',
        ];
    }

    private function canonicalPath(object $post, Collection $postLookup): ?string
    {
        if ($post->post_status !== 'publish' || ! in_array($post->post_type, config('wordpress.public_types', []), true)) {
            return null;
        }

        $segments = [$post->post_name];

        if ($post->post_type === 'page') {
            $parentId = (int) $post->post_parent;
            $seen = [(int) $post->ID => true];

            while ($parentId && isset($postLookup[$parentId]) && ! isset($seen[$parentId])) {
                $parent = $postLookup[$parentId];
                $seen[$parentId] = true;
                array_unshift($segments, $parent->post_name);
                $parentId = (int) $parent->post_parent;
            }
        }

        $prefix = config('wordpress.paths.'.$post->post_type, '');
        $segments = array_values(array_filter([$prefix, ...$segments], fn ($segment) => filled($segment)));

        return '/'.implode('/', $segments).'/';
    }

    private function legacyUrl(object $post, ?string $canonicalPath): string
    {
        $base = config('wordpress.source_url');

        if ($canonicalPath !== null) {
            return $base.$canonicalPath;
        }

        return $base.'/?'.http_build_query(['post_type' => $post->post_type, 'p' => $post->ID]);
    }

    private function contentStatus(string $status): string
    {
        return match ($status) {
            'publish' => 'published',
            'future' => 'scheduled',
            default => $status,
        };
    }

    private function wordpressTimestamp(?string $gmt, ?string $local): ?CarbonImmutable
    {
        $value = ($gmt && $gmt !== '0000-00-00 00:00:00') ? $gmt : $local;

        if (! $value || $value === '0000-00-00 00:00:00') {
            return null;
        }

        return CarbonImmutable::createFromFormat('Y-m-d H:i:s', $value, 'UTC');
    }

    private function firstMeta(array $meta, string $key): mixed
    {
        $values = $meta[$key] ?? [];

        return $values === [] ? null : $values[array_key_last($values)];
    }

    private function stringMeta(array $meta, string $key): ?string
    {
        $value = $this->firstMeta($meta, $key);

        return is_scalar($value) && (string) $value !== '' ? (string) $value : null;
    }

    private function integerMeta(array $meta, string $key): ?int
    {
        $value = $this->firstMeta($meta, $key);

        return is_numeric($value) && (int) $value > 0 ? (int) $value : null;
    }

    private function truthy(mixed $value): bool
    {
        return in_array($value, [true, 1, '1', 'on', 'yes', 'true'], true);
    }

    private function lockedFields(?object $record, array $additional = []): array
    {
        $locked = array_values(array_filter((array) ($record?->import_locked_fields ?? []), 'is_string'));

        return array_values(array_unique([...$locked, ...$additional]));
    }

    private function jsonSafe(mixed $value): mixed
    {
        return json_decode($this->encode($value), true);
    }

    private function encode(mixed $value): string
    {
        return json_encode(
            $value,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR,
        );
    }

    private function auditImportedData(): void
    {
        $duplicates = ContentItem::query()
            ->where('source', 'wordpress')
            ->whereNotNull('canonical_path')
            ->select('canonical_path', DB::raw('COUNT(*) as total'))
            ->groupBy('canonical_path')
            ->having('total', '>', 1)
            ->pluck('total', 'canonical_path');

        if ($duplicates->isNotEmpty()) {
            $this->warnings[] = 'Trùng canonical path: '.$duplicates->keys()->implode(', ');
        }

        $reviewCount = ContentItem::where('source', 'wordpress')
            ->where('needs_seo_review', true)
            ->count();

        if ($reviewCount > 0) {
            $this->warnings[] = $reviewCount.' nội dung công khai đang dùng SEO template hoặc thiếu title/description thủ công.';
        }
    }
}
