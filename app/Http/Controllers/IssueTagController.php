<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class IssueTagController extends Controller
{
    public function attach(Request $request, Issue $issue, Tag $tag): JsonResponse|RedirectResponse
    {
        $issue->tags()->syncWithoutDetaching([$tag->id]);

        if ($request->expectsJson()) {
            return response()->json([
                'attached' => true,
                'tag' => [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'color' => $tag->color,
                ],
            ]);
        }

        return redirect()
            ->route('issues.show', $issue)
            ->with('success', 'Tag attached.');
    }

    public function detach(Request $request, Issue $issue, Tag $tag): JsonResponse|RedirectResponse
    {
        $issue->tags()->detach($tag->id);

        if ($request->expectsJson()) {
            return response()->json(['detached' => true]);
        }

        return redirect()
            ->route('issues.show', $issue)
            ->with('success', 'Tag removed.');
    }
}
