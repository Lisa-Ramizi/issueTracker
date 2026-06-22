@extends('layouts.app')

@section('title', 'Edit '.$project->name)

@section('content')
    <div class="page-header">
        <h1 class="page-title">Edit Project</h1>
        <p class="page-subtitle">{{ $project->name }}</p>
    </div>

    <div class="card form-card">
        <form method="POST" action="{{ route('projects.update', $project) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $project->name) }}" required>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="4">{{ old('description', $project->description) }}</textarea>
                @error('description')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="start_date">Start Date</label>
                <input type="date" id="start_date" name="start_date" class="form-control" value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}">
                @error('start_date')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="deadline">Deadline</label>
                <input type="date" id="deadline" name="deadline" class="form-control" value="{{ old('deadline', $project->deadline?->format('Y-m-d')) }}">
                @error('deadline')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="actions">
                <button type="submit" class="btn btn--primary">Save Changes</button>
                <a href="{{ route('projects.show', $project) }}" class="btn btn--ghost">Cancel</a>
            </div>
        </form>
    </div>

    <div class="section">
        <form method="POST" action="{{ route('projects.destroy', $project) }}" onsubmit="return confirm('Delete this project and all its issues?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn--danger">Delete Project</button>
        </form>
    </div>
@endsection
