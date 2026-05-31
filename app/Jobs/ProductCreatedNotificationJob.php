<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProductCreatedNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Product $product
    ) {}

    public function handle(): void
    {
        Log::info('Product created notification job executed', [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
        ]);
    }
}