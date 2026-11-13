<?php

namespace App\Jobs;

use App\Models\Product;
use App\Services\TelegramProductSoldOutNotifier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendProductSoldOutTelegramNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public int $productId)
    {
        $this->afterCommit = true;
    }

    public function handle(TelegramProductSoldOutNotifier $notifier): void
    {
        $product = Product::with('category')->findOrFail($this->productId);

        $notifier->send($product);
    }
}
