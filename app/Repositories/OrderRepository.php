<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\Product;

class OrderRepository
{
    public function createOrder(array $data): Order
    {
        return Order::create($data);
    }

    public function createOrderItem(Order $order, array $data)
    {
        return $order->items()->create($data);
    }

    public function findProductForUpdate(int $productId): Product
    {
        return Product::where('id', $productId)
            ->lockForUpdate()
            ->firstOrFail();
    }

    public function getOrderWithDetails(int $orderId): Order
    {
        return Order::with(['items.product', 'payment'])
            ->findOrFail($orderId);
    }
}