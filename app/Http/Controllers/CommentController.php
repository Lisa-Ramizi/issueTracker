<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Issue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request, Issue $issue): JsonResponse
    {
        $comments = $issue->comments()
            ->latest()
            ->paginate(10);

        return response()->json([
            'data' => $comments->map(fn ($comment) => [
                'author_name' => $comment->author_name,
                'body' => $comment->body,
                'created_at' => $comment->created_at->toIso8601String(),
                'created_at_human' => $comment->created_at->diffForHumans(),
            ]),
            'next_page_url' => $comments->nextPageUrl(),
        ]);
    }

    public function store(StoreCommentRequest $request, Issue $issue): JsonResponse|RedirectResponse
    {
        $comment = $issue->comments()->create($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'comment' => [
                    'author_name' => $comment->author_name,
                    'body' => $comment->body,
                    'created_at' => $comment->created_at->toIso8601String(),
                    'created_at_human' => $comment->created_at->diffForHumans(),
                ],
            ], 201);
        }

        return redirect()
            ->route('issues.show', $issue)
            ->with('success', 'Comment added.');
    }
}
