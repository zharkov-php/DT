<?php

namespace App\Http\Services;

use App\Http\Repositories\ProjectRepository;
use App\Http\Repositories\TaskRepository;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskService
{
    private TaskRepository $taskRepository;
    private ProjectRepository $projectRepository;

    public function __construct(
        TaskRepository $taskRepository,
        ProjectRepository $projectRepository,
    ) {
        $this->taskRepository = $taskRepository;
        $this->projectRepository = $projectRepository;
    }

    public function create(array $data, Project $project): Task|JsonResponse
    {
        if (isset($data['assigned_to'])) {

            $assignedMember = $this->projectRepository->getByUserIdAssignedTo($project, $data['assigned_to']);

            if (!$assignedMember || $assignedMember->role === 'Viewer') {
                return response()->json(['message' => 'Cannot assign task to a Viewer'], 400);
            }
        }

        return $this->taskRepository->create($data);
    }

    public function update(Task $task, array $data): JsonResponse|bool
    {
        if (isset($data['assigned_to'])) {

            $assignedMember = $task->project->users()->where('user_id', $data['assigned_to'])->first();

            if (!$assignedMember || $assignedMember->role === 'Viewer') {
                return response()->json(['message' => 'Cannot assign task to a Viewer'], 400);
            }
        }

        return $this->taskRepository->update($task, $data);
    }

    public function delete(Task $task): ?bool
    {
        return $this->taskRepository->delete($task);
    }

    public function filterTasks(Project $project, Request $request): Collection
    {
        $query = $project->tasks();

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        if ($request->has('assigned_to')) {
            $query->where('assigned_to', $request->input('assigned_to'));
        }

        return $query->get();
    }

}
