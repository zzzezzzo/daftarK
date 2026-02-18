<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Product;

class StoreCustomerInvoiceRequest extends FormRequest
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
            'type' => 'required|in:payment,return',
            
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => [
                'required',
                'numeric',
                'min:1',
                function ($attribute, $value, $fail) {
                    $productId = $this->input(str_replace('.quantity', '.product_id', $attribute));
                    $product = Product::find($productId);
                    
                    if ($product && $this->input('type') === 'payment' && $value > $product->stock) {
                        $fail("الكمية المطلوبة ({$value}) تتجاوز المتوفر في المخزون ({$product->stock}) للمنتج: {$product->name}");
                    }
                }
            ],
            // 'products.*.unit_price' => 'required|numeric|min:0',

            'paid_amount' => 'nullable|numeric|min:0',
            'paymentMethod' => 'nullable|string',
            'states' => 'nullable|in:paid,partial,unpaid',
        ];
    }
    public function messages(): array
    {
        return [
            'date.required' => 'يجب تحديد تاريخ الفاتورة.',
            'type.required' => 'يجب اختيار نوع الفاتورة.',
            'products.required' => 'يجب إضافة منتج واحد على الأقل.',
            'products.*.product_id.required' => 'اختر المنتج.',
            'products.*.product_id.exists' => 'المنتج المختار غير موجود.',
            'products.*.quantity.required' => 'ادخل كمية صحيحة.',
            'products.*.quantity.min' => 'الكمية يجب أن تكون أكبر من صفر.',
            'products.*.unit_price.required' => 'ادخل سعر الوحدة.',
            'paid_amount.numeric' => 'المبلغ المدفوع يجب أن يكون رقم.',
            'states.in' => 'حالة الدفع غير صحيحة.',
        ];
    }
}
