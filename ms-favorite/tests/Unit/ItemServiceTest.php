<?php

namespace Tests\Unit;

use App\Models\FavoriteItem;
use App\Repositories\FavoriteRepository;
use App\Repositories\ItemsRepository;
use App\Services\ItemService;
use Exception;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class ItemServiceTest extends TestCase
{
    protected $itemsRepository;
    protected $favoriteRepository;
    protected $itemService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->itemsRepository = Mockery::mock(ItemsRepository::class);
        $this->favoriteRepository = Mockery::mock(FavoriteRepository::class);
        $this->itemService = new ItemService($this->itemsRepository, $this->favoriteRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testGetFavoriteIdByUserIdWithNull()
    {
        $result = $this->itemService->getFavoriteIdByUserId(null);
        $this->assertNull($result);
    }

    public function testGetFavoriteIdByUserIdSuccess()
    {
        $this->favoriteRepository
            ->shouldReceive('getFavoriteCartId')
            ->with(1)
            ->once()
            ->andReturn(123);

        $result = $this->itemService->getFavoriteIdByUserId(1);
        $this->assertEquals(123, $result);
    }

    public function testCreateItemSuccess()
    {
        $data = ['user_id' => 1, 'item_id' => 99];
        $expectedData = $data + ['favorite_id' => 123];
        $favoriteItem = new FavoriteItem();

        $this->favoriteRepository
            ->shouldReceive('getFavoriteCartId')
            ->with(1)
            ->once()
            ->andReturn(123);

        $this->itemsRepository
            ->shouldReceive('createFavoriteItem')
            ->with($expectedData)
            ->once()
            ->andReturn($favoriteItem);

        $result = $this->itemService->createItem($data);

        $this->assertEquals($favoriteItem, $result);
    }

    public function testDeleteItemSuccess()
    {
        Log::shouldReceive('info')->once();

        $data = ['user_id' => 1, 'item_id' => 50];
        $expectedData = $data + ['favorite_id' => 123];

        $this->favoriteRepository
            ->shouldReceive('getFavoriteCartId')
            ->with(1)
            ->once()
            ->andReturn(123);

        $this->itemsRepository
            ->shouldReceive('deleteFavoriteItem')
            ->with($expectedData)
            ->once()
            ->andReturn(true);

        $this->itemService->deleteItem($data);

        $this->assertTrue(true);
    }

    public function testDeleteItemFailure()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to delete favorite item.');

        Log::shouldReceive('info')->once();

        $data = ['user_id' => 1, 'item_id' => 50];
        $expectedData = $data + ['favorite_id' => 123];

        $this->favoriteRepository
            ->shouldReceive('getFavoriteCartId')
            ->with(1)
            ->once()
            ->andReturn(123);

        $this->itemsRepository
            ->shouldReceive('deleteFavoriteItem')
            ->with($expectedData)
            ->once()
            ->andReturn(false);

        $this->itemService->deleteItem($data);
    }
}
