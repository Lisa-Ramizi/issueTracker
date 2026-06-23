<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function delete(User $user, Comment $comment): bool
    {
        $comment->loadMissing('issue.project');

        return $user->id === $comment->issue->project->user_id
            || $comment->author_name === $user->name;
    }
}
