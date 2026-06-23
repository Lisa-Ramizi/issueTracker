@extends('layouts.app')

@section('title', 'Edit '.$issue->title)

@section('content')
    <div class="page-header">
        <div>
            <p class="meta" style="margin: 0 0 0.25rem;">
                <a href="{{ route('projects.show', $issue->project) }}">{{ $issue->project->name }}</a>
            </p>
            <h1 class="page-title">Edit Issue</h1>
        </div>
    </div>

    <div class="card form-card">
        <form method="POST" action="{{ route('issues.update', $issue) }}" novalidate>
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" class="form-control" value="{{ old('title', $issue->title) }}">
                @error('title')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="4">{{ old('description', $issue->description) }}</textarea>
                @error('description')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" class="form-control">
                    @foreach (['open', 'in_progress', 'closed'] as $status)
                        <option value="{{ $status }}" @selected(old('status', $issue->status) === $status)>{{ str_replace('_', ' ', $status) }}</option>
                    @endforeach
                </select>
                @error('status')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="priority">Priority</label>
                <select id="priority" name="priority" class="form-control">
                    @foreach (['low', 'medium', 'high'] as $priority)
                        <option value="{{ $priority }}" @selected(old('priority', $issue->priority) === $priority)>{{ $priority }}</option>
                    @endforeach
                </select>
                @error('priority')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="due_date">Due Date</label>
                <input type="date" id="due_date" name="due_date" class="form-control" value="{{ old('due_date', $issue->due_date?->format('Y-m-d')) }}">
                @error('due_date')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            @if ($tags->isNotEmpty())
                <div class="form-group">
                    <label>Tags</label>
                    <div class="checkbox-group">
                        @foreach ($tags as $tag)
                            <label class="checkbox-label">
                                <input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}"
                                    @checked(in_array($tag->id, old('tag_ids', $issue->tags->pluck('id')->all())))>
                                @include('components.tag-chip', ['tag' => $tag])
                            </label>
                        @endforeach
                    </div>
                    @error('tag_ids')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            @endif

            <div class="actions">
                <button type="submit" class="btn btn--primary">Save Changes</button>
                <a href="{{ route('issues.show', $issue) }}" class="btn btn--ghost">Cancel</a>
            </div>
        </form>
    </div>

    <div class="section">
        @can('delete', $issue)
            <form method="POST" action="{{ route('issues.destroy', $issue) }}" onsubmit="return confirm('Delete this issue?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn--danger">Delete Issue</button>
            </form>
        @endcan
    </div>
@endsection
