<?php

namespace App\Http\Repositories;

use App\Models\Comment;

class CommentRepository
{
    public function create(array $data)
    {
        return Comment::create([
            'task_id' => $data['task_id'],
            'user_id' => $data['auth_id'],
            'content' => $data['content'],
        ]);
    }

}
