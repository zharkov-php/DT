<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Services\CommentService;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    private CommentService $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    public function store(StoreCommentRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $comment = $this->commentService->create($validated, auth()->user()->id);

        return response()->json($comment, 201);
    }
}
