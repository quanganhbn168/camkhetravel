<?php

namespace App\Support\Content;

use App\Models\Post;
use App\Support\Media\MediaUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Bài viết liên quan theo thẻ: bài chung nhiều thẻ hơn đứng trước, rồi đến bài tạo gần đây hơn (created_at).
 * Thẻ chỉ dùng để nối nội dung cùng chủ đề (liên kết nội bộ), không có trang riêng cho từng thẻ.
 */
final class RelatedPosts
{
    /**
     * @param  iterable<int>|\Illuminate\Support\Collection<int, int>  $tagIds
     * @return Collection<int, Post>
     */
    public static function forTags(iterable $tagIds, ?int $exceptPostId = null, int $limit = 3): Collection
    {
        $tagIds = collect($tagIds)->filter()->unique()->values();

        if ($tagIds->isEmpty()) {
            return new Collection;
        }

        $posts = Post::query()
            ->published()
            ->when($exceptPostId, fn (Builder $query) => $query->whereKeyNot($exceptPostId))
            ->whereHas('tags', fn (Builder $query) => $query->whereKey($tagIds->all())->where('is_active', true))
            ->withCount(['tags as shared_tags_count' => fn (Builder $query) => $query->whereKey($tagIds->all())])
            ->with(['category', 'curatorMedia', 'slugs'])
            ->orderByDesc('shared_tags_count')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        $posts->each(fn (Post $post) => $post->setAttribute('image_url', MediaUrl::resolve($post->curatorMedia)));

        return $posts;
    }
}
