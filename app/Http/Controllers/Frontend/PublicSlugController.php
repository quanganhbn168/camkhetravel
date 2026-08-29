<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Landing;
use App\Models\LandingCategory;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Slug;
use App\Support\Localization\LanguageCatalog;
use App\Support\Localization\LocalizedUrl;
use Illuminate\Http\Request;

class PublicSlugController extends Controller
{
    /**
     * Resolve every public native resource from the global morph slug table.
     */
    public function __invoke(Request $request, string $slug)
    {
        return $this->resolve($request, $slug);
    }

    public function localized(Request $request, string $locale, string $slug)
    {
        return $this->resolve($request, $slug);
    }

    private function resolve(Request $request, string $slug)
    {
        $locale = app()->getLocale();
        $defaultLocale = app(LanguageCatalog::class)->defaultCode();
        $sluggable = Slug::query()
            ->where('slug', $slug)
            ->whereIn('locale', array_unique([$locale, $defaultLocale]))
            ->orderByRaw('CASE WHEN locale = ? THEN 0 ELSE 1 END', [$locale])
            ->with('sluggable')
            ->first()?->sluggable;

        return match (true) {
            $sluggable instanceof Landing => app(LandingController::class)->show($sluggable),
            $sluggable instanceof LandingCategory => app(LandingController::class)->category($sluggable),
            $sluggable instanceof Project => redirect()->to(LocalizedUrl::project($sluggable), 301),
            $sluggable instanceof ProjectCategory => redirect()->to(LocalizedUrl::projectCategory($sluggable), 301),
            $sluggable instanceof Post => app(PostController::class)->show($sluggable),
            $sluggable instanceof PostCategory => redirect()->to(LocalizedUrl::postCategory($sluggable), 301),
            default => abort(404),
        };
    }
}
