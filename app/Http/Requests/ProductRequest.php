<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends BaseFormRequest
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
            'category_product_id' => 'required|exists:category_products,id',
            'unit_product_id' => 'required|exists:unit_products,id',
            'product_code' => 'required|unique:products,product_code',
            'name' => 'required',
            'unit_price' => 'required|numeric',
            'profit_margin' => 'required|numeric',
            'unit_selling_price' => 'required|numeric',
            'description' => 'nullable|string',
            'img_product' => 'required|file|max:2048|mimes:png,jpg,webp',
            'stock' => 'required|numeric',
            'minimum_stock_level' => 'required|numeric'
        ];

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $rules['category_product_id'] = 'nullable|exists:category_products,id';
            $rules['unit_product_id'] = 'nullable|exists:unit_product_id,id';
            $rules['product_code'] = 'nullable|unique:products,product_code,' . $this->id;
            $rules['name'] = 'nullable|string|max:255';
            $rules['unit_price'] = 'nullable|numeric|min:0';
            $rules['profit_margin'] = 'nullable|numeric|min:0';
            $rules['unit_selling_price'] = 'nullable|numeric|min:0';
            $rules['description'] = 'nullable|string';
            $rules['img_product'] = 'nullable|file|max:2048|mimes:png,jpg,webp';
            $rules['stock'] = 'nullable|numeric';
            $rules['minimum_stock_level'] = 'nullable|numeric';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'category_product_id.required' => 'Kategori wajib dipilih.',
            'unit_product_id.required' => 'Unit Produk wajib dipilih.',
            'product_code' => [
                'required' => 'Cantumkan kode produk.',
                'unique' => 'Kode produk sudah terdaftar.'
            ],
            'name.required' => 'Nama wajib diisi.',
            'unit_price' => [
                'required' => 'Harga pembelian produk (satuan) wajib diisi.',
                'numeric' => 'Harga pembelian produk (satuan) harus berupa angka.'
            ],
            'profit_margin' => [
                'required' => 'Profit Margin wajib diisi.',
                'numeric' => 'Profit Margin harus berupa angka.'
            ],
            'unit_selling_price' => [
                'required' => 'Harga penjualan product (satuan) wajib diisi.',
                'numeric' => 'Harga penjualan product (satuan) harus berupa angka.'
            ],
            'stock' => [
                'required' => 'Stok produk wajib diisi.',
                'numeric' => 'Stok harus berupa angka.'
            ],
            'minimum_stock_level' => [
                'required' => 'Minimum stok produk wajib diisi.',
                'numeric' => 'Minimum stok harus berupa angka.'
            ],
            'img_product' => [
                "required" => 'Produk harus memiliki foto.',
                'file' => 'Berkas yang di upload harus berupa file gambar.',
                'uploaded' => 'Maksimal ukuran gambar adalah 2Mb.',
                'mimes' => 'Ekstensi yang di izinkan (jpg,png,webp).'
            ],
        ];
    }
}
