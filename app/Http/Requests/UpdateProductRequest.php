<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0', 'regex:/^\d+(\.\d{1,2})?$/'],
            'currency_id' => ['sometimes', 'required', 'integer', 'exists:currencies,id'],
            'tax_cost' => ['sometimes', 'required', 'numeric', 'min:0', 'regex:/^\d+(\.\d{1,2})?$/'],
            'manufacturing_cost' => ['sometimes', 'required', 'numeric', 'min:0', 'regex:/^\d+(\.\d{1,2})?$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The product name is required.',
            'name.string' => 'The product name must be a string.',
            'name.max' => 'The product name may not exceed 255 characters.',
            'description.string' => 'The description must be a string.',
            'price.required' => 'The price is required.',
            'price.numeric' => 'The price must be a number.',
            'price.min' => 'The price cannot be negative.',
            'price.regex' => 'The price must have at most 2 decimal places.',
            'currency_id.required' => 'The currency is required.',
            'currency_id.integer' => 'The currency must be an integer.',
            'currency_id.exists' => 'The selected currency does not exist.',
            'tax_cost.required' => 'The tax cost is required.',
            'tax_cost.numeric' => 'The tax cost must be a number.',
            'tax_cost.min' => 'The tax cost cannot be negative.',
            'tax_cost.regex' => 'The tax cost must have at most 2 decimal places.',
            'manufacturing_cost.required' => 'The manufacturing cost is required.',
            'manufacturing_cost.numeric' => 'The manufacturing cost must be a number.',
            'manufacturing_cost.min' => 'The manufacturing cost cannot be negative.',
            'manufacturing_cost.regex' => 'The manufacturing cost must have at most 2 decimal places.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'product name',
            'description' => 'description',
            'price' => 'price',
            'currency_id' => 'currency',
            'tax_cost' => 'tax cost',
            'manufacturing_cost' => 'manufacturing cost',
        ];
    }
}
