<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\LandingPage;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Post $post): RedirectResponse
    {
        return $this->storeComment($request, $post);
    }

    public function storeService(StoreCommentRequest $request, Service $service): RedirectResponse
    {
        return $this->storeComment($request, $service, requiresRating: true);
    }

    public function storeLandingPage(StoreCommentRequest $request, LandingPage $landingPage): RedirectResponse
    {
        return $this->storeComment($request, $landingPage, requiresRating: true);
    }

    public function storeProject(StoreCommentRequest $request, Project $project): RedirectResponse
    {
        return $this->storeComment($request, $project, requiresRating: true);
    }

    private function storeComment(StoreCommentRequest $request, Post|Service|LandingPage|Project $commentable, bool $requiresRating = false): RedirectResponse
    {
        abort_unless($commentable->status === 'published' && (! $commentable->published_at || $commentable->published_at->isPast()), 404);

        if ($requiresRating) {
            $request->validate([
                'rating' => ['required', 'integer', 'between:1,5'],
            ]);
        }

        $data = $request->validated();

        $commentable->comments()->create([
            'author_name' => trim(strip_tags($data['author_name'])),
            'author_email' => $data['author_email'] ?? null,
            'rating' => $data['rating'] ?? null,
            'body' => trim(strip_tags($data['body'])),
            'status' => Comment::STATUS_PENDING,
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 512, ''),
        ]);

        return back()->with('success', 'Cảm ơn bạn. Bình luận đã được gửi và đang chờ duyệt.');
    }
}
