<?php

namespace Tests\Unit\Services;

use App\Services\CartItemService;
use App\Repositories\CartItemRepository;
use App\Repositories\CartRepository;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use Mockery;

class CartItemServiceTest extends TestCase
{
    protected $cartItemRepositoryMock;
    protected $cartRepositoryMock;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cartItemRepositoryMock = Mockery::mock(CartItemRepository::class);
        $this->cartRepositoryMock = Mockery::mock(CartRepository::class);

        $this->service = new CartItemService(
            $this->cartItemRepositoryMock,
            $this->cartRepositoryMock
        );

        Cache::shouldReceive('forget')->andReturnTrue();
        Cache::shouldReceive('put')->andReturnTrue();
    }

    public function test_add_cart_item_successfully()
    {
        $data = ['user_id' => 1, 'product_id' => 2, 'quantity' => 1];

        $this->cartRepositoryMock->shouldReceive('getCartId')->with(1)->andReturn(10);
        $this->cartItemRepositoryMock->shouldReceive('addCartItem')->once()->andReturn(true);
        $this->cartRepositoryMock->shouldReceive('getCart')->with(1)->andReturn((object)['items' => []]);

        $cart = $this->service->addCartItem($data);

        $this->assertIsObject($cart);
    }

    public function test_add_cart_item_failure()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to add cart item.');

        $data = ['user_id' => 1, 'product_id' => 2, 'quantity' => 1];

        $this->cartRepositoryMock->shouldReceive('getCartId')->with(1)->andReturn(10);
        $this->cartItemRepositoryMock->shouldReceive('addCartItem')->once()->andReturn(false);

        $this->service->addCartItem($data);
    }

    public function test_update_cart_add_quantity()
    {
        $data = ['user_id' => 1, 'operation' => '+', 'product_id' => 2];

        $this->cartRepositoryMock->shouldReceive('getCartId')->with(1)->andReturn(10);
        $this->cartItemRepositoryMock->shouldReceive('addQuantityToCartItem')->andReturn(true);
        $this->cartRepositoryMock->shouldReceive('getCart')->with(1)->andReturn((object)['items' => []]);

        $cart = $this->service->updateCart($data);

        $this->assertIsObject($cart);
    }

    public function test_update_cart_remove_quantity()
    {
        $data = ['user_id' => 1, 'operation' => '-', 'product_id' => 2];

        $this->cartRepositoryMock->shouldReceive('getCartId')->with(1)->andReturn(10);
        $this->cartItemRepositoryMock->shouldReceive('removeQuantityFromCartItem')->andReturn(true);
        $this->cartRepositoryMock->shouldReceive('getCart')->with(1)->andReturn((object)['items' => []]);

        $cart = $this->service->updateCart($data);

        $this->assertIsObject($cart);
    }

    public function test_update_cart_remove_item()
    {
        $data = ['user_id' => 1, 'operation' => 'remove', 'product_id' => 2];

        $this->cartRepositoryMock->shouldReceive('getCartId')->with(1)->andReturn(10);
        $this->cartItemRepositoryMock->shouldReceive('removeCartItem')->andReturn(true);
        $this->cartRepositoryMock->shouldReceive('getCart')->with(1)->andReturn((object)['items' => []]);

        $cart = $this->service->updateCart($data);

        $this->assertIsObject($cart);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}