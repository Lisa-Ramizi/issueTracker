<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IssueActivityController extends Controller
{
    public function index(Request $request, Issue $issue): JsonResponse
    {
        $activities = $issue->activities()
            ->with('user')
            ->latest()
            ->paginate(15);

        return response()->json([
            'data' => $activities->map(fn ($activity) => $activity->toTimelineArray()),
            'next_page_url' => $activities->nextPageUrl(),
        ]);
    }
}
