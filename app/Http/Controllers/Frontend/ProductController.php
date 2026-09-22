<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Support\Media\MediaUrl;
use App\Support\Seo\FrontendSeoBuilder;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(private readonly FrontendSeoBuilder $seo) {}

    public function index(Request $request): View
    {
        $data = $this->listingData(request: $request);

        return view('frontend.products.index', $data + [
            'seo' => $this->seo->listing(
                'Sản phẩm | '.$this->seo->siteName(),
                'Thiết bị và vật tư PCCC chính hãng cho các công trình.',
                route('products.index'),
                image: $data['heroImageUrl'],
            ),
        ]);
    }

    public function categoryBySlug(string $slug, Request $request): View
    {
        $category = ProductCategory::query()
            ->active()
            ->whereHas('slugs', fn (Builder $query) => $query->where('slug', $slug))
            ->with('slugs')
            ->firstOrFail();
        $data = $this->listingData($category, $request);

        return view('frontend.products.index', $data + [
            'seo' => $this->seo->productCategory($category),
        ]);
    }

    public function showBySlug(string $slug): View
    {
        $product = Product::query()
            ->published()
            ->whereHas('slugs', fn (Builder $query) => $query->where('slug', $slug))
            ->firstOrFail();

        $product->load([
            'category',
            'curatorMedia',
            'slugs',
            'faqs' => fn ($query) => $query->active()->ordered(),
            'tags',
        ]);
        $product->setAttribute('image_url', MediaUrl::resolve($product->curatorMedia));
        $galleryImages = $this->galleryImages($product->gallery);

        return view('frontend.products.show', [
            'product' => $product,
            'galleryImages' => $galleryImages,
            'faqItems' => $product->faqs,
            'seo' => $this->seo->product($product),
        ]);
    }

    /** @return array<string, mixed> */
    private function listingData(?ProductCategory $activeCategory = null, ?Request $request = null): array
    {
        $request ??= request();
        $sort = $request->string('sort')->value();
        $sort = in_array($sort, ['latest', 'featured', 'title'], true) ? $sort : 'latest';
        $productsQuery = Product::query()->published()->with(['category', 'curatorMedia', 'slugs', 'tags']);

        if ($activeCategory) {
            $productsQuery->whereBelongsTo($activeCategory, 'category');
        }

        match ($sort) {
            'featured' => $productsQuery->orderByDesc('is_featured')->orderBy('sort_order')->orderByDesc('published_at'),
            'title' => $productsQuery->orderBy('title'),
            default => $productsQuery->orderByDesc('published_at')->orderBy('sort_order'),
        };

        $products = $this->withImages($productsQuery->paginate(12)->withQueryString());
        $hero = $products->first();

        return [
            'activeCategory' => $activeCategory,
            'categories' => ProductCategory::query()
                ->active()
                ->withCount(['products' => fn (Builder $query) => $query->published()])
                ->with('slugs')
                ->orderBy('sort_order')
                ->get()
                ->each(fn (ProductCategory $category) => $category->setAttribute('public_url', route('products.category', ['slug' => $category->slug]))),
            'products' => $products,
            'heroImageUrl' => $hero?->image_url,
            'pageTitle' => $activeCategory?->name ?? 'Sản phẩm PCCC',
            'pageDescription' => $activeCategory?->description ?: 'Thiết bị báo cháy, chữa cháy, sprinkler và vật tư đồng bộ cho từng loại công trình.',
            'sort' => $sort,
            'sortOptions' => [
                'latest' => 'Mới nhất',
                'featured' => 'Nổi bật',
                'title' => 'Tên A–Z',
            ],
        ];
    }

    private function withImages(iterable $products): iterable
    {
        foreach ($products as $product) {
            $product->setAttribute('image_url', MediaUrl::resolve($product->curatorMedia));
        }

        return $products;
    }

    /** @return list<string> */
    private function galleryImages(?array $gallery): array
    {
        $ids = collect($gallery ?? [])
            ->map(fn (mixed $item): mixed => is_array($item) ? ($item['id'] ?? $item['media_id'] ?? null) : $item)
            ->filter(fn (mixed $id): bool => is_numeric($id))
            ->map(fn (mixed $id): int => (int) $id)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return [];
        }

        return $ids
            ->map(fn (int $id): ?string => MediaUrl::resolve(Media::query()->find($id)))
            ->filter()
            ->values()
            ->all();
    }
}
