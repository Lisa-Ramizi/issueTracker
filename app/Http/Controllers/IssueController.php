<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Project;

class IssueController extends Controller
{
    public function index(Project $project)
    {
        return view('placeholder', ['title' => 'Issues — Index (Project #'.$project->id.')']);
    }

    public function create(Project $project)
    {
        return view('placeholder', ['title' => 'Issues — Create (Project #'.$project->id.')']);
    }

    public function store(Project $project)
    {
        return view('placeholder', ['title' => 'Issues — Store (Project #'.$project->id.')']);
    }

    public function show(Issue $issue)
    {
        return view('placeholder', ['title' => 'Issues — Show #'.$issue->id]);
    }

    public function edit(Issue $issue)
    {
        return view('placeholder', ['title' => 'Issues — Edit #'.$issue->id]);
    }

    public function update(Issue $issue)
    {
        return view('placeholder', ['title' => 'Issues — Update #'.$issue->id]);
    }

    public function destroy(Issue $issue)
    {
        return view('placeholder', ['title' => 'Issues — Destroy #'.$issue->id]);
    }
}
