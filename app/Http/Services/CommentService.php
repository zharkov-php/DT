<?php

namespace App\Http\Services;

use App\Http\Repositories\CommentRepository;

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

}
