<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Intro;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Slug;
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
        $sluggable = Slug::query()
            ->where('slug', $slug)
            ->with('sluggable')
            ->first()?->sluggable;

        $sluggable?->loadMissing('slugs');
        $request->attributes->set('frontend.content_type', $sluggable?->getMorphClass());

        return match (true) {
            $sluggable instanceof Intro => redirect()->to($sluggable->url, 301),
            $sluggable instanceof Service => app(ServiceController::class)->show($sluggable),
            $sluggable instanceof ServiceCategory => app(ServiceController::class)->redirectCategory($sluggable),
            $sluggable instanceof Post => app(PostController::class)->show($sluggable),
            $sluggable instanceof PostCategory => redirect()->route('posts.category', ['slug' => $sluggable->slug], 301),
            $sluggable instanceof Product => redirect()->route('products.show', ['slug' => $sluggable->slug], 301),
            $sluggable instanceof ProductCategory => redirect()->route('products.category', ['slug' => $sluggable->slug], 301),
            default => abort(404),
        };
    }
}
