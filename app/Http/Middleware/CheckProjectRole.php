<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckProjectRole
{
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        $project = $request->route('project');
        $projectId = $request->route('id');

        if ($project) {
            $projectId = $project->id;
        } elseif ($projectId) {
            $projectId = (int) $projectId;
        } else {
            return response()->json(['message' => 'Invalid project parameter'], 400);

        }
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $roleList = array_map('trim', explode('|', $roles));
        foreach ($roleList as $role) {
            if ($user->hasProjectRole($projectId, $role)) {
                return $next($request);
            }
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }
}
