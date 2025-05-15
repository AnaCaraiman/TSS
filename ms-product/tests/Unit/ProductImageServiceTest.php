<?php

namespace Tests\Unit;

use App\Repositories\ProductImageRepository;
use App\Services\ProductImageService;
use Exception;
use Tests\TestCase;
use Mockery;

class ProductImageServiceTest extends TestCase
{
    protected $productImageRepository;
    protected $productImageService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productImageRepository = Mockery::mock(ProductImageRepository::class);
        $this->productImageService = new ProductImageService($this->productImageRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testCreateImageSuccess(): void
    {
        $data = ['product_id' => 1, 'url' => 'image.jpg'];

        $this->productImageRepository
            ->shouldReceive('createProductImage')
            ->with($data)
            ->once()
            ->andReturn(true);

        $this->productImageService->createImage($data);

        $this->assertTrue(true);
    }

    public function testCreateImageFailure(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Product image not added');

        $data = ['product_id' => 1, 'url' => 'image.jpg'];

        $this->productImageRepository
            ->shouldReceive('createProductImage')
            ->with($data)
            ->once()
            ->andReturn(false);

        $this->productImageService->createImage($data);
    }

    public function testGetImagesByProductId(): void
    {
        $productId = 1;
        $images = [
            ['id' => 1, 'url' => 'img1.jpg'],
            ['id' => 2, 'url' => 'img2.jpg'],
        ];

        $this->productImageRepository
            ->shouldReceive('getImagesByProductId')
            ->with($productId)
            ->once()
            ->andReturn($images);

        $result = $this->productImageService->getImagesByProductId($productId);

        $this->assertEquals($images, $result);
    }

    /**
     * @throws Exception
     */
    public function testDeleteImageSuccess(): void
    {
        $this->productImageRepository
            ->shouldReceive('removeImageById')
            ->with(1)
            ->once()
            ->andReturn(true);

        $this->productImageService->deleteImage(1);

        $this->assertTrue(true);
    }

    public function testDeleteImageFailure(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No picture was deleted. No matching id.');

        $this->productImageRepository
            ->shouldReceive('removeImageById')
            ->with(1)
            ->once()
            ->andReturn(false);

        $this->productImageService->deleteImage(1);
    }
}
