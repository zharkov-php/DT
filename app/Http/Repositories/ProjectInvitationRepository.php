<?php

namespace App\Http\Repositories;

use App\Models\ProjectInvitation;

class ProjectInvitationRepository
{
    public function getByToken(string $token)
    {
        return ProjectInvitation::where('token', $token)->first();
    }

    public function delete(ProjectInvitation $projectInvitation): void
    {
        $projectInvitation->delete();
    }

}
