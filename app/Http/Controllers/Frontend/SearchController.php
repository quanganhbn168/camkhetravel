<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Landing;
use App\Models\Post;
use App\Support\Frontend\MediaUrl;
use App\Support\Localization\LocalizedUrl;
use App\Support\Seo\FrontendSeoBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __construct(private readonly FrontendSeoBuilder $seo) {}

    public function __invoke(Request $request): View
    {
        $keyword = mb_substr(trim((string) $request->query('q', '')), 0, 100);
        $services = $this->services($keyword);
        $posts = $this->posts($keyword);
        $seo = $this->seo->listing(
            $keyword === '' ? 'Tìm kiếm' : 'Tìm kiếm: '.$keyword,
            'Tìm kiếm dịch vụ và bài viết tại '.$this->seo->siteName().'.',
            LocalizedUrl::route('search'),
        );

        return view('frontend.search.index', compact('keyword', 'services', 'posts', 'seo'));
    }

    private function services(string $keyword): LengthAwarePaginator
    {
        $services = Landing::query()
            ->published()
            ->with(['category', 'curatorMedia'])
            ->when($keyword === '', fn (Builder $query) => $query->whereRaw('1 = 0'), fn (Builder $query) => $this->applyKeyword($query, $keyword))
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->paginate(9, ['*'], 'services_page')
            ->withQueryString();

        $services->getCollection()->each(fn (Landing $service) => $service->setAttribute(
            'image_url',
            MediaUrl::resolve($service->curatorMedia),
        ));

        return $services;
    }

    private function posts(string $keyword): LengthAwarePaginator
    {
        $posts = Post::query()
            ->published()
            ->with(['categories', 'curatorMedia'])
            ->when($keyword === '', fn (Builder $query) => $query->whereRaw('1 = 0'), fn (Builder $query) => $this->applyKeyword($query, $keyword))
            ->orderByDesc('published_at')
            ->paginate(9, ['*'], 'posts_page')
            ->withQueryString();

        $posts->getCollection()->each(fn (Post $post) => $post->setAttribute(
            'image_url',
            MediaUrl::resolve($post->curatorMedia),
        ));

        return $posts;
    }

    private function applyKeyword(Builder $query, string $keyword): void
    {
        $like = '%'.addcslashes($keyword, '%_\\').'%';

        $query->where(function (Builder $query) use ($like): void {
            $query->where('title', 'like', $like)
                ->orWhere('excerpt', 'like', $like)
                ->orWhere('body', 'like', $like);
        });
    }
}
