<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends BaseFormRequest
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
            'name' => 'required',
            'contact_phone' => 'required|numeric|regex:/^62[0-9]+$/|max_digits:14',
            'gender' => 'required',
            'address' => 'required'
        ];

        if(in_array($this->method(), ['PUT', 'PATCH'])) {
            $rules['name'] = 'nullable';
            $rules['contact_phone'] = 'nullable|numeric|regex:/^62[0-9]+$/|max_digits:14';
            $rules['gender'] = 'nullable';
            $rules['address'] = 'nullable';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama pelanggan wajib diisi.',
            'contact_phone' => [
                'required' => 'Kontak HP pelanggan wajib diisi.',
                'numeric' => 'Kontak HP harus berupa angka.',
                "regex" => "Kontak HP harus berawalan 62.",
                "max_digits" => "Kontak HP maksimal 14 angka."
            ],
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'address.required' => 'Alamat pelanggan wajib diisi.'
        ];
    }
}
