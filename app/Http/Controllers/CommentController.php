<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Services\CommentService;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    private CommentService $commentService;

    public function __construct(
        CommentService $commentService,
    ) {
        $this->commentService = $commentService;
    }

    public function store(StoreCommentRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $comment = $this->commentService->create($validated, auth()->user()->id);

        return response()->json($comment, 201);
    }

    public function update(UpdateCommentRequest $request, Comment $comment): JsonResponse
    {
        $this->authorize('update', $comment);

        $validated = $request->validated();

        $this->commentService->update($comment, $validated);

        return response()->json($comment);
    }

    public function destroy(Comment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);

        $this->commentService->delete($comment);

        return response()->json(['message' => 'Comment deleted successfully'], 200);
    }
}
