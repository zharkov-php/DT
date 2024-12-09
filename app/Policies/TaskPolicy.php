<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function create(User $user, Task $task): bool
    {
        $project = $task->project;

        if ($project->owner_id === $user->id) {
            return true;
        }

        $member = $project->members()->where('user_id', $user->id)->first();

        return $member && $member->pivot->role === 'Editor';
    }

    public function delete(User $user, Task $task): bool
    {
        return $task->project->owner_id === $user->id;
    }

    public function update(User $user, Task $task): bool
    {
        $project = $task->project;

        if ($project->owner_id === $user->id) {
            return true;
        }

        $member = $project->members()->where('user_id', $user->id)->first();

        return $member && $member->pivot->role === 'Editor';
    }

}
