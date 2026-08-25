<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BniArticle;
use App\Models\BniReaction;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BniInteractionController extends Controller
{
    public function comment(Request $request, BniArticle $article): RedirectResponse
    {
        abort_unless($article->status === 'published', 404);
        $user = $this->member($request);
        $data = $request->validate(['body' => ['required', 'string', 'min:3', 'max:3000']]);

        $article->comments()->create([
            'user_id' => $user->id,
            'author_name' => $user->name,
            'author_email' => $user->email,
            'body' => trim(strip_tags($data['body'])),
            'status' => Comment::STATUS_PENDING,
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 512, ''),
        ]);

        return back()->with('success', 'Bình luận của anh/chị đã được gửi và chờ duyệt.');
    }

    public function react(Request $request, BniArticle $article): RedirectResponse
    {
        abort_unless($article->status === 'published', 404);
        $user = $this->member($request);
        $data = $request->validate(['reaction' => ['required', 'in:like,love,celebrate']]);

        BniReaction::query()->updateOrCreate([
            'user_id' => $user->id,
            'reactable_type' => $article->getMorphClass(),
            'reactable_id' => $article->id,
        ], ['reaction' => $data['reaction']]);

        return back();
    }

    private function member(Request $request): User
    {
        $user = $request->user();

        abort_unless($user && $user->hasAnyRole(['super_admin', 'bni_admin', 'bni_chapter_manager', 'bni_member']), 403);

        return $user;
    }
}
