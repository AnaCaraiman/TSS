<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class ProductService
{
    public function __construct(protected ProductRepository $productRepository) {}

    /**
     * @throws Exception
     */
    public function createProduct(array $data): ?object
    {
        $product= $this->productRepository->createProduct($data);

        if (!$product) {
            throw new Exception('Product not created');
        }


        return $product;
    }

    public function getProducts(): array
    {
        return $this->productRepository->getProducts();
    }

    /**
     * @throws Exception
     */
    public function getProduct(int $id): object
    {

        $product = $this->productRepository->getProduct($id);
        if($product === null)
        {
            throw new Exception('Product not found');
        }

        return $product;
    }

    /**
     * @throws Exception
     */
    public function removeProduct(int $id): void
    {
        if(!$this->productRepository->removeProduct($id)) {
            throw new Exception('Product with id ' . $id . ' not found.');
        }
    }

    public function getBrands(): array {
        return $this->productRepository->getBrands();
    }

    public function decrementStock(int $productId, int $quantity): void{
        Log::info('in service');
        $this->productRepository->lowerStock($productId, $quantity);

    }

    public function getProductsByIds(array $ids): Collection {
        return $this->productRepository->getProductsByIds($ids);
    }



}
