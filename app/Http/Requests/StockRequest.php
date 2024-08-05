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

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $this->all();
             $hasMaximumStockLevel = isset($data['maximum_stock_level']) && $data['maximum_stock_level'] !== null;

            if ($data['minimum_stock_level'] > $data['stock'] || ($hasMaximumStockLevel && $data['minimum_stock_level'] > $data['maximum_stock_level'])) {
                $validator->errors()->add('minimum_stock_level', 'Minimum Stock Tidak Boleh Lebih Besar Dari Stock & Max Stock');
            }
            if ($hasMaximumStockLevel && ($data['maximum_stock_level'] < $data['stock'] || $data['maximum_stock_level'] < $data['minimum_stock_level'])) {
                $validator->errors()->add('maximum_stock_level', 'Maximum Stock Tidak Boleh Lebih Kecil Dari Stock & Max Stock');
            }
        });
    }
}
