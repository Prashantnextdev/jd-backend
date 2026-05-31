<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class OrderCreatedNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order
    ) {}

    public function handle(): void
    {
        Log::info('Order created notification sent', [
            'order_id' => $this->order->id,
            'total_amount' => $this->order->total_amount,
            'status' => $this->order->status,
        ]);
    }
}