<?php

namespace App\Services;

use App\Models\Order;
use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;
use App\Jobs\OrderCreatedNotificationJob;

class OrderService
{
    public function __construct(
        private OrderRepository $orderRepository
    ) {}

    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {

            $order = $this->orderRepository->createOrder([
                'user_id' => auth()->id(),
                'total_amount' => 0,
                'status' => 'pending',
            ]);

            $totalAmount = 0;
            $updatedProductIds = [];

            foreach ($data['items'] as $item) {
                $product = $this->orderRepository->findProductForUpdate($item['product_id']);

                if (!$product->is_active) {
                    throw ValidationException::withMessages([
                        'product' => "Product {$product->name} is not active.",
                    ]);
                }

                if ($product->stock < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'stock' => "Insufficient stock for {$product->name}.",
                    ]);
                }

                $subtotal = $product->price * $item['quantity'];

                $this->orderRepository->createOrderItem($order, [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                ]);

                $product->decrement('stock', $item['quantity']);

                $updatedProductIds[] = $product->id;
                $totalAmount += $subtotal;
            }

            $order->update([
                'total_amount' => $totalAmount,
                'status' => 'confirmed',
            ]);

            $order->payment()->create([
                'amount' => $totalAmount,
                'payment_method' => $data['payment_method'],
                'status' => $data['payment_method'] === 'cod' ? 'pending' : 'paid',
                'transaction_id' => $data['payment_method'] === 'cod' ? null : uniqid('TXN_'),
            ]);

            Cache::forget('products:list');

            foreach ($updatedProductIds as $productId) {
                Cache::forget("products:{$productId}");
            }

            $orderDetails = $this->orderRepository->getOrderWithDetails($order->id);

            OrderCreatedNotificationJob::dispatch($orderDetails);

            return $orderDetails;
        });
    }

    public function getOrder(int $id): Order
    {
        return $this->orderRepository->getOrderWithDetails($id);
    }
}