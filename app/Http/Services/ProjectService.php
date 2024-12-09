<?php

namespace App\Http\Services;

use App\Http\Repositories\ProjectInvitationRepository;
use App\Http\Repositories\ProjectMemberRepository;
use App\Http\Repositories\ProjectRepository;
use App\Models\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ProjectService
{
    private ProjectRepository $projectRepository;
    private ProjectInvitationRepository $projectInvitationRepository;
    private ProjectMemberRepository $projectMemberRepository;

    public function __construct(
        ProjectRepository $projectRepository,
        ProjectInvitationRepository $projectInvitationRepository,
        ProjectMemberRepository $projectMemberRepository,

    ) {
        $this->projectRepository = $projectRepository;
        $this->projectInvitationRepository = $projectInvitationRepository;
        $this->projectMemberRepository = $projectMemberRepository;
    }

    public function create(
        string $name,
        string|null $description,
        int $ownerId,
    ) {

        $project = $this->projectRepository->create(
            $name, $description, $ownerId
        );
        $project->members()->attach($ownerId, ['role' => 'Owner']);

        return $project;
    }

    public function createInvite(Project $project, array $data): Model
    {
        $token = Str::random(32);
        return $project->invitations()->create([
            'invited_by' => auth()->id(),
            'email' => $data['email'],
            'token' => $token,
            'expires_at' => now()->addDay(),
            'invited_role' => $data['invited_role']
        ]);
    }

    public function joinTeam(array $data, int $authId): JsonResponse
    {
        $invitation = $this->projectInvitationRepository->getByToken($data['token']);

        if (now()->greaterThan($invitation->expires_at)) {
            return response()->json(['message' => 'Invitation expired'], 400);
        }

        $existingMember = $this->projectMemberRepository->checkExistsMemberByProjectIdAuthId($invitation->project_id,
            $authId);

        if ($existingMember) {
            return response()->json(['message' => 'You are already a member of this project'], 400);
        }
        $this->projectMemberRepository->create($invitation->project_id, $authId, $invitation->invited_role);

        $this->projectInvitationRepository->delete($invitation);

        return response()->json(['message' => 'Successfully joined the project'], 200);
    }

    public function delete(Project $project): void
    {
        $this->projectRepository->delete($project);
    }

    public function restore(Project $project): void
    {
        $this->projectRepository->restore($project);
    }
}
