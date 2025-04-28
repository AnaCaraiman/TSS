<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class LoginUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_successfully()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);
        $this->assertTrue(
            in_array($response->status(), [200, 201]),
            'Response status is not 200 or 201'
        );

        $response->assertJsonStructure([
                     'message',
                     'user',
                     'accessToken',
                 ]);
    }

    public function test_login_fails_with_wrong_credentials()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'message' => 'Wrong email or password.'
                 ]);
    }
}