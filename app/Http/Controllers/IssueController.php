<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIssueRequest;
use App\Http\Requests\UpdateIssueRequest;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IssueController extends Controller
{
    public function index(Request $request, Project $project): View
    {
        $issues = $project->issues()
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

        $tags = Tag::orderBy('name')->get();

        return view('issues.index', compact('project', 'issues', 'tags'));
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
        $issue->load(['tags', 'project', 'comments' => fn ($q) => $q->latest()]);

        $tags = Tag::orderBy('name')->get();
        $availableTags = $tags->whereNotIn('id', $issue->tags->pluck('id'));

        return view('issues.show', compact('issue', 'tags', 'availableTags'));
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
