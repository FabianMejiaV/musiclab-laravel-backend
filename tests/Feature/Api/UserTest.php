<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function create_user(): void
    {
        $payload = [
            "name" => "user tres",
            "email" => "user_tres@email.com",
            "password" => "@Password1"
        ];

        $response = $this->postJson('/api/v1/users', $payload);

        $response->assertCreated()
            ->assertJsonFragment([
                'name' => $payload['name'],
                'email' => $payload['email'],
            ])
            ->assertJsonMissingPath('password');

        $this->assertDatabaseHas('users', [
            'id' => $response->json('id'),
            'name' => $payload['name'],
            'email' => $payload['email'],
        ]);

        $user = User::firstWhere('email', $payload['email']);

        $this->assertNotSame($payload['password'], $user->password);
        $this->assertTrue(Hash::check($payload['password'], $user->password));
    }

    #[Test]
    public function index_users(): void
    {
        User::factory()->count(4)->create();

        $response = $this->getJson('/api/v1/users');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                        'updated_at'
                    ],

                ],
                'meta' => [
                    'page',
                    'per_page',
                    'total',
                    'last_page',
                    'links' => [
                        'first_page',
                        'last_page'
                    ]
                ]
            ]);

        $this->assertDatabaseCount('users', 4);
    }

    #[Test]
    public function create_user_with_wrong_mail(): void
    {
        $response = $this->postJson('/api/v1/users', [

            "name" => "test",
            "email" => "test_wrong_email.com",
            "password" => "@Password1"

        ]);

        $response->assertJsonValidationErrors(['email'])
            ->assertJsonMissingValidationErrors(['name', 'password']);
    }

    #[Test]
    public function create_user_with_password_invalid(): void
    {
        $response = $this->postJson('/api/v1/users', [

            "name" => "test",
            "email" => "test@email.com",
            "password" => "password"

        ]);

        $response->assertJsonValidationErrors(['password'])
            ->assertJsonMissingValidationErrors(['email', 'name']);
    }

    #[Test]
    public function update_user_with_invalid_email(): void
    {
        $user = User::factory()->create();
        $userId = $user->id;

        $response = $this->patchJson('/api/v1/users/' . $userId, [
            "email" => "test_email.com"

        ]);

        $response->assertJsonValidationErrors(['email'])
            ->assertJsonMissingValidationErrors(['name', 'password']);
    }

    #[Test]
    public function update_user_with_invalid_password(): void
    {
        $user = User::factory()->create();
        $userId = $user->id;

        $response = $this->patchJson('/api/v1/users/' . $userId, [
            "password" => "password"

        ]);

        $response->assertJsonValidationErrors(['password'])
            ->assertJsonMissingValidationErrors(['name', 'email']);
    }

    #[Test]
    public function create_and_delete_user(): void
    {
        $user = User::factory()->create();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);

        $response = $this->deleteJson('/api/v1/users/' . $user->id);

        $response->assertOk()
            ->assertJson(['message' => 'User deleted successfully']);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}
