<?php

namespace App\Http\Controllers;

use App\Models\Project;

class ProjectController extends Controller
{
    public function index()
    {
        return view('placeholder', ['title' => 'Projects — Index']);
    }

    public function create()
    {
        return view('placeholder', ['title' => 'Projects — Create']);
    }

    public function store()
    {
        return view('placeholder', ['title' => 'Projects — Store']);
    }

    public function show(Project $project)
    {
        return view('placeholder', ['title' => 'Projects — Show #'.$project->id]);
    }

    public function edit(Project $project)
    {
        return view('placeholder', ['title' => 'Projects — Edit #'.$project->id]);
    }

    public function update(Project $project)
    {
        return view('placeholder', ['title' => 'Projects — Update #'.$project->id]);
    }

    public function destroy(Project $project)
    {
        return view('placeholder', ['title' => 'Projects — Destroy #'.$project->id]);
    }
}
