<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Cache;
use App\Jobs\ProductCreatedNotificationJob;
class ProductService
{
    private string $productListCacheKey = 'products:list';

    public function __construct(
        private ProductRepository $productRepository
    ) {}

    

    public function getProducts()
    {
        return Cache::remember($this->productListCacheKey, now()->addMinutes(10), function () {
            return $this->productRepository->getAll()->toArray();
        });
    }

    public function createProduct(array $data): Product
    {
        $product = $this->productRepository->create($data);

        $this->clearProductCache($product->id);

        ProductCreatedNotificationJob::dispatch($product);

        return $product;
    }
    public function updateProduct(Product $product, array $data): Product
    {
        $updatedProduct = $this->productRepository->update($product, $data);

        $this->clearProductCache($updatedProduct->id);

        return $updatedProduct;
    }

    public function deleteProduct(Product $product): bool
    {
        $productId = $product->id;

        $deleted = $this->productRepository->delete($product);

        $this->clearProductCache($productId);

        return $deleted;
    }

    private function clearProductCache(?int $productId = null): void
    {
        Cache::forget($this->productListCacheKey);

        if ($productId) {
            Cache::forget("products:{$productId}");
        }
    }
}