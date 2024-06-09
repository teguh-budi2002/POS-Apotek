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
        return [
            'name' => 'required',
            'contact_phone' => 'required|numeric|max:14',
            'gender' => 'required',
            'address' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama pelanggan wajib diisi.',
            'contact_phone' => [
                'required' => 'Kontak HP pelanggan wajib diisi.',
                'numeric' => 'Kontak HP harus berupa angka.',
                'max' => 'Nomer HP maksimal 14 angka.'
            ],
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'address.required' => 'Alamat pelanggan wajib diisi.'
        ];
    }
}
