@extends('layouts.app')

@section('title', $issue->title)

@section('content')
    <div class="page-header">
        <div>
            <p class="meta" style="margin: 0 0 0.25rem;">
                <a href="{{ route('projects.show', $issue->project) }}">{{ $issue->project->name }}</a>
            </p>
            <h1 class="page-title">{{ $issue->title }}</h1>
            <div class="meta-row" style="margin-top: 0.5rem;">
                <span class="badge badge-{{ $issue->status }}">{{ str_replace('_', ' ', $issue->status) }}</span>
                <span class="badge badge-{{ $issue->priority }}">{{ $issue->priority }}</span>
                @if ($issue->due_date)
                    <span class="meta">Due {{ $issue->due_date->format('M j, Y') }}</span>
                @endif
            </div>
        </div>
        <div class="actions">
            <a href="{{ route('issues.edit', $issue) }}" class="btn btn--ghost">Edit</a>
            <a href="{{ route('projects.show', $issue->project) }}" class="btn btn--ghost">Back</a>
        </div>
    </div>

    @if ($issue->description)
        <div class="card">
            <p style="margin: 0;">{{ $issue->description }}</p>
        </div>
    @endif

    <section class="section">
        <h2 class="section-title">Tags</h2>
        <div class="card">
            <div class="meta-row" style="margin-top: 0;">
                @forelse ($issue->tags as $tag)
                    @include('components.tag-chip', ['tag' => $tag, 'issue' => $issue, 'removable' => true])
                @empty
                    <span class="meta">No tags yet.</span>
                @endforelse
            </div>

            @if ($availableTags->isNotEmpty())
                <form method="POST" action="{{ route('issues.tags.attach', [$issue, $availableTags->first()]) }}" class="inline-form" style="margin-top: 1rem;" id="attach-tag-form">
                    @csrf
                    <div class="form-group">
                        <label for="tag_select">Add tag</label>
                        <select id="tag_select" class="form-control" onchange="document.getElementById('attach-tag-form').action = this.value">
                            @foreach ($availableTags as $tag)
                                <option value="{{ route('issues.tags.attach', [$issue, $tag]) }}">{{ $tag->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn--primary btn--sm">Attach</button>
                </form>
            @endif
        </div>
    </section>

    <section class="section">
        <h2 class="section-title">Comments ({{ $issue->comments->count() }})</h2>

        <div class="card">
            @forelse ($issue->comments as $comment)
                @include('components.comment', ['comment' => $comment])
            @empty
                <p class="meta" style="margin: 0;">No comments yet. Be the first!</p>
            @endforelse
        </div>

        <div class="card" style="margin-top: 1rem;">
            <form method="POST" action="{{ route('issues.comments.store', $issue) }}">
                @csrf

                <div class="form-group">
                    <label for="author_name">Your Name</label>
                    <input type="text" id="author_name" name="author_name" class="form-control" value="{{ old('author_name') }}" required>
                    @error('author_name')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label for="body">Comment</label>
                    <textarea id="body" name="body" class="form-control" rows="3" required>{{ old('body') }}</textarea>
                    @error('body')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="btn btn--primary">Add Comment</button>
            </form>
        </div>
    </section>
@endsection
