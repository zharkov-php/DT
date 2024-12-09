<?php

namespace App\Observers;

use App\Http\Services\AnalyticService;
use App\Models\Task;

class TaskObserver
{
    protected AnalyticService $analyticService;

    public function __construct(AnalyticService $analyticService)
    {
        $this->analyticService = $analyticService;
    }

    public function created(Task $task): void
    {
        $this->analyticService->clearCache($task->project);
    }

    public function updated(Task $task): void
    {
        $this->analyticService->clearCache($task->project);
    }

    public function deleted(Task $task): void
    {
        $this->analyticService->clearCache($task->project);
    }
}
