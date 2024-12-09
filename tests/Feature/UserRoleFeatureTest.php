<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserRoleFeatureTest extends TestCase
{
     use RefreshDatabase;

    private User $owner;
    private User $editor;
    private User $viewer;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create();
        $this->editor = User::factory()->create();
        $this->viewer = User::factory()->create();

        $this->project = Project::factory()->create(['owner_id' => $this->owner->id]);

        $this->project->members()->attach($this->editor->id, ['role' => 'Editor']);
        $this->project->members()->attach($this->viewer->id, ['role' => 'Viewer']);
    }

    /** @test */
    public function owner_can_create_tasks()
    {
        $this->actingAs($this->owner);

        $taskData = [
            'project_id' => $this->project->id,
            'title' => 'Task by Owner',
            'description' => 'Created by Owner',
            'status' => 'todo',
            'priority' => 'high',
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJsonFragment(['title' => 'Task by Owner']);
    }

    /** @test */
    public function viewer_cannot_create_tasks()
    {
        $this->actingAs($this->viewer);

        $taskData = [
            'project_id' => $this->project->id,
            'title' => 'Task by Viewer',
            'description' => 'Attempted by Viewer',
            'status' => 'todo',
            'priority' => 'low',
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(403)
            ->assertJsonFragment(['message' => 'This action is unauthorized.']);
    }

    /** @test */
    public function only_owner_can_delete_tasks()
    {
        $task = Task::factory()->create(['project_id' => $this->project->id]);

        $this->actingAs($this->editor);
        $response = $this->deleteJson("/api/tasks/{$task->id}");
        $response->assertStatus(403);

        $this->actingAs($this->viewer);
        $response = $this->deleteJson("/api/tasks/{$task->id}");
        $response->assertStatus(403);

        $this->actingAs($this->owner);
        $response = $this->deleteJson("/api/tasks/{$task->id}");
        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Task deleted successfully']);

        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function viewer_cannot_edit_tasks()
    {
        $task = Task::factory()->create(['project_id' => $this->project->id]);

        $this->actingAs($this->viewer);

        $updateData = [
            'title' => 'Updated by Viewer',
            'status' => 'in_progress',
        ];

        $response = $this->putJson("/api/tasks/{$task->id}", $updateData);

        $response->assertStatus(403)
            ->assertJsonFragment(['message' => 'This action is unauthorized.']);
    }

    /** @test */
    public function owner_can_edit_tasks()
    {
        $task = Task::factory()->create(['project_id' => $this->project->id]);

        $this->actingAs($this->owner);

        $updateData = [
            'title' => 'Updated by Owner',
            'status' => 'done',
        ];

        $response = $this->putJson("/api/tasks/{$task->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Updated Task successfully']);
    }

    /** @test */
    public function editor_can_edit_tasks()
    {
        $task = Task::factory()->create(['project_id' => $this->project->id]);
        $this->actingAs($this->editor);

        $updateData = [
            'title' => 'Updated by Editor 5',
            'status' => 'in_progress',
        ];

        $response = $this->putJson("/api/tasks/{$task->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Updated Task successfully']);
    }

    /** @test */
    public function editor_can_create_tasks()
    {
        $this->actingAs($this->editor);

        $taskData = [
            'project_id' => $this->project->id,
            'title' => 'Task by Editor',
            'description' => 'Created by Editor',
            'status' => 'todo',
            'priority' => 'medium',
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJsonFragment(['title' => 'Task by Editor']);
    }

    /** @test */
    public function all_roles_can_comment_on_tasks()
    {
        $task = Task::factory()->create(['project_id' => $this->project->id]);

        $users = [
            'Editor' => $this->editor,
            'Viewer' => $this->viewer,
        ];

        foreach ($users as $role => $user) {
            $this->actingAs($user);

            $commentData = [
                'task_id' => $task->id,
                'content' => "Comment by {$role}",
            ];

            $response = $this->postJson('/api/comments', $commentData);

            $response->assertStatus(201);
        }
    }
}
