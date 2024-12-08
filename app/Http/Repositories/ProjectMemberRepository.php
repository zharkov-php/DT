<?php

namespace App\Http\Repositories;

use App\Models\ProjectMember;

class ProjectMemberRepository
{
    public function checkExistsMemberByProjectIdAuthId(int $projectId, int $authId): bool
    {
        return  ProjectMember::where('project_id', $projectId)
            ->where('user_id', $authId)
            ->exists();
    }

    public function create(int $projectId, int $authId)
    {
        return ProjectMember::create([
            'project_id' => $projectId,
            'user_id' => $authId,
            'role' => 'Viewer',
        ]);
    }

}
