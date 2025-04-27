<?php

namespace Tests\Feature\Cart;

use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateCartTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_product_to_cart()
    {
        $user = User::factory()->create();
        Cart::factory()->create([
            'id' => 1,
            'user_id' => $user->id,
        ]);

        $fakeProduct = (object)[
            'id' => 2,
            'name' => 'Test Product',
            'price' => 99.99,
        ];

        $response = $this->actingAs($user)->putJson('/api/ms-cart', [
            'user_id' => $user->id,
            'operation' => '+',
            'product_id' => $fakeProduct->id,
            'quantity' => 1,
        ]);

        print("Response: " . json_encode($response->json(), JSON_PRETTY_PRINT));

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'items'
                ]);
    }

    public function test_user_can_remove_product_from_cart()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson('/api/ms-cart', [
            'user_id' => $user->id,
            'operation' => '-',
            'product_id' => 2,
            'quantity' => 1,
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'items'
                 ]);
    }

    public function test_invalid_operation_defaults_to_remove_item()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson('/api/ms-cart', [
            'user_id' => $user->id,
            'operation' => 'invalid_operation',
            'product_id' => 2,
            'quantity' => 1,
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'items'
                 ]);
    }
}