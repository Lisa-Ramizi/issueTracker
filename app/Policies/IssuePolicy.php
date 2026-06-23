<?php

namespace App\Policies;

use App\Models\Issue;
use App\Models\User;

class IssuePolicy
{
    public function update(User $user, Issue $issue): bool
    {
        return $user->id === $issue->project->user_id;
    }

    public function delete(User $user, Issue $issue): bool
    {
        return $user->id === $issue->project->user_id;
    }
}
