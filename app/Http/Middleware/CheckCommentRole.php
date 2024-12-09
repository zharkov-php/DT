<?php

namespace App\Http\Middleware;

use App\Models\Comment;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCommentRole
{
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        $comment = $request->route('comment');
        $commentId = $request->route('id');

        if (!$comment && $commentId) {
            $comment = Comment::find($commentId);
        }

        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Отримання проекту, до якого належить коментар
        $project = $comment->task->project ?? null;

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
