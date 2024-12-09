<?php

namespace App\Http\Repositories;

use App\Models\Task;

class TaskRepository
{
    public function create(array $data): Task
    {
        return  Task::create($data);
    }

    public function update(Task $task, array $data): bool
    {
        return $task->update($data);

    }

    public function delete(Task $task): ?bool
    {
        return  $task->delete();
    }

    public function getById(int $taskId)
    {
        return \App\Models\Task::findOrFail($taskId);
    }
}
