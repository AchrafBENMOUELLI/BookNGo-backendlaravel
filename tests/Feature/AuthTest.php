<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\WithFixtures;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    use WithFixtures;

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/register', $this->loadFixture('users.register_valid'));

        $response->assertStatus(201)
                 ->assertJsonStructure(['user', 'token']);
    }

    public function test_user_cannot_register_with_existing_email(): void
    {
        $fixture = $this->loadFixture('users.register_valid');
        User::factory()->create(['email' => $fixture['email']]);

        $response = $this->postJson('/api/register', $fixture);

        $response->assertStatus(422);
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password123')]);
        $payload = $this->loadFixture('users.login_valid');
        $payload['email'] = $user->email;

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(200)
                 ->assertJsonStructure(['user', 'token']);
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password123')]);
        $payload = $this->loadFixture('users.login_wrong_password');
        $payload['email'] = $user->email;

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(422);
    }

    public function test_user_can_logout(): void
    {
        $user  = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->postJson('/api/logout');

        $response->assertStatus(200);
    }

    public function test_user_can_get_their_profile(): void
    {
        $user  = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->getJson('/api/me');

        $response->assertStatus(200)
                 ->assertJsonFragment(['email' => $user->email]);
    }

    public function test_cannot_register_without_name(): void
    {
        $response = $this->postJson('/api/register', $this->loadFixture('users.register_missing_name'));
        $response->assertStatus(422);
    }

    public function test_cannot_register_with_short_password(): void
    {
        $response = $this->postJson('/api/register', $this->loadFixture('users.register_short_password'));
        $response->assertStatus(422);
    }

    public function test_cannot_register_without_password_confirmation(): void
    {
        $response = $this->postJson('/api/register', $this->loadFixture('users.register_missing_confirmation'));
        $response->assertStatus(422);
    }

    public function test_cannot_login_without_email(): void
    {
        $response = $this->postJson('/api/login', $this->loadFixture('users.login_missing_email'));
        $response->assertStatus(422);
    }

    public function test_cannot_login_without_password(): void
    {
        $response = $this->postJson('/api/login', $this->loadFixture('users.login_missing_password'));
        $response->assertStatus(422);
    }
}
