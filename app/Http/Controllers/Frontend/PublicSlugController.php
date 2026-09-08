<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\LandingPage;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Slug;
use App\Support\Landing\LandingRegistry;
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

    private function resolve(Request $request, string $slug)
    {
        $defaultLocale = app(LanguageCatalog::class)->defaultCode();
        $landingTemplateKey = null;
        $sluggable = Slug::query()
            ->where('slug', $slug)
            ->where('locale', $defaultLocale)
            ->with('sluggable')
            ->first()?->sluggable;

        if (! $sluggable && ($landingTemplateKey = LandingRegistry::templateForSlug($slug))) {
            $sluggable = Slug::query()
                ->whereIn('slug', LandingRegistry::slugsForTemplate($landingTemplateKey))
                ->where('locale', $defaultLocale)
                ->with('sluggable')
                ->get()
                ->pluck('sluggable')
                ->first(fn (mixed $model): bool => $model instanceof LandingPage || $model instanceof Service);
        }

        return match (true) {
            $sluggable instanceof Service => app(ServiceController::class)->show($sluggable, $landingTemplateKey),
            $sluggable instanceof ServiceCategory => app(ServiceController::class)->redirectCategory($sluggable),
            $sluggable instanceof LandingPage => app(LandingController::class)->show($sluggable),
            $sluggable instanceof Project => redirect()->to(LocalizedUrl::project($sluggable), 301),
            $sluggable instanceof ProjectCategory => redirect()->to(LocalizedUrl::projectCategory($sluggable), 301),
            $sluggable instanceof Post => app(PostController::class)->show($sluggable),
            $sluggable instanceof PostCategory => redirect()->to(LocalizedUrl::postCategory($sluggable), 301),
            default => abort(404),
        };
    }
}
