<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IssueActivity extends Model
{
    protected $fillable = [
        'issue_id',
        'user_id',
        'action',
        'subject',
        'from_value',
        'to_value',
    ];

    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function log(
        Issue $issue,
        ?User $user,
        string $action,
        ?string $subject = null,
        ?string $from = null,
        ?string $to = null,
    ): self {
        return $issue->activities()->create([
            'user_id' => $user?->id,
            'action' => $action,
            'subject' => $subject,
            'from_value' => $from,
            'to_value' => $to,
        ]);
    }

    public function getMessageAttribute(): string
    {
        return match ($this->action) {
            'created' => 'Created this issue',
            'status_changed' => sprintf(
                'Moved from %s to %s',
                self::labelStatus($this->from_value),
                self::labelStatus($this->to_value),
            ),
            'priority_changed' => sprintf(
                'Changed priority from %s to %s',
                self::labelPriority($this->from_value),
                self::labelPriority($this->to_value),
            ),
            'tag_attached' => sprintf('Added tag %s', $this->subject),
            'tag_detached' => sprintf('Removed tag %s', $this->subject),
            'member_assigned' => sprintf('Assigned %s', $this->subject),
            'member_unassigned' => sprintf('Unassigned %s', $this->subject),
            'comment_added' => 'Added a comment',
            default => 'Updated this issue',
        };
    }

    public static function labelStatus(?string $status): string
    {
        return match ($status) {
            'open' => 'To Do',
            'in_progress' => 'In Progress',
            'closed' => 'Completed',
            default => $status ?? 'Unknown',
        };
    }

    public static function labelPriority(?string $priority): string
    {
        return $priority ? ucfirst($priority) : 'Unknown';
    }

    public function toTimelineArray(): array
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
            'user_name' => $this->user?->name ?? 'System',
            'created_at_human' => $this->created_at->diffForHumans(),
        ];
    }
}
