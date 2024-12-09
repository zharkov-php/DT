<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectInvitationFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function project_owner_can_invite_user_to_project()
    {
        $this->actingAs($owner = User::factory()->create());

        $project = Project::factory()->create(['owner_id' => $owner->id]);

        $response = $this->postJson(route('projects.invite', $project), [
            'email' => 'test@example.com',
            'invited_role' => 'Editor',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Invitation sent successfully',
            ]);

        $this->assertDatabaseHas('project_invitations', [
            'project_id' => $project->id,
            'email' => 'test@example.com',
            'invited_role' => 'Editor',
        ]);
    }

    /** @test */
    public function non_owner_cannot_invite_user_to_project()
    {
        $this->actingAs($user = User::factory()->create());

        $project = Project::factory()->create();

        $response = $this->postJson(route('projects.invite', $project), [
            'email' => 'test@example.com',
            'invited_role' => 'Viewer',
        ]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('project_invitations', [
            'email' => 'test@example.com',
        ]);
    }
}
