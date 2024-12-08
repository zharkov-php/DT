<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Services\ProjectService;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    private ProjectService $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $project = $this->projectService->create(
            $validated['name'],
            $validated['description'] ?? null,
            auth()->id(),
        );

        return response()->json($project, 201);
    }

    public function addMember()
    {
        //
    }
}
