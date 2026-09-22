<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Route;

class MenuItem extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (MenuItem $item): void {
            if ($item->menu_id !== null || $item->parent_id === null) {
                return;
            }

            $item->menu_id = static::query()
                ->whereKey($item->parent_id)
                ->value('menu_id');
        });
    }

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'linked_source_id' => 'integer',
            'position' => 'integer',
        ];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('position');
    }

    /**
     * Resolve the public URL from the configured route or content reference.
     * The URL field is only a literal URL when the item is explicitly custom.
     */
    public function getLinkAttribute(): string
    {
        return match ($this->linkType()) {
            'intro' => Intro::query()->published()->with('slugs')->find($this->linked_source_id)?->url ?? '#',
            'route' => $this->routeLink(),
            'service' => $this->serviceLink(),
            'service-category' => $this->serviceCategoryLink(),
            'project' => $this->projectLink(),
            'project-category' => $this->projectCategoryLink(),
            'post' => $this->postLink(),
            'post-category' => $this->postCategoryLink(),
            'product' => $this->productLink(),
            'product-category' => $this->productCategoryLink(),
            'page' => $this->pageLink(),
            'custom' => $this->url ?: '#',
            default => $this->url ?: '#',
        };
    }

    private function linkType(): string
    {
        return match ($this->linked_source_type) {
            'native_intro' => 'intro',
            'native_route' => 'route',
            'native_service' => 'service',
            'native_service_category' => 'service-category',
            'native_project' => 'project',
            'native_project_category' => 'project-category',
            'native_post' => 'post',
            'native_post_category' => 'post-category',
            'native_page' => 'custom',
            'custom' => 'custom',
            Service::class => 'service',
            Project::class => 'project',
            Post::class => 'post',
            Product::class => 'product',
            ProductCategory::class => 'product-category',
            default => 'custom',
        };
    }

    private function routeLink(): string
    {
        $routeName = (string) $this->url;

        if ($routeName === '') {
            return '#';
        }

        if (! Route::has($routeName)) {
            $path = '/'.trim((string) parse_url($routeName, PHP_URL_PATH), '/');

            foreach (self::menuRouteNames() as $candidate) {
                if ($path === '/'.trim((string) parse_url(route($candidate), PHP_URL_PATH), '/')) {
                    $routeName = $candidate;

                    break;
                }
            }
        }

        return $routeName !== '' && Route::has($routeName)
            ? route($routeName)
            : '#';
    }

    /** @return array<int, string> */
    private static function menuRouteNames(): array
    {
        return [
            'home',
            'about',
            'services.index',
            'solutions.index',
            'projects.index',
            'posts.index',
            'products.index',
            'contact',
            'search',
        ];
    }

    private function serviceLink(): string
    {
        $service = Service::query()
            ->published()
            ->with('slugs')
            ->find($this->linked_source_id);

        return $service?->slug ? route('slug.show', ['slug' => $service->slug]) : '#';
    }

    private function serviceCategoryLink(): string
    {
        $category = ServiceCategory::query()
            ->where('is_active', true)
            ->find($this->linked_source_id);

        return $category ? route('services.category', ['category' => $category->slug]) : '#';
    }

    private function projectLink(): string
    {
        $project = Project::query()
            ->published()
            ->with('slugs')
            ->find($this->linked_source_id);

        return $project ? route('projects.show', ['slug' => $project->slug]) : '#';
    }

    private function projectCategoryLink(): string
    {
        $category = ProjectCategory::query()
            ->where('is_active', true)
            ->find($this->linked_source_id);

        return $category ? route('projects.category', ['slug' => $category->slug]) : '#';
    }

    private function postLink(): string
    {
        $post = Post::query()
            ->published()
            ->with('slugs')
            ->find($this->linked_source_id);

        return $post ? route('slug.show', ['slug' => $post->slug]) : '#';
    }

    private function postCategoryLink(): string
    {
        $category = PostCategory::query()
            ->where('is_active', true)
            ->find($this->linked_source_id);

        return $category ? route('posts.category', ['slug' => $category->slug]) : '#';
    }

    private function productLink(): string
    {
        $product = Product::query()->published()->with('slugs')->find($this->linked_source_id);

        return $product ? route('products.show', ['slug' => $product->slug]) : '#';
    }

    private function productCategoryLink(): string
    {
        $category = ProductCategory::query()->active()->find($this->linked_source_id);

        return $category ? route('products.category', ['slug' => $category->slug]) : '#';
    }

    private function pageLink(): string
    {
        return $this->url ?: '#';
    }
}
