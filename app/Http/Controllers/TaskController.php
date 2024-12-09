<?php

namespace App\Http\Controllers;

use App\Http\Repositories\ProjectRepository;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Services\TaskService;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    private TaskService $taskService;
    private ProjectRepository $projectRepository;

    public function __construct(
        TaskService $taskService,
        ProjectRepository $projectRepository,
    ) {
        $this->taskService = $taskService;
        $this->projectRepository = $projectRepository;
    }

    public function index(Request $request, Project $project): JsonResponse
    {
        $tasks = $this->taskService->filterTasks($project, $request);

        return response()->json($tasks);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $project = $this->projectRepository->getByProjectId($validated['project_id']);
        $this->authorize('create', [Task::class, $project]);
        $task = $this->taskService->create($validated, $project);

        return response()->json($task, 201);
    }


    public function update(UpdateTaskRequest $request, Task $task)
    {
        $validated = $request->validated();
        $updateTask = $this->taskService->update($task, $validated);
        if ($updateTask) {
            return response()->json(['message' => 'Updated Task successfully']);
        }
    }

    public function destroy(Task $task): JsonResponse
    {
        $this->taskService->delete($task);

        return response()->json(['message' => 'Task deleted successfully'], 200);
    }
}
