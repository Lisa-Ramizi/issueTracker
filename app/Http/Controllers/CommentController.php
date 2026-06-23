<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Issue;
use App\Models\IssueActivity;
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
            'data' => $comments->map(fn (Comment $comment) => $this->commentPayload($request, $comment)),
            'next_page_url' => $comments->nextPageUrl(),
        ]);
    }

    public function store(StoreCommentRequest $request, Issue $issue): JsonResponse|RedirectResponse
    {
        $comment = $issue->comments()->create([
            ...$request->validated(),
            'author_name' => $request->user()->name,
        ]);

        $activity = IssueActivity::log($issue, $request->user(), 'comment_added');

        if ($request->expectsJson()) {
            return response()->json([
                'comment' => $this->commentPayload($request, $comment),
                'activity' => $activity->load('user')->toTimelineArray(),
            ], 201);
        }

        return redirect()
            ->route('issues.show', $issue)
            ->with('success', 'Comment added.');
    }

    public function destroy(Request $request, Comment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json(['deleted' => true]);
    }

    private function commentPayload(Request $request, Comment $comment): array
    {
        return [
            'id' => $comment->id,
            'author_name' => $comment->author_name,
            'body' => $comment->body,
            'created_at' => $comment->created_at->toIso8601String(),
            'created_at_human' => $comment->created_at->diffForHumans(),
            'can_delete' => $request->user()->can('delete', $comment),
        ];
    }
}
