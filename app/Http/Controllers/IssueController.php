<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIssueRequest;
use App\Http\Requests\UpdateIssueRequest;
use App\Http\Requests\UpdateIssueStatusRequest;
use App\Models\Issue;
use App\Models\IssueActivity;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class IssueController extends Controller
{
    public function index(Request $request, Project $project): View|Response
    {
        $issues = $this->filteredIssues($request, $project);
        $tags = Tag::orderBy('name')->get();

        if ($request->ajax()) {
            return response()->view('issues._list', compact('issues'));
        }

        return view('issues.index', compact('project', 'issues', 'tags'));
    }

    private function filteredIssues(Request $request, Project $project)
    {
        return $project->issues()
            ->with(['tags', 'project'])
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->priority, fn ($q, $priority) => $q->where('priority', $priority))
            ->when($request->tag_id, fn ($q, $tagId) => $q->whereHas('tags', fn ($q) => $q->where('tags.id', $tagId)))
            ->when($request->search, fn ($q, $search) => $q->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }

    public function create(Project $project): View
    {
        $this->authorize('update', $project);

        $tags = Tag::orderBy('name')->get();

        return view('issues.create', compact('project', 'tags'));
    }

    public function store(StoreIssueRequest $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);
        $data = $request->validated();
        $tagIds = $data['tag_ids'] ?? [];
        unset($data['tag_ids']);

        $issue = $project->issues()->create($data);

        if ($tagIds) {
            $issue->tags()->sync($tagIds);
        }

        IssueActivity::log($issue, $request->user(), 'created');

        return redirect()
            ->route('issues.show', $issue)
            ->with('success', 'Issue created.');
    }

    public function show(Issue $issue): View
    {
        $issue->load(['tags', 'project', 'comments', 'users'])->loadCount('comments');

        $activities = $issue->activities()->with('user')->latest()->take(20)->get();

        $tags = Tag::orderBy('name')->get();
        $availableTags = $tags->whereNotIn('id', $issue->tags->pluck('id'));

        $users = User::orderBy('name')->get();
        $availableUsers = $users->whereNotIn('id', $issue->users->pluck('id'));

        return view('issues.show', compact('issue', 'tags', 'availableTags', 'availableUsers', 'activities'));
    }

    public function edit(Issue $issue): View
    {
        $this->authorize('update', $issue);

        $issue->load('tags');
        $tags = Tag::orderBy('name')->get();

        return view('issues.edit', compact('issue', 'tags'));
    }

    public function update(UpdateIssueRequest $request, Issue $issue): RedirectResponse
    {
        $this->authorize('update', $issue);

        $data = $request->validated();
        $tagIds = $data['tag_ids'] ?? [];
        unset($data['tag_ids']);

        $oldStatus = $issue->status;
        $oldPriority = $issue->priority;
        $oldTagIds = $issue->tags()->pluck('tags.id');

        $issue->update($data);

        if ($oldStatus !== $issue->status) {
            IssueActivity::log($issue, $request->user(), 'status_changed', from: $oldStatus, to: $issue->status);
        }

        if ($oldPriority !== $issue->priority) {
            IssueActivity::log($issue, $request->user(), 'priority_changed', from: $oldPriority, to: $issue->priority);
        }

        $issue->tags()->sync($tagIds);

        $newTagIds = collect($tagIds);
        $addedTagIds = $newTagIds->diff($oldTagIds);
        $removedTagIds = $oldTagIds->diff($newTagIds);

        if ($addedTagIds->isNotEmpty() || $removedTagIds->isNotEmpty()) {
            $tagNames = Tag::whereIn('id', $addedTagIds->merge($removedTagIds))->pluck('name', 'id');

            foreach ($addedTagIds as $tagId) {
                IssueActivity::log($issue, $request->user(), 'tag_attached', subject: $tagNames[$tagId] ?? 'tag');
            }

            foreach ($removedTagIds as $tagId) {
                IssueActivity::log($issue, $request->user(), 'tag_detached', subject: $tagNames[$tagId] ?? 'tag');
            }
        }

        return redirect()
            ->route('issues.show', $issue)
            ->with('success', 'Issue updated.');
    }

    public function updateStatus(UpdateIssueStatusRequest $request, Issue $issue): JsonResponse
    {
        $this->authorize('update', $issue);

        $newStatus = $request->validated('status');

        if ($issue->status === $newStatus) {
            return response()->json([
                'status' => $newStatus,
                'progress' => Issue::progressForStatus($newStatus),
            ]);
        }

        $oldStatus = $issue->status;
        $issue->update(['status' => $newStatus]);

        $activity = IssueActivity::log($issue, $request->user(), 'status_changed', from: $oldStatus, to: $newStatus);

        return response()->json([
            'status' => $newStatus,
            'progress' => Issue::progressForStatus($newStatus),
            'activity' => $activity->load('user')->toTimelineArray(),
        ]);
    }

    public function destroy(Issue $issue): RedirectResponse
    {
        $this->authorize('delete', $issue);

        $project = $issue->project;
        $issue->delete();

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Issue deleted.');
    }
}
