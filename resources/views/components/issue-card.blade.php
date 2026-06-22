<article class="card">
    <div class="actions" style="justify-content: space-between; align-items: flex-start;">
        <div>
            <a href="{{ route('issues.show', $issue) }}" style="text-decoration: none; font-weight: 600;">
                {{ $issue->title }}
            </a>
            @if ($issue->description)
                <p class="meta" style="margin: 0.35rem 0 0;">{{ Str::limit($issue->description, 120) }}</p>
            @endif
        </div>
        <div class="actions">
            <span class="badge badge-{{ $issue->status }}">{{ str_replace('_', ' ', $issue->status) }}</span>
            <span class="badge badge-{{ $issue->priority }}">{{ $issue->priority }}</span>
        </div>
    </div>

    <div class="meta-row">
        @if ($issue->due_date)
            <span>Due {{ $issue->due_date->format('M j, Y') }}</span>
        @endif
        @if ($issue->relationLoaded('tags'))
            @foreach ($issue->tags as $tag)
                @include('components.tag-chip', ['tag' => $tag])
            @endforeach
        @endif
    </div>
</article>
