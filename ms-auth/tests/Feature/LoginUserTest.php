<?php

namespace Tests\Feature\Auth;

use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Mockery\MockInterface;


class LoginUserTest extends TestCase
{
    use RefreshDatabase;
    public function setUp(): void
    {
        parent::setUp();

        $mockChannel = \Mockery::mock();
        $mockChannel->shouldIgnoreMissing(); // Let channel() calls go through safely
        $mockConnection = \Mockery::mock(AMQPStreamConnection::class, function (MockInterface $mock) use ($mockChannel) {
            $mock->shouldReceive('channel')->andReturn($mockChannel);
        });
        //$mockConnection->shouldReceive('channel')->andReturn($mockChannel);

        $this->app->instance(AMQPStreamConnection::class, $mockConnection);
    }
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

    public function test_user_can_logout_successfully()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'User logged out successfully.'
            ]);

        $this->assertEmpty($user->tokens);
    }

    public function test_user_can_refresh_token()
    {
        $user = User::factory()->create();
        $user->createToken('access_token');

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/auth/refresh', [
            'refreshToken' => 'test_value'
        ]);


        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'accessToken'
            ]);
    }

    public function test_user_can_delete_account()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->deleteJson('/api/auth/');

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Account deleted successfully'
            ]);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}