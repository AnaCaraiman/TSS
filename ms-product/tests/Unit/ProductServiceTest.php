<?php

namespace Tests\Unit;

use App\Repositories\ProductRepository;
use App\Services\ProductService;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use PHPUnit\Framework\TestCase;

class ProductServiceTest extends TestCase
{
    protected $productRepository;
    protected $productService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productRepository = Mockery::mock(ProductRepository::class);
        $this->productService = new ProductService($this->productRepository);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testCreateProductSuccess()
    {
        $data = ['name' => 'Test Product'];
        $product = (object) ['id' => 1, 'name' => 'Test Product'];

        $this->productRepository
            ->shouldReceive('createProduct')
            ->once()
            ->with($data)
            ->andReturn($product);

        $result = $this->productService->createProduct($data);

        $this->assertEquals($product, $result);
    }

    public function testCreateProductFailure()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Product not created');

        $this->productRepository
            ->shouldReceive('createProduct')
            ->once()
            ->andReturn(null);

        $this->productService->createProduct(['name' => 'Fail']);
    }

    /**
     * @throws Exception
     */
    public function testGetProductSuccess()
    {
        $product = (object)['id' => 1, 'name' => 'Test'];

        $this->productRepository
            ->shouldReceive('getProduct')
            ->with(1)
            ->once()
            ->andReturn($product);

        $result = $this->productService->getProduct(1);

        $this->assertEquals($product, $result);
    }

    public function testGetProductNotFound()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Product not found');

        $this->productRepository
            ->shouldReceive('getProduct')
            ->with(1)
            ->once()
            ->andReturn(null);

        $this->productService->getProduct(1);
    }

    /**
     * @throws Exception
     */
    public function testRemoveProductSuccess()
    {
        $this->productRepository
            ->shouldReceive('removeProduct')
            ->with(1)
            ->once()
            ->andReturn(true);

        $this->productService->removeProduct(1);

        $this->assertTrue(true);
    }

    public function testRemoveProductFailure()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Product with id 1 not found.');

        $this->productRepository
            ->shouldReceive('removeProduct')
            ->with(1)
            ->once()
            ->andReturn(false);

        $this->productService->removeProduct(1);
    }

    public function testGetProducts()
    {
        $products = [['id' => 1], ['id' => 2]];

        $this->productRepository
            ->shouldReceive('getProducts')
            ->once()
            ->andReturn($products);

        $result = $this->productService->getProducts();

        $this->assertEquals($products, $result);
    }

    public function testGetBrands()
    {
        $brands = ['BrandA', 'BrandB'];

        $this->productRepository
            ->shouldReceive('getBrands')
            ->once()
            ->andReturn($brands);

        $result = $this->productService->getBrands();

        $this->assertEquals($brands, $result);
    }

    public function testDecrementStock()
    {
        $this->productRepository
            ->shouldReceive('lowerStock')
            ->once()
            ->with(1, 5);

        $this->productService->decrementStock(1, 5);

        $this->assertTrue(true);
    }

    public function testGetProductsByIds()
    {
        $collection = new Collection([(object)['id' => 1], (object)['id' => 2]]);

        $this->productRepository
            ->shouldReceive('getProductsByIds')
            ->once()
            ->with([1, 2])
            ->andReturn($collection);

        $result = $this->productService->getProductsByIds([1, 2]);

        $this->assertEquals($collection, $result);
    }
}
