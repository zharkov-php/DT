<?php

namespace Tests\Unit;

use App\Http\Services\AnalyticService;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AnalyticServiceTest extends TestCase
{
    use RefreshDatabase;

    private AnalyticService $analyticService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->analyticService = new AnalyticService();
    }

    /** @test */
    public function it_calculates_task_analytics_correctly()
    {
        $tasks = [
            ['status' => 'todo', 'priority' => 'low', 'assigned_to' => 1],
            ['status' => 'in_progress', 'priority' => 'medium', 'assigned_to' => 2],
            ['status' => 'done', 'priority' => 'high', 'assigned_to' => 1],
            ['status' => 'todo', 'priority' => 'low', 'assigned_to' => 2],
        ];

        $result = $this->analyticService->calculateTaskAnalytics($tasks);

        $this->assertEquals([
            'todo' => 2,
            'in_progress' => 1,
            'done' => 1,
        ], $result['status_summary']);

        $this->assertEquals([
            'low' => 2,
            'medium' => 1,
            'high' => 1,
        ], $result['priority_summary']);

        $this->assertEquals([
            ['user_id' => 1, 'task_count' => 2],
            ['user_id' => 2, 'task_count' => 2],
        ], $result['member_task_summary']);
    }

    /** @test */
    public function it_returns_cached_analytics_if_exists()
    {
        $project = Project::factory()->create();
        $tasks = Task::factory(3)->create(['project_id' => $project->id])->toArray();

        $cacheKey = "project_analytics_{$project->id}";
        $cachedResult = [
            'status_summary' => ['todo' => 2, 'done' => 1],
            'priority_summary' => ['low' => 1, 'high' => 2],
            'member_task_summary' => [['user_id' => 1, 'task_count' => 3]],
        ];

        Cache::shouldReceive('remember')
            ->once()
            ->with($cacheKey, \Mockery::type('DateTime'), \Mockery::on(function ($callback) use ($tasks) {
                return is_callable($callback);
            }))
            ->andReturn($cachedResult);

        $result = $this->analyticService->getTaskAnalytics($project);

        $this->assertEquals($cachedResult, $result);
    }

    /** @test */
    public function it_clears_cache_correctly()
    {
        $project = Project::factory()->create();
        $cacheKey = "project_analytics_{$project->id}";

        Cache::shouldReceive('forget')
            ->once()
            ->with($cacheKey);

        $this->analyticService->clearCache($project);
    }
}
