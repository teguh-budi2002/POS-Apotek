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
            'reference_number' => 'unique:purchase_products,reference_number',
            'productData.*.total_price' => 'required',
            'grand_total' => 'required|numeric',
            'qty' => 'nullable|array',
            'price_after_discount' => 'nullable|array',
            'productData.*.selling_price' => 'required',
            'productData.*.profit_margin' => 'required',
            'discount' => 'nullable|array',
            'productData.*.expired_date_product' => 'required',
            'payment_method' => 'required',
            'status_payment' => 'nullable',
            'order_date' => 'required|date',
            'status_order' => 'required',
            'paid_on' => 'nullable|date',
            'tax' => 'nullable|array',
            'shipping_cost' => 'nullable|numeric',
            'shipping_details' => 'nullable|string',
            'order_notes' => 'nullable|string',
            'termin_payment' => 'nullable|numeric',
            'proof_of_payment' => 'nullable|file|max:2048|mimes:png,webp',
            'purchase_invoice' => 'required|file|max:2048|mimes:png,webp'
        ];
    }

    public function messages()
    {
        return [
            'apotek_id.required' => 'Tentukan lokasi apotek.',
            'supplier_id.required' => 'Tentukan supplier produk.',
            'reference_number' => [
                'unique' => 'Nomor Referensi harus bersfiat unik.'
            ],
            'grand_total' => [
                'required' => 'Grand Total (Total Harga) tidak boleh kosong.',
                'numeric' => 'Grand Total (Total Harga) harus berupa angka.'
            ],
            'price_after_discount' => [
                'numeric' => 'Harga setelah diskon harus berupa angka.'
            ],
            'productData.*.selling_price.required' => 'Harga jual per unit tidak boleh kosong.',
            'productData.*.profit_margin.required' => 'Profit margin tidak boleh kosong.',
            'productData.*.expired_date_product.required' => 'Cantumkan masa kadaluarsa pada produk.',
            'productData.*.total_price.required' => 'Total harga setiap produk wajib diisi.',
            'payment_method.required' => 'Metode pembayaran tidak boleh kosong.',
            'status_payment.required' => 'Status pembayaran tidak boleh kosong.',
            'order_date.required' => 'Tentukan tanggal order.',
            'status_order.required' => 'Tentukan status order.',
            'shipping_cost.numeric' => 'Pajak pengiriman harus berupa angka.',
            'termin_payment.numeric' => 'Format termin harus berupa angka.',
            'proof_of_payment' => [
                'uploaded' => 'Maksimum ukuran berkas sebesar 2mb',
                'mimes' => 'Ekstensi file yang di-izinkan png,webp'
            ],
            'purchase_invoice' => [
                'required' => 'Bukti pembelian wajib diunggah',
                'uploaded' => 'Maksimum ukuran berkas sebesar 2mb',
                'mimes' => 'Ekstensi file yang di-izinkan png,webp'
            ]
        ];
    }
}
