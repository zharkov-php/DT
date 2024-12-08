<?php

namespace App\Http\Controllers;

use App\Http\Services\AnalyticService;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticController extends Controller
{
    private AnalyticService $analyticService;

    public function __construct(AnalyticService $analyticService)
    {
        $this->analyticService = $analyticService;
    }

    public function index(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        $tasks = $project->tasks->toArray();

        $analytics = $this->analyticService->calculateTaskAnalytics($tasks);

        return response()->json($analytics);
    }
}
