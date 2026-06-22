@extends('layouts.app')

@section('title', $project->name)

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $project->name }}</h1>
            @if ($project->description)
                <p class="page-subtitle">{{ $project->description }}</p>
            @endif
            <div class="meta-row" style="margin-top: 0.5rem;">
                @if ($project->start_date)
                    <span class="meta">Start {{ $project->start_date->format('M j, Y') }}</span>
                @endif
                @if ($project->deadline)
                    <span class="meta">Deadline {{ $project->deadline->format('M j, Y') }}</span>
                @endif
                <span class="meta">{{ $project->issues_count }} issue{{ $project->issues_count === 1 ? '' : 's' }}</span>
            </div>
        </div>
        <div class="actions">
            <a href="{{ route('projects.issues.create', $project) }}" class="btn btn--primary">New Issue</a>
            <a href="{{ route('projects.issues.index', $project) }}" class="btn btn--ghost">Filter Issues</a>
            <a href="{{ route('projects.edit', $project) }}" class="btn btn--ghost">Edit</a>
        </div>
    </div>

    <h2 class="section-title">Issues</h2>

    @if ($issues->isEmpty())
        <div class="empty-state card">
            <h2>No issues yet</h2>
            <p>Start fresh 🍃</p>
            <a href="{{ route('projects.issues.create', $project) }}" class="btn btn--primary" style="margin-top: 1rem;">Add Issue</a>
        </div>
    @else
        @foreach ($issues as $issue)
            @include('components.issue-card', ['issue' => $issue])
        @endforeach

        <div class="pagination">
            {{ $issues->links() }}
        </div>
    @endif
@endsection
