<?php

namespace Tests\Feature;

use App\Jobs\OrderCreatedNotificationJob;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_can_be_created_and_stock_decreases(): void
    {
        Queue::fake();

        $product = Product::factory()->create([
            'stock' => 10,
            'price' => 100,
            'is_active' => true,
        ]);

        $payload = [
            'payment_method' => 'cod',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 3,
                ],
            ],
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('orders', [
            'status' => 'confirmed',
            'total_amount' => 300,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 7,
        ]);

        Queue::assertPushed(OrderCreatedNotificationJob::class);
    }

    public function test_order_fails_when_stock_is_insufficient(): void
    {
        Queue::fake();

        $product = Product::factory()->create([
            'stock' => 2,
            'price' => 100,
            'is_active' => true,
        ]);

        $payload = [
            'payment_method' => 'cod',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                ],
            ],
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(422);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 2,
        ]);

        Queue::assertNotPushed(OrderCreatedNotificationJob::class);
    }
}