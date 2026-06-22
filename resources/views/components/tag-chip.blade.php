<span class="tag-chip" style="background-color: {{ $tag->color ?? '#D8D8D0' }}">
    {{ $tag->name }}
</span>
@if (isset($removable) && isset($issue))
    <form method="POST" action="{{ route('issues.tags.detach', [$issue, $tag]) }}" style="display: inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="tag-chip__remove" title="Remove tag">&times;</button>
    </form>
@endif
