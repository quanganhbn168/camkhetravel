<?php

namespace App\Console\Commands;

use App\Models\ContentItem;
use App\Models\Landing;
use App\Models\LandingCategory;
use App\Models\MediaAsset;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Taxonomy;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SyncLegacyContentCommand extends Command
{
    protected $signature = 'cms:sync-legacy-content';

    protected $description = 'Khởi tạo nội dung public mới từ dữ liệu WordPress đã import, không ghi đè nội dung đã quản trị.';

    public function handle(): int
    {
        $this->syncLandings();
        $this->syncProjects();
        $this->syncPosts();

        $this->components->info('Đã khởi tạo nội dung public từ dữ liệu legacy.');

        return self::SUCCESS;
    }

    private function syncLandings(): void
    {
        $categories = $this->syncCategories('danh-muc-dich-vu', LandingCategory::class)
            ->union($this->syncCategories('landing-cate', LandingCategory::class));

        $this->legacyItems(['service', 'landing'])->each(function (ContentItem $item) use ($categories): void {
            $category = $this->categoryFor($item, ['danh-muc-dich-vu', 'landing-cate'], $categories);

            $landing = Landing::query()->firstOrCreate(
                ['legacy_content_item_id' => $item->id],
                $this->contentAttributes($item, [
                    'landing_category_id' => $category?->id,
                    'is_featured' => false,
                    'sort_order' => $item->menu_order ?? 0,
                ]),
            );

            $this->syncSlug($landing, $item->slug);
        });
    }

    private function syncProjects(): void
    {
        $categories = $this->syncCategories('us_portfolio_category', ProjectCategory::class);

        $this->legacyItems('us_portfolio')->each(function (ContentItem $item) use ($categories): void {
            $category = $this->categoryFor($item, 'us_portfolio_category', $categories);

            $project = Project::query()->firstOrCreate(
                ['legacy_content_item_id' => $item->id],
                $this->contentAttributes($item, [
                    'project_category_id' => $category?->id,
                    'client_name' => null,
                    'industry' => null,
                    'video_url' => null,
                    'completed_at' => null,
                    'is_featured' => false,
                    'sort_order' => $item->menu_order ?? 0,
                ]),
            );

            $this->syncSlug($project, $item->slug);
        });
    }

    private function syncPosts(): void
    {
        $categories = $this->syncCategories('category', PostCategory::class);

        $this->legacyItems('post')->each(function (ContentItem $item) use ($categories): void {
            $post = Post::query()->firstOrCreate(
                ['legacy_content_item_id' => $item->id],
                $this->contentAttributes($item),
            );

            $this->syncSlug($post, $item->slug);

            if (! $post->wasRecentlyCreated) {
                return;
            }

            $categoryIds = $item->terms
                ->filter(fn ($term): bool => $term->taxonomy?->key === 'category')
                ->map(fn ($term): ?int => $categories->get($term->id)?->id)
                ->filter()
                ->values()
                ->all();

            $post->categories()->syncWithoutDetaching($categoryIds);
        });
    }

    /**
     * @return Collection<int, LandingCategory|ProjectCategory|PostCategory>
     */
    private function syncCategories(string $taxonomyKey, string $model): Collection
    {
        $taxonomy = Taxonomy::query()->where('key', $taxonomyKey)->first();

        if (! $taxonomy) {
            return new Collection;
        }

        return $taxonomy->terms()
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function ($term) use ($model): array {
                $category = $model::query()->firstOrCreate(
                    ['legacy_term_id' => $term->id],
                    [
                        'name' => $term->name,
                        'description' => $term->description,
                        'is_active' => true,
                        'sort_order' => $term->term_order ?? 0,
                    ],
                );

                $this->syncSlug($category, $term->slug);

                return [$term->id => $category];
            });
    }

    /**
     * @return Collection<int, ContentItem>
     */
    private function legacyItems(string|array $type): Collection
    {
        return ContentItem::query()
            ->where('source', 'wordpress')
            ->whereIn('type', (array) $type)
            ->with('terms.taxonomy')
            ->orderBy('id')
            ->get();
    }

    private function categoryFor(ContentItem $item, string|array $taxonomyKey, Collection $categories): LandingCategory|ProjectCategory|PostCategory|null
    {
        $term = $item->terms->first(fn ($term): bool => in_array($term->taxonomy?->key, (array) $taxonomyKey, true));

        return $term ? $categories->get($term->id) : null;
    }

    private function contentAttributes(ContentItem $item, array $additional = []): array
    {
        return [
            'legacy_media_asset_id' => MediaAsset::query()
                ->where('source', 'wordpress')
                ->where('source_id', $item->featured_media_source_id)
                ->value('id'),
            'title' => $item->title,
            'excerpt' => $item->excerpt ?: Str::limit(trim(strip_tags((string) $item->body)), 260),
            'body' => $item->body,
            'status' => $item->status,
            'seo_title' => $item->seo_title,
            'seo_description' => $item->seo_description,
            'published_at' => $item->published_at,
            ...$additional,
        ];
    }

    private function syncSlug(Model $model, ?string $slug): void
    {
        if (blank($slug)) {
            return;
        }

        $model->slug = $slug;
        $model->save();
    }
}
