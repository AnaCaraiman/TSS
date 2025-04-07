<?php

namespace App\Services;

use App\Repositories\ProductImageRepository;
use Exception;

class ProductImageService
{
    public function __construct(protected ProductImageRepository $productImageRepository) {}

    /**
     * @throws Exception
     */
    public function createImage(array $data): void
    {
        if(!$this->productImageRepository->createProductImage($data)) {
            throw new Exception('Product image not added');
        }
    }

    public function getImagesByProductId(int $productId): array
    {
        return $this->productImageRepository->getImagesByProductId($productId);
    }

    /**
     * @throws Exception
     */
    public function deleteImage(int $id): void
    {
        if(!$this->productImageRepository->removeImageById($id)){
            throw new Exception('No picture was deleted. No matching id.');
        }
    }


}
