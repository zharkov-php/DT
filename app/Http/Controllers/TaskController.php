<?php

namespace App\Http\Controllers;

use App\Http\Repositories\ProjectRepository;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Services\TaskService;
use App\Models\Task;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    private TaskService $taskService;
    private ProjectRepository $projectRepository;

    public function __construct(
        TaskService $taskService,
        ProjectRepository $projectRepository,
    )
    {
        $this->taskService = $taskService;
        $this->projectRepository = $projectRepository;
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $project = $this->projectRepository->getByProjectId($validated['project_id']);
        $this->authorize('create', new Task(['project_id' => $project->id]));

        $task = $this->taskService->create($validated, $project);

        return response()->json($task, 201);
    }


    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $validated = $request->validated();
        $this->authorize('update', $task);

        $updateTask = $this->taskService->update($task, $validated);

        return response()->json($updateTask);
    }

    public function destroy(Task $task): JsonResponse
    {
        $project = $task->project;
        $this->authorize('delete', $project);

        $this->taskService->delete($task);

        return response()->json(['message' => 'Task deleted successfully'], 200);
    }
}
