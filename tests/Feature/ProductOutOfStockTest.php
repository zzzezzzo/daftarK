<?php

use App\Http\Controllers\ProductController;
use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomerInvoiceItems;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\SupplierInvoiceItems;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

test('out of stock products require sales movement and are filtered by their latest depletion date', function () {
    $category = Category::create(['name' => 'Test category']);
    $customer = Customer::create([
        'name' => 'Test customer',
        'phone' => '1234567890',
        'type' => 'walkin',
    ]);
    $supplier = Supplier::create([
        'name' => 'Test supplier',
        'phone' => '1234567890',
    ]);

    $createProduct = function (string $code, string $name) use ($category) {
        return Product::create([
            'code' => $code,
            'name' => $name,
            'price_base' => 10,
            'price_trade' => 12,
            'price_technician' => 12,
            'price_customer' => 12,
            'category_id' => $category->id,
            'stock' => 0,
        ]);
    };

    $product = $createProduct('MOV-1', 'Moved product');
    $createProduct('NEW-1', 'New product');

    $supplierInvoiceNumber = 0;
    $recordPurchase = function (Product $product, string $date, int $quantity) use ($supplier, &$supplierInvoiceNumber) {
        $invoiceId = DB::table('supplier_invoices')->insertGetId([
            'invoice_number' => 'SUP-' . ++$supplierInvoiceNumber,
            'supplier_id' => $supplier->id,
            'type' => 'purchase',
            'date' => $date,
            'total_amount' => $quantity * 10,
            'paid_amount' => $quantity * 10,
            'Remaining_amount' => 0,
            'states' => 'paid',
            'created_at' => $date . ' 09:00:00',
            'updated_at' => $date . ' 09:00:00',
        ]);

        SupplierInvoiceItems::create([
            'supplier_invoice_id' => $invoiceId,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'unit_price' => 10,
        ]);
    };

    $recordSale = function (Product $product, string $date, int $quantity) use ($customer) {
        $invoiceId = DB::table('customer_invoices')->insertGetId([
            'invoice_number' => 'INV-' . $date,
            'customer_id' => $customer->id,
            'date' => $date,
            'total_amount' => $quantity * 12,
            'paid_amount' => $quantity * 12,
            'remining_amount' => 0,
            'state' => 'paid',
            'type' => 'payment',
            'created_at' => $date . ' 10:00:00',
            'updated_at' => $date . ' 10:00:00',
        ]);

        CustomerInvoiceItems::create([
            'customer_invoice_id' => $invoiceId,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'unit_price' => 12,
        ]);
    };

    $recordPurchase($product, '2026-03-01', 3);
    $recordSale($product, '2026-03-05', 3);
    $recordPurchase($product, '2026-04-01', 1);
    $recordSale($product, '2026-04-10', 1);

    $marchResponse = app(ProductController::class)->outOfStock(Request::create(
        '/products/out-of-stock',
        'GET',
        ['from_date' => '2026-03-01', 'to_date' => '2026-03-31']
    ));
    $aprilResponse = app(ProductController::class)->outOfStock(Request::create(
        '/products/out-of-stock',
        'GET',
        ['from_date' => '2026-04-01', 'to_date' => '2026-04-30']
    ));

    expect($marchResponse->getData()['products']->pluck('name')->all())->toBe([])
        ->and($aprilResponse->getData()['products']->pluck('name')->all())->toBe(['Moved product'])
        ->and($aprilResponse->getData()['products']->first()->depleted_at)->toBe('2026-04-10');
});
