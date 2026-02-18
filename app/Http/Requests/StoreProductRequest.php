<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'code' => 'required|string|max:10|unique:products,code',
            'name' =>'required|string|max:255|regex:/^(?=.*\pL)[\pL\pN\s\-]+$/u',
            'price_base' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
        ];
    }
    public function messages(): array
    {
        return [
            'code.required' => 'الكود مطلوب.',
            'code.unique' => 'الكود مستخدم من قبل.',
            'name.required' => 'الاسم مطلوب.',
            'name.regex' => 'الاسم يجب أن يحتوي على حروف وأرقام فقط.',
            'price_base.required' => 'سعر القاعدة مطلوب.',
            'category_id.required' => 'التصنيف مطلوب.',
            'category_id.exists' => 'التصنيف غير موجود.',
        ];
    }
}
