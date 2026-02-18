<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
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
            'name' =>'required|string |min:3|max:255|regex:/^(?=.*\pL)[\pL\pN\s\-]+$/u',
            'phone' => 'required|string|regex:/^[0-9]{11,15}$/',
            'type' => 'required|string|in:permanent,walkin',
            'price_type' =>'required|string'
            
        ];
    }
    public function messages(){
        return [
            'name.required' => 'الاسم مطلوب',
            'name.min' => 'الاسم قصير',
            'name.max'=> 'الاسم طويل',
            'phone.required'=> 'الرقم مطلوب',
            'type.required'=> 'نوع العميل مطلوب',
            'price_type.required'=> 'نوع السعر مطلوب',
            'phone.regex'=> 'يحب ادخالي الرقم صحيح ولا يحتولي علي حروف عربي '
        ];
    }
}
