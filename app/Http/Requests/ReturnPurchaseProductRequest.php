<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReturnPurchaseProductRequest extends BaseFormRequest
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
            'ref_numbers' => 'required|array',
            'return_reference_number' => 'unique:return_purchased_products,return_reference_number',
            'supplier_id' => 'required|exists:suppliers,id',
            'apotek_id' => 'required|exists:apoteks,id',
            'total_returned_items' => 'required|numeric',
            'return_amount' => 'required|numeric',
            'return_date' => 'required|date',
            'return_status' => 'required|in:Pending,Completed,Rejected',
            'return_note' => 'nullable|string',
            'additional_note' => 'nullable|string'
        ];

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $rules['return_reference_number'] = 'unique:return_purchased_products,return_reference_number,' . $this->return_ref_num;
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'ref_numbers.required' => 'Produk yang diretur wajib dipilih.',
            'return_reference_numbers.unique' => 'Nomor REF Return harus bersifat unik.',
            'supplier_id.required' => 'Supplier wajib dipilih.',
            'apotek_id.required' => 'Apotek wajib dipilih.',
            'total_returned_items.required' => 'Total item retur wajib diisi.',
            'return_amount.required' => 'Jumlah retur wajib diisi.',
            'return_date.required' => 'Tanggal retur wajib diisi.',
            'return_status.required' => 'Status retur wajib diisi.',
            'return_status.in' => 'Status retur tidak valid.',
        ];
    }
}
