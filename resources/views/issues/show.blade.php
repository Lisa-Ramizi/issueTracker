@extends('layouts.app')

@section('title', $issue->title)

@section('content')
    <div id="issue-show"
         data-issue-id="{{ $issue->id }}"
         data-comments-url="{{ route('issues.comments.index', $issue) }}"
         data-comment-store-url="{{ route('issues.comments.store', $issue) }}">

        <div class="issue-hero">
            <div class="page-header" style="margin-bottom: 0;">
                <div>
                    <p class="meta" style="margin: 0 0 0.35rem;">
                        <a href="{{ route('projects.show', $issue->project) }}">{{ $issue->project->name }}</a>
                    </p>
                    <h1 class="page-title">{{ $issue->title }}</h1>
                    <div class="meta-pills" style="margin-top: 0.75rem;">
                        <span class="badge badge-{{ $issue->status }}">{{ str_replace('_', ' ', $issue->status) }}</span>
                        <span class="badge badge-{{ $issue->priority }}">{{ $issue->priority }}</span>
                        @if ($issue->due_date)
                            <span class="meta-pill">Due {{ $issue->due_date->format('M j, Y') }}</span>
                        @endif
                    </div>
                </div>
                <div class="actions">
                    @can('update', $issue)
                        <a href="{{ route('issues.edit', $issue) }}" class="btn btn--ghost">Edit</a>
                    @endcan
                    <a href="{{ route('projects.show', $issue->project) }}" class="btn btn--ghost">Board</a>
                    @can('delete', $issue)
                        <form method="POST" action="{{ route('issues.destroy', $issue) }}" class="inline-form" style="margin: 0;" onsubmit="return confirm('Delete this issue?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn--danger">Delete</button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>

        @if ($issue->description)
            <div class="card">
                <p style="margin: 0; line-height: 1.6;">{{ $issue->description }}</p>
            </div>
        @endif

        <section class="section">
            <h2 class="section-title">Activity</h2>
            <div class="card">
                <ul id="activity-list" class="activity-timeline">
                    @forelse ($activities as $activity)
                        @include('components.activity-item', ['activity' => $activity])
                    @empty
                        <li class="meta" id="activity-empty" style="list-style: none; margin: 0;">No activity yet.</li>
                    @endforelse
                </ul>
            </div>
        </section>

        <section class="section">
            <h2 class="section-title">Tags</h2>
            <div class="card">
                <div class="meta-row" style="margin-top: 0;" id="issue-tags">
                    @forelse ($issue->tags as $tag)
                        <span class="tag-chip-wrapper" data-tag-id="{{ $tag->id }}">
                            <span class="tag-chip" style="background-color: {{ $tag->color ?? '#F5D0DC' }}">{{ $tag->name }}</span>
                            <button type="button" class="tag-chip__remove" data-tag-id="{{ $tag->id }}" title="Remove tag">&times;</button>
                        </span>
                    @empty
                        <span class="meta" id="tags-empty">No tags yet.</span>
                    @endforelse
                </div>

                <div class="inline-form" id="tag-attach-group" style="margin-top: 1rem;{{ $availableTags->isEmpty() ? ' display: none;' : '' }}">
                    <div class="form-group">
                        <label for="tag-attach-select">Add tag</label>
                        <select id="tag-attach-select" class="form-control">
                            <option value="">Choose a tag…</option>
                            @foreach ($availableTags as $tag)
                                <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button" id="tag-attach-btn" class="btn btn--primary btn--sm">Attach</button>
                </div>
            </div>
        </section>

        <section class="section">
            <h2 class="section-title">Members</h2>
            <div class="card">
                <div class="meta-row" style="margin-top: 0;" id="issue-members">
                    @forelse ($issue->users as $member)
                        <span class="member-chip-wrapper" data-user-id="{{ $member->id }}">
                            <span class="member-chip">{{ $member->name }}</span>
                            <button type="button" class="member-chip__remove" data-user-id="{{ $member->id }}" title="Remove member">&times;</button>
                        </span>
                    @empty
                        <span class="meta" id="members-empty">No members assigned yet.</span>
                    @endforelse
                </div>

                <div class="inline-form" id="member-attach-group" style="margin-top: 1rem;{{ $availableUsers->isEmpty() ? ' display: none;' : '' }}">
                    <div class="form-group">
                        <label for="member-attach-select">Add member</label>
                        <select id="member-attach-select" class="form-control">
                            <option value="">Choose a member…</option>
                            @foreach ($availableUsers as $member)
                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button" id="member-attach-btn" class="btn btn--primary btn--sm">Assign</button>
                </div>
            </div>
        </section>

        <section class="section">
            <h2 class="section-title">Comments (<span id="comment-count">{{ $issue->comments_count }}</span>)</h2>

            <div class="card">
                <div id="comments-list"></div>
                <button type="button" id="comments-load-more" class="btn btn--ghost btn--sm" style="display: none; margin-top: 0.75rem;">Load more</button>
            </div>

            <div class="card" style="margin-top: 1rem;">
                <div id="comment-success" class="flash flash--success" style="display: none; margin-bottom: 1rem;">Comment added.</div>

                <form id="comment-form" novalidate>
                    <p class="meta" style="margin: 0 0 1rem;">Commenting as <strong>{{ auth()->user()->name }}</strong></p>

                    <div class="form-group">
                        <label for="body">Comment</label>
                        <textarea id="body" name="body" class="form-control" rows="3"></textarea>
                    </div>

                    <button type="submit" class="btn btn--primary">Add Comment</button>
                </form>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/issue-show.js'])
@endpush
