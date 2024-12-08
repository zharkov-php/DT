<?php

namespace App\Http\Controllers;

use App\Http\Requests\InviteProjectRequest;
use App\Http\Requests\JoinTeamProjectRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Services\ProjectService;
use App\Models\Project;
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

    public function invite(InviteProjectRequest $request, Project $project): JsonResponse
    {
        $this->authorize('update', $project);

        $validated = $request->validated();

        $invitation = $this->projectService->createInvite($project, $validated);

        // We can make any funct for sending invitation
        // Mail::to($validated['email'])->send(new ProjectInvitationMail($invitation));

        return response()->json([
            'message' => 'Invitation sent successfully',
            'invitation' => $invitation,
        ], 201);
    }

    public function joinTeam(JoinTeamProjectRequest $request): JsonResponse
    {
        $validated = $request->validated();

        return $this->projectService->joinTeam($validated, auth()->user()->id);
    }

    public function destroy(Project $project): JsonResponse
    {
        $this->authorize('delete', $project);

        $this->projectService->delete($project);

        return response()->json(['message' => 'Project soft deleted successfully'], 200);
    }

    public function restore(int $projectId): JsonResponse
    {

        $project = Project::onlyTrashed()->where('id', $projectId)->firstOrFail();
        $this->authorize('restore', $project);

        if ($project) {
            $this->projectService->restore($project);
        }


        return response()->json(['message' => 'Project restored successfully'], 200);
    }
}
