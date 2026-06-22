<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Issue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Issue $issue): JsonResponse|RedirectResponse
    {
        $comment = $issue->comments()->create($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'comment' => [
                    'author_name' => $comment->author_name,
                    'body' => $comment->body,
                    'created_at' => $comment->created_at->toIso8601String(),
                ],
            ], 201);
        }

        return redirect()
            ->route('issues.show', $issue)
            ->with('success', 'Comment added.');
    }
}
