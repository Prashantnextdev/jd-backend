<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    public function index(): JsonResponse
    {
        $products = $this->productService->getProducts();

        return response()->json([
            'success' => true,
            'message' => 'Products fetched successfully',
            'data' => $products,
        ]);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->createProduct($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->productService->getProduct($id);

        return response()->json([
            'success' => true,
            'message' => 'Product fetched successfully',
            'data' => $product,
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product = $this->productService->updateProduct(
            $product,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product,
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->productService->deleteProduct($product);

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
        ]);
    }
}