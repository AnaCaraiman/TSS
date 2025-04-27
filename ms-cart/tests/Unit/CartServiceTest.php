<?php

namespace Tests\Unit\Services;

use App\Services\CartService;
use App\Repositories\CartRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;
use stdClass;

class CartServiceTest extends TestCase
{
    protected $cartRepositoryMock;
    protected $cartService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cartRepositoryMock = Mockery::mock(CartRepository::class);
        $this->cartService = new CartService($this->cartRepositoryMock);

        Cache::shouldReceive('forget')->andReturnTrue();
        Cache::shouldReceive('put')->andReturnTrue();
        Log::shouldReceive('info')->andReturnNull();
    }

    public function test_create_cart_successfully()
    {
        $data = ['user_id' => 1];

        $fakeCart = (object)['user_id' => 1];

        $this->cartRepositoryMock->shouldReceive('createCart')
            ->with($data)
            ->once()
            ->andReturn(true);

        $this->cartRepositoryMock->shouldReceive('getCart')
            ->with($data['user_id'])
            ->once()
            ->andReturn($fakeCart);

        $this->cartService->createCart($data);

        $this->assertTrue(true); // If no exception, test passes
    }

    public function test_create_cart_failure()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Cart not created.");

        $data = ['user_id' => 1];

        $this->cartRepositoryMock->shouldReceive('createCart')
            ->with($data)
            ->once()
            ->andReturn(false);

        $this->cartService->createCart($data);
    }

    public function test_get_cart_returns_cached()
    {
        $userId = 1;
        $cachedCart = (object)['user_id' => $userId];

        Cache::shouldReceive('get')
            ->with('cart_user_' . $userId)
            ->once()
            ->andReturn($cachedCart);

        $this->cartRepositoryMock->shouldNotReceive('getCart');

        $cart = $this->cartService->getCart($userId);

        $this->assertEquals($userId, $cart->user_id);
    }

    public function test_get_cart_returns_from_repository()
    {
        $userId = 1;
        $fakeCart = (object)['user_id' => $userId];

        Cache::shouldReceive('get')
            ->with('cart_user_' . $userId)
            ->once()
            ->andReturn(null);

        $this->cartRepositoryMock->shouldReceive('getCart')
            ->with($userId)
            ->once()
            ->andReturn($fakeCart);

        $cart = $this->cartService->getCart($userId);

        $this->assertEquals($userId, $cart->user_id);
    }

    public function test_delete_cart_successfully()
    {
        $userId = 1;

        $this->cartRepositoryMock->shouldReceive('deleteCart')
            ->with($userId)
            ->once()
            ->andReturn(true);

        $this->cartService->deleteCart($userId);

        $this->assertTrue(true); // If no exception, passes
    }

    public function test_delete_cart_failure()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Cart not deleted.");

        $userId = 1;

        $this->cartRepositoryMock->shouldReceive('deleteCart')
            ->with($userId)
            ->once()
            ->andReturn(false);

        $this->cartService->deleteCart($userId);
    }

    public function test_clear_cart_successfully()
    {
        $userId = 1;
        $fakeCart = (object)['user_id' => $userId];

        $this->cartRepositoryMock->shouldReceive('getCartId')
            ->with($userId)
            ->once()
            ->andReturn(10);

        $this->cartRepositoryMock->shouldReceive('clearCart')
            ->with(10)
            ->once()
            ->andReturn(true);

        $this->cartService->clearCart($userId);

        $this->assertTrue(true); // If no exception, passes
    }

    public function test_clear_cart_failure()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Cart not cleared.");

        $userId = 0;

        $this->cartRepositoryMock->shouldReceive('getCartId')
            ->with($userId)
            ->once()
            ->andReturn(10);

        $this->cartRepositoryMock->shouldReceive('clearCart')
            ->with(10)
            ->once()
            ->andReturn(false);

        $this->cartService->clearCart($userId);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}