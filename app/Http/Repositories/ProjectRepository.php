<?php

namespace App\Http\Repositories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectRepository
{
    public function create(
        string $name,
        string|null $description,
        int $ownerId,
    )
    {
        return Project::create([
            'name' => $name,
            'description' => $description ?? null,
            'owner_id' => $ownerId,
        ]);
    }

    public function getByProjectId(int $projectId)
    {
        return Project::findOrFail($projectId);
    }

    public function getByUserIdAssignedTo(Project $project, int $assignedTo): Model|HasMany|null
    {
        return $project->members()->where('user_id', $assignedTo)->first();
    }

}
