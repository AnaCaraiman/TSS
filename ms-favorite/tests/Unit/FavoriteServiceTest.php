<?php

namespace Tests\Unit;

use App\Models\Favorite;
use App\Repositories\FavoriteRepository;
use App\Services\FavoriteService;
use Exception;
use Mockery;
use Tests\TestCase;

class FavoriteServiceTest extends TestCase
{
    protected $favoriteRepository;
    protected $favoriteService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->favoriteRepository = Mockery::mock(FavoriteRepository::class);
        $this->favoriteService = new FavoriteService($this->favoriteRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testCreateFavoriteSuccess()
    {
        $userId = 1;
        $favorite = new Favorite(['user_id' => $userId]);

        $this->favoriteRepository
            ->shouldReceive('createFavoriteCart')
            ->with($userId)
            ->once()
            ->andReturn($favorite);

        $result = $this->favoriteService->createFavorite($userId);

        $this->assertEquals($favorite, $result);
    }

    public function testGetFavoriteCartSuccess()
    {
        $userId = 1;
        $favorite = new Favorite(['user_id' => $userId]);

        $this->favoriteRepository
            ->shouldReceive('getFavoriteCart')
            ->with($userId)
            ->once()
            ->andReturn($favorite);

        $result = $this->favoriteService->getFavoriteCart($userId);

        $this->assertEquals($favorite, $result);
    }

    public function testGetFavoriteCartNotFound()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Favorite not found');

        $userId = 1;

        $this->favoriteRepository
            ->shouldReceive('getFavoriteCart')
            ->with($userId)
            ->once()
            ->andReturn(null);

        $this->favoriteService->getFavoriteCart($userId);
    }

    public function testDeleteFavoriteSuccess()
    {
        $userId = 1;

        $this->favoriteRepository
            ->shouldReceive('deleteFavoriteCart')
            ->with($userId)
            ->once()
            ->andReturn(true);

        $this->favoriteService->deleteFavorite($userId);

        $this->assertTrue(true);
    }

    public function testDeleteFavoriteFailure()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to delete favorite');

        $userId = 1;

        $this->favoriteRepository
            ->shouldReceive('deleteFavoriteCart')
            ->with($userId)
            ->once()
            ->andReturn(false);

        $this->favoriteService->deleteFavorite($userId);
    }
}
