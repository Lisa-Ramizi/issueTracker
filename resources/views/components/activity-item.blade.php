<li class="activity-item" data-activity-id="{{ $activity->id ?? '' }}">
    <div class="activity-item__dot"></div>
    <div class="activity-item__body">
        <p class="activity-item__message">{{ $activity->message }}</p>
        <p class="activity-item__meta">
            <span class="activity-item__user">{{ $activity->user?->name ?? 'System' }}</span>
            <span class="activity-item__time">{{ $activity->created_at->diffForHumans() }}</span>
        </p>
    </div>
</li>
