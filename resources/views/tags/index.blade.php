@extends('layouts.app')

@section('title', 'Tags')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Tags</h1>
            <p class="page-subtitle">{{ $tags->count() }} tag{{ $tags->count() === 1 ? '' : 's' }}</p>
        </div>
    </div>

    <div class="card" style="margin-bottom: 1.5rem;">
        <h2 class="section-title">Create Tag</h2>
        <form method="POST" action="{{ route('tags.store') }}">
            @csrf

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="color">Color (hex)</label>
                <input type="text" id="color" name="color" class="form-control" value="{{ old('color', '#9CB88D') }}" placeholder="#9CB88D">
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
        <ul class="card-list">
            @foreach ($tags as $tag)
                <li class="card-list__item">
                    <div class="card actions" style="justify-content: space-between; align-items: center;">
                        @include('components.tag-chip', ['tag' => $tag])
                        <span class="meta">{{ $tag->issues_count }} issue{{ $tag->issues_count === 1 ? '' : 's' }}</span>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
@endsection
