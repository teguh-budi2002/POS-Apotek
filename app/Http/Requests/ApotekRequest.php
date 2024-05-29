<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApotekRequest extends BaseFormRequest
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
            "name_of_apotek" => "required",
            "email" => "required|email:dns",
            "contact_phone" => "required|numeric|regex:/^62[0-9]+$/|max_digits:14",
            "city" => "required",
            "province" => "required",
            "zip_code" => "required|numeric",
            "address" => "required",
            "bio" => "required"
        ];
    }

    public function messages()
    {
        return [
            'name_of_apotek.required' => "Nama Apotek wajib diisi.",
            "email" => [
                "required" => "Email wajib diisi.",
                "email" => "Email harus berupa email yang valid."
            ],
            "contact_phone" => [
                "required" => "Kontak HP wajib diisi.",
                "numeric" => "Kontak HP harus berupa angka.",
                "regex" => "Kontak HP harus berawalan 62.",
                "max_digits" => "Kontak HP maksimal 14 angka."
            ],
            "city.required" => "Kota wajib diisi.",
            "province.required" => "Provinsi wajib diisi.",
            "zip_code.required" => "Kode Pos wajib diisi.",
            "address.required" => "Alamat Lengkap wajib diisi.",
            "bio.required" => "Bio wajib diisi.",
        ];
    }
}
