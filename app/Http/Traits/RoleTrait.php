<?php

namespace App\Http\Traits;

use App\Models\Role;
use App\Models\User;

trait RoleTrait
{
    private function assignRoleToUserInProject(int $userId, int $projectId, string $roleName): void
    {
        $user = User::findOrFail($userId);
        $role = Role::where('name', $roleName)->firstOrFail();

        if (!$user->roles()->wherePivot('project_id', $projectId)->where('role_id', $role->id)->exists()) {
            $user->roles()->attach($role->id, ['project_id' => $projectId]);
        }
    }

}
