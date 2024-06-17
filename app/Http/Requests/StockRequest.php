<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockRequest extends BaseFormRequest
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
        $rules = [
            'product_id' => 'required',
            'stock' => 'required|numeric',
            'minimum_stock_level' => 'required|numeric',
            'maximum_stock_level' => 'nullable|numeric',
        ];

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $rules['product_id'] = 'nullable';
            $rules['stock'] = 'nullable|numeric';
            $rules['minimum_stock_level'] = 'nullable|numeric';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'product_id.required' => 'Cantumkan produk yang ingin di tambahkan stock digital.',
            'stock' => [
                'required' => 'Cantum stock pada produk.',
                'numeric' => 'Stock harus berupa angka.'
            ],
            'minimum_stock_level' => [
                'required' => 'Mohon cantumkan minimum stock pada produk.',
                'numeric' => 'Minimum Stock harus berupa angka.'
            ],
            'maximum_stock_level' => [
                'numeric' => 'Maximum Stock harus berupa angka.'
            ],
        ];
    }
}
