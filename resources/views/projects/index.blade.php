@extends('layouts.app')

@section('title', 'Projects')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Projects</h1>
            <p class="page-subtitle">{{ $projects->total() }} project{{ $projects->total() === 1 ? '' : 's' }}</p>
        </div>
        <a href="{{ route('projects.create') }}" class="btn btn--primary">New Project</a>
    </div>

    @if ($projects->isEmpty())
        <div class="empty-state card">
            <h2>No projects yet</h2>
            <p>Start fresh — create your first project.</p>
            <a href="{{ route('projects.create') }}" class="btn btn--primary" style="margin-top: 1rem;">Create Project</a>
        </div>
    @else
        <ul class="card-list">
            @foreach ($projects as $project)
                <li class="card-list__item">
                    <article class="card">
                        <div class="actions" style="justify-content: space-between; align-items: flex-start;">
                            <div>
                                <a href="{{ route('projects.show', $project) }}" style="text-decoration: none; font-weight: 600; font-size: 1.1rem;">
                                    {{ $project->name }}
                                </a>
                                @if ($project->description)
                                    <p class="meta" style="margin: 0.35rem 0 0;">{{ Str::limit($project->description, 140) }}</p>
                                @endif
                            </div>
                            <span class="meta">{{ $project->issues_count }} issue{{ $project->issues_count === 1 ? '' : 's' }}</span>
                        </div>
                        @if ($project->start_date || $project->deadline)
                            <div class="meta-row">
                                @if ($project->start_date)
                                    <span>Start {{ $project->start_date->format('M j, Y') }}</span>
                                @endif
                                @if ($project->deadline)
                                    <span>Deadline {{ $project->deadline->format('M j, Y') }}</span>
                                @endif
                            </div>
                        @endif
                    </article>
                </li>
            @endforeach
        </ul>

        <div class="pagination">
            {{ $projects->links() }}
        </div>
    @endif
@endsection
