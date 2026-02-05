<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Update Product Request
 * 
 * Validates data for updating an existing product.
 */
class UpdateProductRequest extends FormRequest
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
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0', 'regex:/^\d+(\.\d{1,2})?$/'],
            'currency_id' => ['sometimes', 'required', 'integer', 'exists:currencies,id'],
            'tax_cost' => ['sometimes', 'required', 'numeric', 'min:0', 'regex:/^\d+(\.\d{1,2})?$/'],
            'manufacturing_cost' => ['sometimes', 'required', 'numeric', 'min:0', 'regex:/^\d+(\.\d{1,2})?$/'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del producto es obligatorio.',
            'name.string' => 'El nombre del producto debe ser texto.',
            'name.max' => 'El nombre del producto no puede exceder 255 caracteres.',
            'description.string' => 'La descripción debe ser texto.',
            'price.required' => 'El precio es obligatorio.',
            'price.numeric' => 'El precio debe ser un número.',
            'price.min' => 'El precio no puede ser negativo.',
            'price.regex' => 'El precio debe tener máximo 2 decimales.',
            'currency_id.required' => 'La divisa es obligatoria.',
            'currency_id.integer' => 'La divisa debe ser un número entero.',
            'currency_id.exists' => 'La divisa seleccionada no existe.',
            'tax_cost.required' => 'El costo de impuestos es obligatorio.',
            'tax_cost.numeric' => 'El costo de impuestos debe ser un número.',
            'tax_cost.min' => 'El costo de impuestos no puede ser negativo.',
            'tax_cost.regex' => 'El costo de impuestos debe tener máximo 2 decimales.',
            'manufacturing_cost.required' => 'El costo de fabricación es obligatorio.',
            'manufacturing_cost.numeric' => 'El costo de fabricación debe ser un número.',
            'manufacturing_cost.min' => 'El costo de fabricación no puede ser negativo.',
            'manufacturing_cost.regex' => 'El costo de fabricación debe tener máximo 2 decimales.',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'description' => 'descripción',
            'price' => 'precio',
            'currency_id' => 'divisa',
            'tax_cost' => 'costo de impuestos',
            'manufacturing_cost' => 'costo de fabricación',
        ];
    }
}
