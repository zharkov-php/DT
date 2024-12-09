<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class TaskPolicy
{
    public function create(User $user, Project $project): bool
    {
        return $user->hasProjectRole($project->id, 'Owner') || $user->hasProjectRole($project->id, 'Editor');
    }


}
