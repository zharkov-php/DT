<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskFeatureTest extends TestCase
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
    }

    /** @test */
    public function a_user_can_create_a_task(): void
    {
        $taskData = [
            'project_id' => $this->project->id,
            'title' => 'Test Task',
            'description' => 'This is a test task',
            'status' => 'todo',
            'priority' => 'medium',
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJsonFragment(['title' => 'Test Task']);

        $this->assertDatabaseHas('tasks', $taskData);
    }

    /** @test */
    public function a_user_can_update_a_task(): void
    {
        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'title' => 'Original Task',
        ]);

        $updateData = [
            'title' => 'Updated Task',
            'status' => 'in_progress',
        ];

        $response = $this->putJson("/api/tasks/{$task->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Updated Task successfully']);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task',
            'status' => 'in_progress',
        ]);
    }

    /** @test */
    public function only_owner_can_delete_a_task(): void
    {
        $task = Task::factory()->create([
            'project_id' => $this->project->id,
        ]);

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Task deleted successfully']);

        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function a_user_can_filter_tasks_by_status(): void
    {
        Task::factory()->create([
            'project_id' => $this->project->id,
            'status' => 'todo',
        ]);

        Task::factory()->create([
            'project_id' => $this->project->id,
            'status' => 'done',
        ]);

        $response = $this->getJson("/api/projects/{$this->project->id}/tasks?status=todo");

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['status' => 'todo']);
    }
}
