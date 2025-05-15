<?php

namespace Tests\Unit;

use App\Repositories\ProductAttributeRepository;
use App\Services\ProductAttributeService;
use Exception;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductAttributeServiceTest extends TestCase
{
    protected $productAttributeRepository;
    protected $productAttributeService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productAttributeRepository = Mockery::mock(ProductAttributeRepository::class);
        $this->productAttributeService = new ProductAttributeService($this->productAttributeRepository);
    }

    /** @test
     * @throws Exception
     */
    public function it_creates_an_attribute_successfully()
    {
        $this->productAttributeRepository
            ->shouldReceive('createAttributeForProduct')
            ->once()
            ->with(1, ['attribute_name' => 'Color', 'attribute_value' => 'Red'])
            ->andReturn(true);

        $this->productAttributeService->createAttribute(1, [
            'attribute_name' => 'Color',
            'attribute_value' => 'Red',
        ]);

        $this->assertTrue(true);
    }

    #[Test] public function it_throws_exception_when_create_attribute_fails()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Attribute not created for product with id 1');

        $this->productAttributeRepository
            ->shouldReceive('createAttributeForProduct')
            ->once()
            ->with(1, ['attribute_name' => 'Color', 'attribute_value' => 'Red'])
            ->andReturn(false);

        $this->productAttributeService->createAttribute(1, [
            'attribute_name' => 'Color',
            'attribute_value' => 'Red',
        ]);
    }

    #[Test] public function it_gets_product_attributes_successfully()
    {
        $this->productAttributeRepository
            ->shouldReceive('getAllAtributesByProductId')
            ->once()
            ->with(1)
            ->andReturn([
                ['attribute_name' => 'Color', 'attribute_value' => 'Red']
            ]);

        $attributes = $this->productAttributeService->getProductAttributes(1);

        $this->assertIsArray($attributes);
        $this->assertEquals('Color', $attributes[0]['attribute_name']);
    }

    /** @test
     * @throws Exception
     */
    public function it_removes_attribute_successfully()
    {
        $this->productAttributeRepository
            ->shouldReceive('removeAttributeFromProduct')
            ->once()
            ->with(1)
            ->andReturn(true);

        $this->productAttributeService->removeAttribute(1);

        $this->assertTrue(true);
    }

    #[Test] public function it_throws_exception_when_remove_attribute_fails()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Attribute with id 1 not removed');

        $this->productAttributeRepository
            ->shouldReceive('removeAttributeFromProduct')
            ->once()
            ->with(1)
            ->andReturn(false);

        $this->productAttributeService->removeAttribute(1);
    }
}
