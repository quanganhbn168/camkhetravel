<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Post $post): RedirectResponse
    {
        abort_unless($post->status === 'published' && (! $post->published_at || $post->published_at->isPast()), 404);

        $data = $request->validated();

        $post->comments()->create([
            'author_name' => trim(strip_tags($data['author_name'])),
            'author_email' => $data['author_email'] ?? null,
            'body' => trim(strip_tags($data['body'])),
            'status' => Comment::STATUS_PENDING,
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 512, ''),
        ]);

        return back()->with('success', 'Cảm ơn bạn. Bình luận đã được gửi và đang chờ duyệt.');
    }
}
