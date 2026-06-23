@extends('layouts.app')

@section('title', $project->name)

@section('content')
    <div id="project-board" data-can-update="{{ auth()->user()->can('update', $project) ? '1' : '0' }}">
        <div class="page-header">
            <div>
                <h1 class="page-title">{{ $project->name }}</h1>
                @if ($project->description)
                    <p class="page-subtitle">{{ $project->description }}</p>
                @endif
                <div class="meta-pills">
                    @if ($project->start_date)
                        <span class="meta-pill">Start {{ $project->start_date->format('M j, Y') }}</span>
                    @endif
                    @if ($project->deadline)
                        <span class="meta-pill">Deadline {{ $project->deadline->format('M j, Y') }}</span>
                    @endif
                    <span class="meta-pill meta-pill--status">In Progress</span>
                    <span class="meta-pill">{{ $project->issues_count }} issue{{ $project->issues_count === 1 ? '' : 's' }}</span>
                </div>
            </div>
                <div class="actions">
                    @can('update', $project)
                        <a href="{{ route('projects.issues.create', $project) }}" class="btn btn--primary">+ New Issue</a>
                        <a href="{{ route('projects.edit', $project) }}" class="btn btn--ghost">Edit Project</a>
                    @endcan
                    @can('delete', $project)
                        <form method="POST" action="{{ route('projects.destroy', $project) }}" class="inline-form" style="margin: 0;" onsubmit="return confirm('Delete this project and all its issues?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn--danger">Delete</button>
                        </form>
                    @endcan
                </div>
        </div>

        <div class="view-tabs">
            <span class="view-tab view-tab--active">Board</span>
            <a href="{{ route('projects.issues.index', $project) }}" class="view-tab">List</a>
        </div>

        @include('issues._filters', ['project' => $project, 'tags' => $tags])

        <div id="issue-search-panel" class="issue-search-panel" hidden>
            <p class="meta" id="issues-result-count" style="margin: 0 0 1rem;">0 results</p>
            <div id="issue-list" data-total="0"></div>
        </div>

        <div id="project-board-kanban">
        @php
            $columns = [
                'open' => ['label' => 'To Do', 'class' => 'open'],
                'in_progress' => ['label' => 'In Progress', 'class' => 'in_progress'],
                'closed' => ['label' => 'Completed', 'class' => 'closed'],
            ];
            $hasIssues = $issuesByStatus->flatten()->isNotEmpty();
        @endphp

        @if (! $hasIssues)
            <div class="empty-state card">
                <h2>No issues yet</h2>
                <p>Start fresh — add your first issue to the board.</p>
                <a href="{{ route('projects.issues.create', $project) }}" class="btn btn--primary" style="margin-top: 1rem;">Add Issue</a>
            </div>
        @else
            @can('update', $project)
                <p class="meta kanban-hint">Drag cards between columns to update status.</p>
            @else
                <p class="meta kanban-hint">View only — you can browse this board but cannot move cards.</p>
            @endcan
            <div class="kanban">
                @foreach ($columns as $status => $col)
                    @php $colIssues = $issuesByStatus->get($status, collect()); @endphp
                    <div class="kanban__col kanban__col--{{ $col['class'] }}">
                        <div class="kanban__header">
                            <h2 class="kanban__title">
                                <span class="kanban__dot kanban__dot--{{ $col['class'] }}"></span>
                                {{ $col['label'] }}
                            </h2>
                            <span class="kanban__count">{{ $colIssues->count() }}</span>
                        </div>
                        <div class="kanban__cards" data-status="{{ $status }}">
                            @forelse ($colIssues as $issue)
                                <div class="kanban__card"
                                     @if (auth()->user()->can('update', $project)) draggable="true" @endif
                                     data-issue-id="{{ $issue->id }}"
                                     data-status="{{ $issue->status }}">
                                    @include('components.issue-card', ['issue' => $issue])
                                </div>
                            @empty
                                <p class="meta kanban__empty" style="margin: 0; padding: 0.5rem;">No issues here</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        </div>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/project-show.js', 'resources/js/issues-filter.js'])
@endpush
