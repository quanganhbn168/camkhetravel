<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Intro;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\ServiceCategory;
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

    private function resolve(Request $request, string $slug)
    {
        $defaultLocale = app(LanguageCatalog::class)->defaultCode();
        $sluggable = Slug::query()
            ->where('slug', $slug)
            ->where('locale', $defaultLocale)
            ->with('sluggable')
            ->first()?->sluggable;

        return match (true) {
            $sluggable instanceof Intro => redirect()->to($sluggable->url, 301),
            $sluggable instanceof Service => app(ServiceController::class)->show($sluggable),
            $sluggable instanceof ServiceCategory => app(ServiceController::class)->redirectCategory($sluggable),
            $sluggable instanceof Project => redirect()->to(LocalizedUrl::project($sluggable), 301),
            $sluggable instanceof ProjectCategory => redirect()->to(LocalizedUrl::projectCategory($sluggable), 301),
            $sluggable instanceof Post => app(PostController::class)->show($sluggable),
            $sluggable instanceof PostCategory => redirect()->to(LocalizedUrl::postCategory($sluggable), 301),
            $sluggable instanceof Product => redirect()->to(LocalizedUrl::product($sluggable), 301),
            $sluggable instanceof ProductCategory => redirect()->to(LocalizedUrl::productCategory($sluggable), 301),
            default => abort(404),
        };
    }
}
