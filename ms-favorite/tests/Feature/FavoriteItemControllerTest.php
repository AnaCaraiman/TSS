<?php

namespace Tests\Feature;

use App\Http\Controllers\FavoriteItemController;
use App\Repositories\FavoriteRepository;
use App\Repositories\ItemsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FavoriteItemControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::post('/api/favorite/item', [FavoriteItemController::class, 'addFavoriteItem']);
        Route::delete('/api/favorite/item', [FavoriteItemController::class, 'removeFavoriteItem']);
    }


    #[Test]
    public function it_fails_to_add_favorite_item_with_invalid_data()
    {
        $response = $this->postJson('/api/favorite/item', []);

        $response->assertStatus(400);
        $this->assertStringContainsString('Favorite cart not found for user', $response->json('message'));
    }

    #[Test]
    public function it_removes_a_favorite_item_successfully()
    {
        $favoriteRepositoryMock = Mockery::mock(FavoriteRepository::class);
        $favoriteRepositoryMock->shouldReceive('getFavoriteCartId')
            ->with(1)
            ->andReturn(1);

        $itemsRepositoryMock = Mockery::mock(ItemsRepository::class);
        $itemsRepositoryMock->shouldReceive('deleteFavoriteItem')
            ->once()
            ->with(Mockery::type('array'))
            ->andReturn(true);

        $this->app->instance(FavoriteRepository::class, $favoriteRepositoryMock);
        $this->app->instance(ItemsRepository::class, $itemsRepositoryMock);

        $payload = [
            'user_id' => 1,
            'product_id' => 123,
        ];

        $response = $this->deleteJson('/api/favorite/item', $payload);

        $response->assertStatus(201);
        $response->assertJsonFragment(['message' => 'Item removed from favorite list']);
    }

    #[Test]
    public function it_returns_400_when_removal_fails()
    {
        $favoriteRepositoryMock = Mockery::mock(FavoriteRepository::class);
        $favoriteRepositoryMock->shouldReceive('getFavoriteCartId')
            ->with(999)
            ->andReturn(1);

        $itemsRepositoryMock = Mockery::mock(ItemsRepository::class);
        $itemsRepositoryMock->shouldReceive('deleteFavoriteItem')
            ->once()
            ->with(Mockery::type('array'))
            ->andReturn(false);

        $this->app->instance(FavoriteRepository::class, $favoriteRepositoryMock);
        $this->app->instance(ItemsRepository::class, $itemsRepositoryMock);

        $payload = [
            'user_id' => 999,
            'product_id' => 999,
        ];

        $response = $this->deleteJson('/api/favorite/item', $payload);

        $response->assertStatus(400);
        $this->assertStringContainsString('Failed to delete favorite item.', $response->json('message'));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
