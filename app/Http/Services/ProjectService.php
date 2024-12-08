<?php

namespace App\Http\Services;

use App\Http\Repositories\ProjectRepository;

class ProjectService
{
    private ProjectRepository $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function create(
        string $name,
        string|null $description,
        int $ownerId,
    )
    {
        return $this->projectRepository->create(
            $name, $description, $ownerId
        );
    }

}
