<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Task $task;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->task = Task::factory()->create();

        $this->actingAs($this->user);
    }

    /** @test */
    public function a_user_can_create_a_comment()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $project = Project::factory()->create(['owner_id' => $user->id]);
        $task = Task::factory()->create(['project_id' => $project->id]);

        $project->members()->attach($user->id, ['role' => 'editor']);

        $commentData = [
            'task_id' => $task->id,
            'content' => 'This is a test comment.',
        ];

        $response = $this->postJson('/api/comments', $commentData);

        $response->assertStatus(201)
            ->assertJsonFragment(['content' => 'This is a test comment.']);

        $this->assertDatabaseHas('comments', [
            'task_id' => $task->id,
            'content' => 'This is a test comment.',
        ]);
    }

    /** @test */
    public function a_user_can_update_a_comment_within_time_limit()
    {
        $comment = Comment::factory()->create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
            'created_at' => now(),
        ]);

        $updateData = [
            'content' => 'Updated comment content.',
        ];

        $response = $this->putJson("/api/comments/{$comment->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment(['content' => 'Updated comment content.']);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'Updated comment content.',
        ]);
    }

    /** @test */
    public function a_user_cannot_update_a_comment_after_time_limit()
    {
        $comment = Comment::factory()->create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
            'created_at' => now()->subMinutes(15),
        ]);

        $updateData = [
            'content' => 'Late update attempt.',
        ];

        $response = $this->putJson("/api/comments/{$comment->id}", $updateData);

        $response->assertStatus(403);
    }

    /** @test */
    public function a_user_can_delete_a_comment()
    {
        $comment = Comment::factory()->create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->deleteJson("/api/comments/{$comment->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Comment deleted successfully']);
    }
}
