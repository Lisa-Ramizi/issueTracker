@extends('layouts.app')

@section('title', 'Projects')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Projects</h1>
            <p class="page-subtitle">{{ $projects->total() }} project{{ $projects->total() === 1 ? '' : 's' }} on your board</p>
        </div>
        <a href="{{ route('projects.create') }}" class="btn btn--primary">+ New Project</a>
    </div>

    @if ($projects->isEmpty())
        <div class="empty-state card">
            <h2>No projects yet</h2>
            <p>Plant your first project and watch it grow.</p>
            <a href="{{ route('projects.create') }}" class="btn btn--primary" style="margin-top: 1rem;">Create Project</a>
        </div>
    @else
        <ul class="card-list card-list--grid">
            @foreach ($projects as $project)
                <li>
                    <article class="card">
                        <a href="{{ route('projects.show', $project) }}" class="project-card__title">
                            {{ $project->name }}
                        </a>
                        @if ($project->description)
                            <p class="meta" style="margin: 0.5rem 0 0;">{{ Str::limit($project->description, 120) }}</p>
                        @endif
                        <div class="meta-pills" style="margin-top: 0.85rem;">
                            <span class="meta-pill">{{ $project->issues_count }} issue{{ $project->issues_count === 1 ? '' : 's' }}</span>
                            @if ($project->user_id === auth()->id())
                                <span class="meta-pill meta-pill--status">Yours</span>
                            @else
                                <span class="meta-pill">{{ $project->user->name }}</span>
                            @endif
                            @if ($project->deadline)
                                <span class="meta-pill">Due {{ $project->deadline->format('M j') }}</span>
                            @endif
                        </div>
                    </article>
                </li>
            @endforeach
        </ul>

        <div class="pagination">
            {{ $projects->links() }}
        </div>
    @endif
@endsection
