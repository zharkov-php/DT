<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    public function update(User $user, Project $project): bool
    {
        $member = $project->members()->where('user_id', $user->id)->first();

        return $member && in_array($member->role, ['Owner', 'Editor']);
    }

    public function delete(User $user, Project $project): bool
    {
        return $project->owner_id === $user->id;
    }

    public function view(User $user, Project $project): bool
    {
        return $project->members()->where('user_id', $user->id)->exists();
    }

    public function restore(User $user, Project $project): bool
    {
        return $project->owner_id === $user->id;
    }

    public function invite(User $user, Project $project): bool
    {
        return $project->owner_id === $user->id;
    }
}
