<?php

namespace Tests\Feature\Api;

use App\Models\Project;
use App\Models\ProjectContent;
use App\Models\User;
use Emerald\Models\User as ModelsUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    /**
     * SUCCESS CASES
     */
    #[Test]
    public function create_project(): void
    {
        $user = User::factory()->create();
        $user_id = $user->id;

        $payload = [
            "name" => "Project name test",
            "description" => "This test is for testing of a project creation"
        ];

        $response = $this->postJson('/api/v1/users/' . $user_id . '/projects', $payload);

        $response->assertCreated()
            ->assertJsonStructure([
                'name',
                'description',
                'owner_id',
                'updated_at',
                'created_at',
                'id'
            ])
            ->assertJsonFragment([
                'name' => 'Project name test',
                'description' => 'This test is for testing of a project creation'
            ]);

        // Project created in 'projects' table
        $this->assertDatabaseHas('projects', [
            'owner_id' => $user_id,
            'name' => 'Project name test',
            'description' => 'This test is for testing of a project creation'
        ]);

        // Content Project created in 'project_contents'
        $this->assertDatabaseHas('project_contents', [
            'project_id' => $response->json('id'),
        ]);

        $content = ProjectContent::where(
            'project_id',
            $response->json('id')
        )->firstOrFail();

        $this->assertSame([], $content->content);
    }

    #[Test]
    public function update_valid_name_of_a_project(): void
    {
        $user = User::factory()->create();
        $user_id = $user->id;
        $project = Project::factory()->create([
            'owner_id' => $user_id
        ]);
        $project_id = $project->id;

        $request = $this->patchJson('/api/v1/users/' . $user_id . '/projects/' . $project_id, [
            'name' => 'updated name'
        ]);

        $request->assertOk()
            ->assertJsonStructure([
                'id',
                'owner_id',
                'name',
                'description',
                'created_at',
                'updated_at'

            ])
            ->assertJsonFragment([
                'id' => $project_id,
                'owner_id' => $user_id,
                'name' => 'updated name',
            ]);
    }


    /**
     * FAILED CASSES
     */
    #[Test]
    public function create_project_duplicated_name(): void
    {
        $user = User::factory()->create();
        $user_id = $user->id;

        $origPayload = [
            "name" => "Duplicated name test",
            "description" => "This test is in case of duplicated project name"
        ];

        $duplPayload = [
            "name" => "Duplicated name test",
            "description" => "This test is in case of duplicated project name"
        ];

        $this->postJson('/api/v1/users/' . $user_id . '/projects', $origPayload);

        $response = $this->postJson('/api/v1/users/' . $user_id . '/projects', $duplPayload);

        $response->assertJsonValidationErrors('name')
            ->assertJsonMissingValidationErrors('description');
    }



    #[Test]
    public function update_invalid_name_of_a_project(): void
    {
        $user = User::factory()->create();

        $originalProject = Project::factory()->create([
            'owner_id' => $user->id,
            'name' => 'Original project',
        ]);

        $existingProject = Project::factory()->create([
            'owner_id' => $user->id,
            'name' => 'Existing project',
        ]);

        $response = $this->patchJson(
            "/api/v1/users/{$user->id}/projects/{$originalProject->id}",
            ['name' => $existingProject->name]
        );

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('name');

        $this->assertDatabaseHas('projects', [
            'id' => $originalProject->id,
            'name' => 'Original project',
        ]);
    }
}
