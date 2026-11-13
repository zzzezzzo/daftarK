<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class TelegramProductSoldOutNotifier
{
    public function send(Product $product): void
    {
        $soldUnits = $product->saleItems()
            ->whereHas('invoice', function ($query) {
                $query->where('type', 'payment')
                    ->whereBetween('date', [
                        now()->subDays(29)->toDateString(),
                        now()->toDateString(),
                    ]);
            })
            ->sum('quantity');

        $highDemandStatus = $soldUnits >= 10 ? 'نعم، سريع البيع' : 'لا';
        $message = implode("\n", [
            'تنبيه: نفد مخزون المنتج بالكامل بعد عملية بيع.',
            'اسم المنتج: ' . $product->name,
            'الكود: ' . $product->code,
            'الفئة: ' . ($product->category?->name ?? 'بدون فئة'),
            'المخزون المتبقي: 0',
            'الكمية المباعة خلال آخر 30 يومًا: ' . $soldUnits,
            'منتج سريع البيع (10 وحدات أو أكثر): ' . $highDemandStatus,
        ]);

        try {
            $response = Http::timeout(10)->post(
                'https://api.telegram.org/bot' . config('services.telegram.bot_token') . '/sendMessage',
                [
                    'chat_id' => config('services.telegram.admin_chat_id'),
                    'text' => $message,
                ]
            );
        } catch (ConnectionException) {
            throw new RuntimeException('Unable to connect to Telegram while sending a product sold-out alert.');
        }

        if (!$response->successful() || $response->json('ok') !== true) {
            $description = $response->json('description', 'Telegram rejected the request.');

            throw new RuntimeException(
                'Telegram product sold-out alert failed (HTTP ' . $response->status() . '): ' . $description
            );
        }
    }
}
