<?php

namespace App\Http\Services;

use App\Http\Repositories\CommentRepository;
use App\Models\Comment;

class CommentService
{
    private CommentRepository $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function create(array $data, int $authId)
    {
        $data['auth_id'] = $authId;
        return $this->commentRepository->create($data);
    }

    public function update(Comment $comment, array $data): bool
    {
        return $this->commentRepository->update($comment, $data);
    }

    public function delete(Comment $comment): ?bool
    {
        return $this->commentRepository->delete($comment);
    }

}
