<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIssueRequest;
use App\Http\Requests\UpdateIssueRequest;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
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
        $tags = Tag::orderBy('name')->get();

        return view('issues.create', compact('project', 'tags'));
    }

    public function store(StoreIssueRequest $request, Project $project): RedirectResponse
    {
        $data = $request->validated();
        $tagIds = $data['tag_ids'] ?? [];
        unset($data['tag_ids']);

        $issue = $project->issues()->create($data);

        if ($tagIds) {
            $issue->tags()->sync($tagIds);
        }

        return redirect()
            ->route('issues.show', $issue)
            ->with('success', 'Issue created.');
    }

    public function show(Issue $issue): View
    {
        $issue->load(['tags', 'project', 'comments', 'users'])->loadCount('comments');

        $tags = Tag::orderBy('name')->get();
        $availableTags = $tags->whereNotIn('id', $issue->tags->pluck('id'));

        $users = User::orderBy('name')->get();
        $availableUsers = $users->whereNotIn('id', $issue->users->pluck('id'));

        return view('issues.show', compact('issue', 'tags', 'availableTags', 'availableUsers'));
    }

    public function edit(Issue $issue): View
    {
        $issue->load('tags');
        $tags = Tag::orderBy('name')->get();

        return view('issues.edit', compact('issue', 'tags'));
    }

    public function update(UpdateIssueRequest $request, Issue $issue): RedirectResponse
    {
        $data = $request->validated();
        $tagIds = $data['tag_ids'] ?? [];
        unset($data['tag_ids']);

        $issue->update($data);
        $issue->tags()->sync($tagIds);

        return redirect()
            ->route('issues.show', $issue)
            ->with('success', 'Issue updated.');
    }

    public function destroy(Issue $issue): RedirectResponse
    {
        $project = $issue->project;
        $issue->delete();

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Issue deleted.');
    }
}
