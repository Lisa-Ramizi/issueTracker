@extends('layouts.app')

@section('title', 'New Project')

@section('content')
    <div class="page-header">
        <h1 class="page-title">New Project</h1>
        <p class="page-subtitle">Give it a name and optional timeline.</p>
    </div>

    <div class="card form-card">
        <form method="POST" action="{{ route('projects.store') }}" novalidate>
            @csrf

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}">
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                @error('description')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="start_date">Start Date</label>
                <input type="date" id="start_date" name="start_date" class="form-control" value="{{ old('start_date') }}">
                @error('start_date')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="deadline">Deadline</label>
                <input type="date" id="deadline" name="deadline" class="form-control" value="{{ old('deadline') }}">
                @error('deadline')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="actions">
                <button type="submit" class="btn btn--primary">Create Project</button>
                <a href="{{ route('projects.index') }}" class="btn btn--ghost">Cancel</a>
            </div>
        </form>
    </div>
@endsection
