<div id="issue-list-meta" data-total="{{ $issues->total() }}" hidden></div>

@if ($issues->isEmpty())
    <div class="empty-state card">
        <h2>No issues match</h2>
        <p>Try adjusting your filters or create a new issue.</p>
    </div>
@else
    <div class="kanban__cards">
        @foreach ($issues as $issue)
            @include('components.issue-card', ['issue' => $issue])
        @endforeach
    </div>

    <div class="pagination">
        {{ $issues->links() }}
    </div>
@endif
