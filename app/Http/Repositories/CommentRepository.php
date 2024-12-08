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

    public function update(Comment $comment, $data): bool
    {
        return $comment->update($data);
    }

    public function delete(Comment $comment): ?bool
    {
        return $comment->delete();
    }

}
