<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\IssueActivity;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class IssueTagController extends Controller
{
    public function attach(Request $request, Issue $issue, Tag $tag): JsonResponse|RedirectResponse
    {
        $issue->tags()->syncWithoutDetaching([$tag->id]);

        $activity = IssueActivity::log($issue, $request->user(), 'tag_attached', subject: $tag->name);

        if ($request->expectsJson()) {
            return response()->json([
                'attached' => true,
                'tag' => [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'color' => $tag->color,
                ],
                'activity' => $activity->load('user')->toTimelineArray(),
            ]);
        }

        return redirect()
            ->route('issues.show', $issue)
            ->with('success', 'Tag attached.');
    }

    public function detach(Request $request, Issue $issue, Tag $tag): JsonResponse|RedirectResponse
    {
        $issue->tags()->detach($tag->id);

        $activity = IssueActivity::log($issue, $request->user(), 'tag_detached', subject: $tag->name);

        if ($request->expectsJson()) {
            return response()->json([
                'detached' => true,
                'activity' => $activity->load('user')->toTimelineArray(),
            ]);
        }

        return redirect()
            ->route('issues.show', $issue)
            ->with('success', 'Tag removed.');
    }
}
