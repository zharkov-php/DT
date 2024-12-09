<?php

namespace App\Http\Controllers;

use App\Http\Services\AnalyticService;
use App\Models\Project;
use Illuminate\Http\JsonResponse;

class AnalyticController extends Controller
{
    private AnalyticService $analyticService;

    public function __construct(AnalyticService $analyticService)
    {
        $this->analyticService = $analyticService;
    }

    public function index(Project $project): JsonResponse
    {
        $analytics = $this->analyticService->getTaskAnalytics($project);

        return response()->json($analytics);
    }
}
