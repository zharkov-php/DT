<?php

namespace App\Http\Repositories;

use App\Models\Project;

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

}
