<?php

use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomerInvoice;
use App\Models\CustomerInvoiceItems;
use App\Models\Product;
use App\Services\TelegramProductSoldOutNotifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

test('telegram sold-out alert includes product details and counts sales from the last 30 days', function () {
    config([
        'services.telegram.bot_token' => 'test-token',
        'services.telegram.admin_chat_id' => '123456',
    ]);
    Http::fake([
        'api.telegram.org/*' => Http::response(['ok' => true], 200),
    ]);

    $category = Category::create(['name' => 'Electronics']);
    $customer = Customer::create([
        'name' => 'Test customer',
        'phone' => '1234567890',
        'type' => 'walkin',
    ]);
    $product = Product::create([
        'code' => 'TEL-1',
        'name' => 'Popular product',
        'price_base' => 10,
        'price_trade' => 12,
        'price_technician' => 12,
        'price_customer' => 12,
        'category_id' => $category->id,
        'stock' => 0,
    ]);

    $createSale = function (string $date, int $quantity) use ($customer, $product) {
        $invoice = CustomerInvoice::create([
            'invoice_number' => 'INV-' . $date,
            'customer_id' => $customer->id,
            'date' => $date,
            'total_amount' => $quantity * 12,
            'paid_amount' => $quantity * 12,
            'remining_amount' => 0,
            'state' => 'paid',
            'type' => 'payment',
        ]);

        CustomerInvoiceItems::create([
            'customer_invoice_id' => $invoice->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'unit_price' => 12,
        ]);
    };

    $createSale(now()->subDays(31)->toDateString(), 100);
    $createSale(now()->subDays(10)->toDateString(), 6);
    $createSale(now()->toDateString(), 4);

    app(TelegramProductSoldOutNotifier::class)->send($product);

    Http::assertSent(function (ClientRequest $request) {
        return str_ends_with($request->url(), '/sendMessage')
            && $request['chat_id'] === '123456'
            && str_contains($request['text'], 'Popular product')
            && str_contains($request['text'], 'TEL-1')
            && str_contains($request['text'], 'Electronics')
            && str_contains($request['text'], 'الكمية المباعة خلال آخر 30 يومًا: 10')
            && str_contains($request['text'], 'نعم، سريع البيع');
    });
});
