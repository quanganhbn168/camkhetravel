<?php

namespace App\Models;

use App\Support\Localization\LocalizedUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Route;

class MenuItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'legacy_meta' => 'array',
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
            'route' => $this->routeLink(),
            'service' => $this->serviceLink(),
            'project' => $this->projectLink(),
            'post' => $this->postLink(),
            'page' => $this->pageLink(),
            'custom' => $this->url ?: '#',
            default => $this->url ?: '#',
        };
    }

    private function linkType(): string
    {
        return match ($this->linked_source_type) {
            'native_route' => 'route',
            'native_service' => 'service',
            'native_project' => 'project',
            'native_post' => 'post',
            'native_page' => 'page',
            'custom' => 'custom',
            Landing::class => 'service',
            Project::class => 'project',
            Post::class => 'post',
            ContentItem::class => 'page',
            default => 'legacy',
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
            ? LocalizedUrl::route($routeName)
            : '#';
    }

    /** @return array<int, string> */
    private static function menuRouteNames(): array
    {
        return [
            'home',
            'about',
            'services.index',
            'pricing.index',
            'projects.index',
            'posts.index',
            'contact',
            'search',
        ];
    }

    private function serviceLink(): string
    {
        $service = Landing::query()
            ->published()
            ->with('slugs')
            ->find($this->linked_source_id);

        return $service?->slug ? LocalizedUrl::slug($service->slug) : '#';
    }

    private function projectLink(): string
    {
        $project = Project::query()
            ->published()
            ->with(['slugs', 'legacyContent'])
            ->find($this->linked_source_id);

        return $project ? LocalizedUrl::project($project) : '#';
    }

    private function postLink(): string
    {
        $post = Post::query()
            ->published()
            ->with(['slugs', 'legacyContent'])
            ->find($this->linked_source_id);

        return $post ? LocalizedUrl::post($post) : '#';
    }

    private function pageLink(): string
    {
        $page = ContentItem::query()
            ->where('type', 'page')
            ->where('status', 'published')
            ->find($this->linked_source_id);

        return filled($page?->slug) ? LocalizedUrl::slug($page->slug) : '#';
    }
}
