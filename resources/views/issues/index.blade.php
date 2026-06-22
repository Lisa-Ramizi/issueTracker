@extends('layouts.app')

@section('title', $project->name.' — Issues')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $project->name }}</h1>
            <p class="page-subtitle" id="issues-result-count">Issues · {{ $issues->total() }} result{{ $issues->total() === 1 ? '' : 's' }}</p>
        </div>
        <div class="actions">
            <a href="{{ route('projects.show', $project) }}" class="btn btn--ghost">Back to Project</a>
            <a href="{{ route('projects.issues.create', $project) }}" class="btn btn--primary">New Issue</a>
        </div>
    </div>

    <form method="GET" action="{{ route('projects.issues.index', $project) }}" class="filter-bar card" id="issues-filter-form">
        <div class="form-group">
            <label for="search">Search</label>
            <input type="text" id="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Title or description" autocomplete="off">
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status" class="form-control">
                <option value="">All</option>
                @foreach (['open', 'in_progress', 'closed'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ str_replace('_', ' ', $status) }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="priority">Priority</label>
            <select id="priority" name="priority" class="form-control">
                <option value="">All</option>
                @foreach (['low', 'medium', 'high'] as $priority)
                    <option value="{{ $priority }}" @selected(request('priority') === $priority)>{{ $priority }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="tag_id">Tag</label>
            <select id="tag_id" name="tag_id" class="form-control">
                <option value="">All</option>
                @foreach ($tags as $tag)
                    <option value="{{ $tag->id }}" @selected(request('tag_id') == $tag->id)>{{ $tag->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn--primary">Filter</button>
        <a href="{{ route('projects.issues.index', $project) }}" class="btn btn--ghost" id="issues-filter-clear">Clear</a>
    </form>

    <div id="issue-list" data-total="{{ $issues->total() }}">
        @include('issues._list', ['issues' => $issues])
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/issues-filter.js'])
@endpush
