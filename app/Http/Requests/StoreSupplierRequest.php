<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierRequest extends FormRequest
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
            'name' =>'required|string|max:255|regex:/^(?=.*\pL)[\pL\pN\s\-]+$/u',
            'phone' => 'required|string|max:15'
        ];
    }
    public function messages(){
        return [
            'name.required' => 'الاسم مطلوب',
            'name.max'=> 'الاسم طويل',
            'phone.required'=> 'الرقم مطلوب'
        ];
    }
}
