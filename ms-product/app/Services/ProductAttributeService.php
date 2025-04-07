<?php

namespace App\Services;

use App\Repositories\ProductAttributeRepository;
use Exception;

class ProductAttributeService
{
    public function __construct(public ProductAttributeRepository $productAttributeRepository) {}

    /**
     * @throws Exception
     */
    public function createAttribute(int $productId, $attributeData): void
    {
        if(!$this->productAttributeRepository->createAttributeForProduct($productId, $attributeData)) {
            throw new Exception('Attribute not created for product with id ' . $productId);
        }
    }

    public function getProductAttributes(int $productId): array
    {
        return $this->productAttributeRepository
            ->getAllAtributesByProductId($productId);
    }

    /**
     * @throws Exception
     */
    public function removeAttribute(int $attributeId): void
    {
        if(!$this->productAttributeRepository
            ->removeAttributeFromProduct($attributeId)){
            throw new Exception('Attribute with id ' . $attributeId .' not removed');
        }
    }
}
