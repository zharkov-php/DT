<?php

namespace App\Http\Middleware;

use App\Models\Task;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTaskRole
{
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        $task = $request->route('task');

        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $project = $task->project ?? null;

        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        $roleList = array_map('trim', explode('|', $roles));
        foreach ($roleList as $role) {
            if ($user->hasProjectRole($project->id, $role)) {
                return $next($request);
            }
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }
}
