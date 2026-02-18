<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Product;

class StoreSupplierInvoiceRequest extends FormRequest
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
            'date' => 'required|date',
            'type' => 'required|in:purchase,return',
            'states' => 'required|in:paid,partially_paid,unpaid',

            'paid_amount' => 'nullable|numeric|min:0',
            'paymentMethod' => 'nullable|string',

            'products' => 'required|array|min:1',

            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    $productId = $this->input(str_replace('.quantity', '.product_id', $attribute));
                    $product = Product::find($productId);
                    
                    if ($product && $this->input('type') === 'return' && $value > $product->stock) {
                        $fail("الكمية المطلوبة ({$value}) تتجاوز المتوفر في المخزون ({$product->stock}) للمنتج: {$product->name}");
                    }
                }
            ],
            'products.*.unit_price' => 'required|numeric|min:0',
        ];
    }
    public function messages(){
        return [
            'date.required' => 'التاريخ مطلوب',
            'type.required' => 'نوع الفاتورة مطلوب',
            'states.required' => 'حالة الفاتورة مطلوبة',

            'products.required' => 'لازم تضيف منتج واحد على الأقل',
            'products.array' => 'صيغة المنتجات غير صحيحة',

            'products.*.product_id.required' => 'اختيار المنتج مطلوب',
            'products.*.product_id.exists' => 'المنتج غير موجود',

            'products.*.quantity.required' => 'الكمية مطلوبة',
            'products.*.quantity.min' => 'الكمية لا يمكن أن تكون أقل من 1',

            'products.*.unit_price.required' => 'سعر الوحدة مطلوب',
            'products.*.unit_price.min' => 'السعر لا يمكن أن يكون أقل من صفر',
        ];
    }
    public function withValidator($validator)
{
    $validator->after(function ($validator) {

        $products = $this->products ?? [];
        $paid = $this->paid_amount ?? 0;

        // حساب total من المنتجات
        $total = 0;
        foreach ($products as $item) {
            $qty = $item['quantity'] ?? 0;
            $price = $item['unit_price'] ?? 0;
            $total += $qty * $price;
        }
        if ($paid > $total) {
            $validator->errors()->add(
                'paid_amount',
                'لا يمكن دفع مبلغ أكبر من إجمالي الفاتورة (' . $total . ')'
            );
        }
        $state = $this->states;
        if ($state === 'paid' && $paid < $total) {
            $validator->errors()->add(
                'states',
                'الحالة "مدفوع" تتطلب دفع كامل المبلغ'
            );
        }
        if ($state === 'unpaid' && $paid > 0) {
            $validator->errors()->add(
                'states',
                'لا يمكن الدفع مع حالة "غير مدفوع"'
            );
        }
        if ($state === 'partially_paid' && ($paid <= 0 || $paid >= $total)) {
            $validator->errors()->add(
                'states',
                'الدفع الجزئي يجب أن يكون بين 0 و ' . $total
            );
        }
    });
}
}

