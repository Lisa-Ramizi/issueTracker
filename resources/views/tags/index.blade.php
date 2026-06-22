@extends('layouts.app')

@section('title', 'Tags')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Tags</h1>
            <p class="page-subtitle">{{ $tags->count() }} label{{ $tags->count() === 1 ? '' : 's' }} across your issues</p>
        </div>
    </div>

    <div class="card form-card" style="margin-bottom: 1.5rem; max-width: 480px;">
        <h2 class="section-title" style="font-size: 1rem; margin-bottom: 1.25rem;">Create Tag</h2>
        <form method="POST" action="{{ route('tags.store') }}">
            @csrf

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="color">Color (hex)</label>
                <input type="text" id="color" name="color" class="form-control" value="{{ old('color', '#F5D0DC') }}" placeholder="#F5D0DC">
                @error('color')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn btn--primary">Add Tag</button>
        </form>
    </div>

    @if ($tags->isEmpty())
        <div class="empty-state card">
            <h2>No tags yet</h2>
            <p>Create your first tag above.</p>
        </div>
    @else
        <ul class="card-list card-list--grid">
            @foreach ($tags as $tag)
                <li>
                    <div class="card" style="display: flex; justify-content: space-between; align-items: center;">
                        @include('components.tag-chip', ['tag' => $tag])
                        <span class="meta">{{ $tag->issues_count }} issue{{ $tag->issues_count === 1 ? '' : 's' }}</span>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
@endsection
