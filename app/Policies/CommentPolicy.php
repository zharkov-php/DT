<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    public function create(User $user, Comment $comment): bool
    {
        $task = $comment->task ?? Task::with('project.members')->find($comment->task_id);

        if (!$task) {
            return false;
        }

        $project = $task->project;

        if (!$project) {
            return false;
        }

        if ($project->owner_id === $user->id) {
            return true;
        }

        $member = $project->members->firstWhere('id', $user->id);
        return $member !== null;
    }

    public function update(User $user, Comment $comment): bool
    {
        return $comment->user_id === $user->id && $comment->created_at->diffInMinutes(now()) <= 10;
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $comment->user_id === $user->id && $comment->created_at->diffInMinutes(now()) <= 10;
    }
}
