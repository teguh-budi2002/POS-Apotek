<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends BaseFormRequest
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
            "supplier_name" => "required",
            "email" => "nullable|email:dns",
            "contact_phone" => "nullable|numeric|regex:/^62[0-9]+$/|max_digits:14",
            "city" => "required",
            "province" => "required",
            "zip_code" => "nullable|numeric",
            "address" => "nullable",
            "description" => "nullable"
        ];
    }

    public function messages()
    {
        return [
            'supplier_name.required' => "Nama Supplier wajib diisi.",
            "email.email" => "Email harus berupa email yang valid.",
            "contact_phone" => [
                "numeric" => "Kontak HP harus berupa angka.",
                "regex" => "Kontak HP harus berawalan 62.",
                "max_digits" => "Kontak HP maksimal 14 angka."
            ],
            "city.required" => "Kota wajib diisi.",
            "province.required" => "Provinsi wajib diisi.",
        ];
    }
}
