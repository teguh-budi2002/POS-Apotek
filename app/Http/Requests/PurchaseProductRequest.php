<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseProductRequest extends BaseFormRequest
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
            'apotek_id' => 'required',
            'supplier_id' => 'required',
            'invoice' => 'unique:purchase_products,invoice',
            'grand_total' => 'required|numeric',
            'sub_total' => 'required|numeric',
            'selling_price' => 'required',
            'profit_margin' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'payment_method' => 'required',
            // 'status_order' => 'required',
            'status_payment' => 'required',
            'order_date' => 'required|date',
            'paid_on' => 'nullable|date',
            'tax_amount' => 'nullable|numeric',
            'shipping_fee' => 'nullable|numeric',
            'shipping_details' => 'nullable|string',
            'order_notes' => 'nullable|string',
            'proof_of_payment' => 'nullable|file|max:2048|mimes:png,jpg,webp'
        ];
    }

    public function messages()
    {
        return [
            'apotek_id.required' => 'Tentukan lokasi apotek.',
            'supplier_id.required' => 'Tentukan supplier produk.',
            'invoice' => [
                'unique' => 'Invoice harus bersfiat unique.'
            ],
            'grand_total' => [
                'required' => 'Grand Total tidak boleh kosong.',
                'numeric' => 'Grand Total harus berupa angka.'
            ],
            'sub_total' => [
                'required' => 'Sub Total tidak boleh kosong.',
                'numeric' => 'Sub Total harus berupa angka.'
            ],
            'selling_price.required' => 'Harga jual tidak boleh kosong.',
            'profit_margin' => [
                'required' => 'Profit margin tidak boleh kosong.',
                'numeric' => 'Profit margin harus berupa angka.'
            ],
            'discount.numeric' => 'Diskon harus berupa angka.',
            'payment_method.required' => 'Metode pembayaran tidak boleh kosong.',
            // 'status_order.required' => 'Status order tidak boleh kosong.',
            'status_payment.required' => 'Status pembayaran tidak boleh kosong.',
            'order_date.required' => 'Tentukan tanggal order.',
            'tax_amount.numeric' => 'Pajak harus berupa angka.',
            'shipping_fee.numeric' => 'Pajak pengiriman harus berupa angka.',
            'proof_of_payment' => [
                'file' => 'Bukti pembayaran harus berupa file gambar.',
                'uploaded' => 'Maksimal ukuran gambar adalah 2Mb.',
                'mimes' => 'Ekstensi yang di izinkan (jpg,png,webp).'
            ]
        ];
    }
}
