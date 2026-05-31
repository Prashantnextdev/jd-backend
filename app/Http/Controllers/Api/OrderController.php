<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->createOrder($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => $order,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $order = $this->orderService->getOrder($id);

        return response()->json([
            'success' => true,
            'message' => 'Order fetched successfully',
            'data' => $order,
        ]);
    }
}