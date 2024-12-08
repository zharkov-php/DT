<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    public function delete(User $user, Task $task): bool
    {
        return $task->project->owner_id === $user->id;
    }

    public function update(User $user, Task $task): bool
    {
        $member = $task->project->members()->where('user_id', $user->id)->first();

        return $member && in_array($member->role, ['Owner', 'Editor']);
    }
}
