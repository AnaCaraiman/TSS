<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_with_valid_data()
    {   
        $response = $this->postJson('/api/auth/register', [
            'last_name' => 'Victor',
            'first_name' => 'Cucu',
            'email' => 'abcdef@yahoo.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone_prefix' => '+40',
            'phone_number' => '66425',
        ]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'user',
                    'accessToken',
                ]);

        $this->assertDatabaseHas('users', [
            'email' => 'abcdef@yahoo.com',
        ]);
    }

    public function test_registration_fails_with_invalid_data()
    {
        $response = $this->postJson('/api/auth/register', [
            'email' => 'not-an-email',
            // invalid email format
        ]);

        $response->assertStatus(400)
                 ->assertJsonStructure([
                     'errors',
                     "message",
                 ]);
    }
}