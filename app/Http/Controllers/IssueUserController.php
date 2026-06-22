<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class IssueUserController extends Controller
{
    public function attach(Request $request, Issue $issue, User $user): JsonResponse|RedirectResponse
    {
        $issue->users()->syncWithoutDetaching([$user->id]);

        if ($request->expectsJson()) {
            return response()->json([
                'attached' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                ],
            ]);
        }

        return redirect()
            ->route('issues.show', $issue)
            ->with('success', 'Member assigned.');
    }

    public function detach(Request $request, Issue $issue, User $user): JsonResponse|RedirectResponse
    {
        $issue->users()->detach($user->id);

        if ($request->expectsJson()) {
            return response()->json(['detached' => true]);
        }

        return redirect()
            ->route('issues.show', $issue)
            ->with('success', 'Member removed.');
    }
}
