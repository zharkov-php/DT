<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Services\TaskService;
use App\Models\Task;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    private TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $task = $this->taskService->create($validated);

        return response()->json($task, 201);
    }


    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task->project);

        $validated = $request->validated();
        $updateTask = $this->taskService->update($task, $validated);

        return response()->json($updateTask);
    }

    public function destroy(Task $task): JsonResponse
    {
        $this->authorize('delete', $task);

        $this->taskService->delete($task);

        return response()->json(['message' => 'Task deleted'], 200);
    }
}
