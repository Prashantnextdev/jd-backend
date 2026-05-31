<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_can_be_listed(): void
    {
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
    }

    public function test_product_can_be_created(): void
    {
        $payload = [
            'name' => 'iPhone 15',
            'description' => 'Apple phone',
            'price' => 70000,
            'stock' => 10,
            'is_active' => true,
        ];

        $response = $this->postJson('/api/products', $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('products', [
            'name' => 'iPhone 15',
            'stock' => 10,
        ]);
    }
}