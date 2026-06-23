<a href="{{ route('issues.show', $issue) }}" class="issue-card">
    <div class="issue-card__priority">
        <span class="badge badge-{{ $issue->priority }}">{{ $issue->priority }}</span>
    </div>
    <h3 class="issue-card__title">{{ $issue->title }}</h3>
    @if ($issue->description)
        <p class="issue-card__note">{{ Str::limit($issue->description, 100) }}</p>
    @endif
    @php
        $progress = \App\Models\Issue::progressForStatus($issue->status);
    @endphp
    <div class="issue-card__progress">
        <div class="issue-card__progress-bar">
            <div class="issue-card__progress-fill" style="width: {{ $progress }}%"></div>
        </div>
        <span class="issue-card__progress-label">{{ $progress }}%</span>
    </div>
    <div class="issue-card__footer">
        <div class="issue-card__tags">
            @if ($issue->relationLoaded('tags'))
                @foreach ($issue->tags->take(3) as $tag)
                    @include('components.tag-chip', ['tag' => $tag])
                @endforeach
            @endif
        </div>
        @if ($issue->due_date)
            <span class="issue-card__due">{{ $issue->due_date->format('M j') }}</span>
        @endif
    </div>
</a>
