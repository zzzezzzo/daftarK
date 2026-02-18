<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchForCustomerRequest extends FormRequest
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
            'search' => ['nullable', 'string', 'regex:/^[\p{L}\s]+$/u'],
        ];
    }
    public function messages(){
        return [
            'search.regex' => 'البحث لازم يكون بالاسم فقط (حروف ومسافات فقط)',
        ];
    }
}
