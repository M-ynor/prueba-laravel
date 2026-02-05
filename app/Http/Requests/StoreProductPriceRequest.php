<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'currency_id' => ['required', 'integer', 'exists:currencies,id'],
            'price' => ['required', 'numeric', 'min:0', 'regex:/^\d+(\.\d{1,2})?$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'currency_id.required' => 'The currency is required.',
            'currency_id.integer' => 'The currency must be an integer.',
            'currency_id.exists' => 'The selected currency does not exist.',
            'price.required' => 'The price is required.',
            'price.numeric' => 'The price must be a number.',
            'price.min' => 'The price cannot be negative.',
            'price.regex' => 'The price must have at most 2 decimal places.',
        ];
    }

    public function attributes(): array
    {
        return [
            'currency_id' => 'currency',
            'price' => 'price',
        ];
    }
}
