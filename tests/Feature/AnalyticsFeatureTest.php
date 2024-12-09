<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AnalyticsFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->project = Project::factory()->create(['owner_id' => $this->user->id]);

        $this->actingAs($this->user);

        $this->project->members()->attach($this->user->id, ['role' => 'editor']);
    }

    /** @test */
    public function it_returns_correct_analytics_for_a_project_with_caching()
    {
        Task::factory()->create([
            'project_id' => $this->project->id,
            'status' => 'todo',
            'priority' => 'low',
            'assigned_to' => $this->user->id,
        ]);

        Task::factory()->create([
            'project_id' => $this->project->id,
            'status' => 'in_progress',
            'priority' => 'medium',
            'assigned_to' => $this->user->id,
        ]);

        Task::factory()->create([
            'project_id' => $this->project->id,
            'status' => 'done',
            'priority' => 'high',
            'assigned_to' => $this->user->id,
        ]);

        $expected = [
            'status_summary' => [
                'todo' => 1,
                'in_progress' => 1,
                'done' => 1,
            ],
            'priority_summary' => [
                'low' => 1,
                'medium' => 1,
                'high' => 1,
            ],
            'member_task_summary' => [
                [
                    'user_id' => $this->user->id,
                    'task_count' => 3,
                ],
            ],
        ];

        $cacheKey = "project_analytics_{$this->project->id}";
        Cache::shouldReceive('remember')
            ->once()
            ->withArgs(function ($key, $ttl, $closure) use ($cacheKey) {
                return $key === $cacheKey && $ttl instanceof \DateTime && $closure instanceof \Closure;
            })
            ->andReturn($expected);

        $response = $this->getJson("/api/projects/{$this->project->id}/analytics");

        $response->assertStatus(200)
            ->assertJson($expected);
    }

    /** @test */
    public function it_returns_empty_analytics_if_no_tasks_exist()
    {
        $response = $this->getJson("/api/projects/{$this->project->id}/analytics");

        $expected = [
            'status_summary' => [
                'todo' => 0,
                'in_progress' => 0,
                'done' => 0,
            ],
            'priority_summary' => [
                'low' => 0,
                'medium' => 0,
                'high' => 0,
            ],
            'member_task_summary' => [],
        ];

        $response->assertStatus(200)
            ->assertJson($expected);
    }

    /** @test */
    public function it_clears_the_cache_when_a_task_is_updated()
    {
        $cacheKey = "project_analytics_{$this->project->id}";
        Cache::put($cacheKey, ['cached_data'], now()->addMinutes(10));

        $this->assertTrue(Cache::has($cacheKey));

        $task = Task::factory()->create(['project_id' => $this->project->id]);

        $task->update(['status' => 'done']);

        $this->assertFalse(Cache::has($cacheKey));
    }

    /** @test */
    public function it_clears_the_cache_when_a_task_is_created()
    {
        $cacheKey = "project_analytics_{$this->project->id}";
        Cache::put($cacheKey, ['cached_data'], now()->addMinutes(10));

        $this->assertTrue(Cache::has($cacheKey));

        Task::factory()->create(['project_id' => $this->project->id]);

        $this->assertFalse(Cache::has($cacheKey));
    }

    /** @test */
    public function it_clears_the_cache_when_a_task_is_deleted()
    {
        $cacheKey = "project_analytics_{$this->project->id}";
        Cache::put($cacheKey, ['cached_data'], now()->addMinutes(10));

        $this->assertTrue(Cache::has($cacheKey));

        $task = Task::factory()->create(['project_id' => $this->project->id]);

        $task->delete();

        $this->assertFalse(Cache::has($cacheKey));
    }
}
