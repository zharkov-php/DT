<?php

namespace App\Observers;

use App\Models\Project;
use App\Models\ProjectInvitation;
use App\Models\Task;

class ProjectObserver
{
    public function deleting(Project $project)
    {
        if (!$project->isForceDeleting()) {

            $project->tasks()->delete();
            $project->invitations()->delete();
        } else {

            $project->tasks()->forceDelete();
            $project->invitations()->forceDelete();
        }
    }

    public function restoring(Project $project): void
    {
        Task::where('project_id', $project->id)
            ->withTrashed()
            ->restore();

        ProjectInvitation::where('project_id', $project->id)
            ->withTrashed()
            ->restore();
    }
}
