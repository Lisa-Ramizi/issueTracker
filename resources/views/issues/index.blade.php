@extends('layouts.app')

@section('title', $project->name.' — Issues')

@section('content')
    <div class="page-header">
        <div>
            <p class="meta" style="margin: 0 0 0.25rem;">
                <a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a>
            </p>
            <h1 class="page-title">All Issues</h1>
            <p class="page-subtitle" id="issues-result-count">{{ $issues->total() }} result{{ $issues->total() === 1 ? '' : 's' }}</p>
        </div>
        <div class="actions">
            <a href="{{ route('projects.show', $project) }}" class="btn btn--ghost">Board</a>
            @can('update', $project)
                <a href="{{ route('projects.issues.create', $project) }}" class="btn btn--primary">+ New Issue</a>
            @endcan
        </div>
    </div>

    <div class="view-tabs">
        <a href="{{ route('projects.show', $project) }}" class="view-tab">Board</a>
        <span class="view-tab view-tab--active">List</span>
    </div>

    @include('issues._filters', ['project' => $project, 'tags' => $tags])

    <div id="issue-list" data-total="{{ $issues->total() }}">
        @include('issues._list', ['issues' => $issues])
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/issues-filter.js'])
@endpush
