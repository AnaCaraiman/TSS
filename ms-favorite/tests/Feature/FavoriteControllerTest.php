<?php

namespace Tests\Feature;

use App\Http\Controllers\FavoriteController;
use App\Models\Favorite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class FavoriteControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::post('/api/favorite', [FavoriteController::class, 'createFavoriteCart']);
        Route::get('/api/favorite/{id}', [FavoriteController::class, 'getFavoriteCart']);
        Route::delete('/api/favorite', [FavoriteController::class, 'deleteFavoriteCart']);
    }

    #[Test]
    public function it_creates_a_favorite_cart_successfully()
    {
        $payload = ['user_id' => 1];

        $response = $this->postJson('/api/favorite', $payload);

        $response->assertStatus(201);
        $response->assertJsonFragment(['message' => 'Success']);

        $this->assertDatabaseHas('favorites', ['user_id' => 1]);
    }

    #[Test]
    public function it_fails_to_create_favorite_cart_with_invalid_data()
    {
        $response = $this->postJson('/api/favorite'); // missing user_id

        $response->assertStatus(400);
        $this->assertStringContainsString('user_id is required', $response->json('message'));
    }

    #[Test]
    public function it_gets_a_favorite_cart_successfully()
    {
        $favorite = Favorite::factory()->create(['user_id' => 2]);

        $response = $this->getJson("/api/favorite/$favorite->user_id");

        $response->assertStatus(201);
        $response->assertJsonFragment(['message' => 'Success']);
        $this->assertEquals(2, $response->json('favorite.user_id'));
    }

    #[Test]
    public function it_returns_400_if_favorite_cart_not_found()
    {
        $response = $this->getJson('/api/favorite/999');

        $response->assertStatus(400);
        $this->assertStringContainsString('Favorite not found', $response->json('message'));
    }

    #[Test]
    public function it_deletes_a_favorite_cart_successfully()
    {
        $favorite = Favorite::factory()->create(['user_id' => 3]);

        $response = $this->deleteJson('/api/favorite', ['user_id' => 3]);

        $response->assertStatus(201);
        $response->assertJsonFragment(['message' => 'Success']);

        $this->assertDatabaseMissing('favorites', ['user_id' => 3]);
    }

    #[Test]
    public function it_returns_400_if_favorite_cart_delete_fails()
    {
        $response = $this->deleteJson('/api/favorite', ['user_id' => 999]);

        $response->assertStatus(400);
        $this->assertStringContainsString('Failed to delete favorite', $response->json('message'));
    }
}
