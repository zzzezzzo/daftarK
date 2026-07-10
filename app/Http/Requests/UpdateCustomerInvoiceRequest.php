<?php

namespace App\Http\Requests;

use App\Models\CustomerInvoice;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;

class UpdateCustomerInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
                return [
            'date'        => ['required', 'date'],
            // عدّل القيم دي لو الأنواع الفعلية عندك مختلفة عن payment/return
            'type'        => ['required', 'in:payment,return'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'products'                  => ['required', 'array', 'min:1'],
            'products.*.id'             => ['nullable', 'integer', 'exists:customer_invoice_items,id'],
            'products.*.product_id'     => ['required', 'integer', 'exists:products,id'],
            'products.*.quantity'       => ['required', 'integer', 'min:1'],
        ];

    }
        public function messages(): array
    {
        return [
            'products.required'            => 'يجب إضافة منتج واحد على الأقل',
            'products.*.product_id.exists' => 'أحد المنتجات المختارة غير موجود',
            'products.*.quantity.min'      => 'الكمية يجب أن تكون أكبر من صفر',
        ];
    }
 
    public function withValidator(ValidatorContract $validator): void
    {
        $validator->after(function (ValidatorContract $validator) {
            $this->validateStockAvailability($validator);
        });
    }
        protected function validateStockAvailability(ValidatorContract $validator): void
    {
        $products = $this->input('products', []);
 
        if (empty($products) || $this->input('type', 'payment') !== 'payment') {
            return;
        }
 
        // ---- 1. جلب الفاتورة الحالية وكمياتها الأصلية ----
        // عدّل اسم الـ route parameter ده لو مختلف عندك (invoice / invoiceId / customerInvoice ...)
        $invoiceParam = $this->route('invoiceId') ?? $this->route('invoice') ?? $this->route('customerInvoice');
 
        if (!$invoiceParam) {
            // مفيش فاتورة في الراوت = حاجة غلط في استخدام الريكوست ده، اعتبره فاتورة جديدة بلا حجز
            $originalQuantities = collect();
        } else {
            $invoiceId = is_object($invoiceParam) ? $invoiceParam->id : $invoiceParam;
            $invoice = CustomerInvoice::with('items')->find($invoiceId);
 
            $originalQuantities = $invoice
                ? $invoice->items->groupBy('product_id')->map(fn ($items) => $items->sum('quantity'))
                : collect();
        }
 
        // ---- 2. تجميع الكميات المطلوبة في هذا الطلب لكل منتج ----
        $requestedQuantities = collect($products)
            ->filter(fn ($item) => isset($item['product_id'], $item['quantity']))
            ->groupBy('product_id')
            ->map(fn ($items) => collect($items)->sum('quantity'));
 
        if ($requestedQuantities->isEmpty()) {
            return;
        }
 
        // ---- 3. جلب المنتجات دفعة واحدة ----
        $productsCache = Product::whereIn('id', $requestedQuantities->keys())
            ->get()
            ->keyBy('id');
 
        // ---- 4. المقارنة الفعلية ----
        foreach ($requestedQuantities as $productId => $requestedQty) {
            $product = $productsCache->get($productId);
 
            if (!$product) {
                continue;
            }
 
            $originalQty    = $originalQuantities->get($productId, 0);
            $effectiveStock = $product->stock + $originalQty;
 
            if ($requestedQty > $effectiveStock) {
                $validator->errors()->add(
                    'products',
                    "الكمية المطلوبة للمنتج \"{$product->name}\" ({$requestedQty}) تتجاوز المتوفر ({$effectiveStock})"
                );
            }
        }
    }


}
