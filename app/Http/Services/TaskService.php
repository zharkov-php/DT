<?php

namespace App\Http\Services;

use App\Http\Repositories\TaskRepository;
use App\Models\Task;

class TaskService
{
    private TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function create(array $data): Task
    {
        return $this->taskRepository->create($data);
    }

    public function update(Task $task, array $data): bool
    {
        return $this->taskRepository->update($task, $data);
    }

    public function delete(Task $task): ?bool
    {
        return $this->taskRepository->delete($task);
    }

}
