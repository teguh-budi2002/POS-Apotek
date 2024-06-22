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
            'grand_total' => 'required|numeric',
            'sub_total' => 'required|numeric',
            'qty' => 'required|array',
            'qty.*' => 'numeric',
            'price_after_discount' => 'nullable|numeric',
            'unit_price' => 'required',
            'profit_margin' => 'required|array',
            'profit_margin.*' => 'numeric',
            'discount' => 'nullable|array',
            'discount.*' => 'numeric',
            'expired_date' => 'required|array',
            'expired_date.*' => 'date',
            'payment_method' => 'required',
            'status_payment' => 'required',
            'order_date' => 'required|date',
            'paid_on' => 'nullable|date',
            'tax' => 'nullable|array',
            'tax.*' => 'numeric',
            'shipping_cost' => 'nullable|numeric',
            'shipping_details' => 'nullable|string',
            'order_notes' => 'nullable|string',
            'proof_of_payment' => 'nullable|file|max:2048|mimes:png,jpg,webp',
            'termin_payment' => 'nullable|numeric',
            'payment_invoice' => 'required|file|max:2048|mimes:docx,png,jpg'
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
                'required' => 'Grand Total tidak boleh kosong.',
                'numeric' => 'Grand Total harus berupa angka.'
            ],
            'sub_total' => [
                'required' => 'Sub Total tidak boleh kosong.',
                'numeric' => 'Sub Total harus berupa angka.'
            ],
            'qty.required' => 'Cantumkan quantity pada produk.',
            'qty.*.numeric' => 'Quantity harus berupa angka.',
            'price_after_discount' => [
                'numeric' => 'Harga setelah diskon harus berupa angka.'
            ],
            'unit_price.required' => 'Harga jual per unit tidak boleh kosong.',
            'profit_margin.required' => 'Profit margin tidak boleh kosong.',
            'profit_margin.*.numeric' => 'Profit margin harus berupa angka.',
            'discount.*.numeric' => 'Diskon harus berupa angka.',
            'expired_date.required' => 'Cantumkan masa kadaluarsa pada produk.',
            'expired_date.*.date' => 'Format tanggal tidak valid.',
            'payment_method.required' => 'Metode pembayaran tidak boleh kosong.',
            'status_payment.required' => 'Status pembayaran tidak boleh kosong.',
            'order_date.required' => 'Tentukan tanggal order.',
            'tax.*.numeric' => 'Pajak harus berupa angka.',
            'shipping_cost.numeric' => 'Pajak pengiriman harus berupa angka.',
            'proof_of_payment' => [
                'file' => 'Bukti pembayaran harus berupa file gambar.',
                'uploaded' => 'Maksimal ukuran gambar adalah 2Mb.',
                'mimes' => 'Ekstensi yang di izinkan (jpg,png,webp).'
            ],
            'termin_payment.numeric' => 'Format termin harus berupa angka.',
            'payment_invoice' => [
                'required' => 'Faktur pembelian wajib di upload.',
                'uploaded' => 'Maksimum ukuran berkas sebesar 2mb',
                'mimes' => 'Ekstensi file yang di-izinkan docx,jpg,png'
            ]
        ];
    }
}
