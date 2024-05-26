<?php

namespace App\Http\Requests;

class RegisterRequest extends BaseFormRequest
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
            'email' => 'required|email:dns|unique:users,email',
            'password' => 'required|min:6'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'email' => [
                'required' => 'Email wajib diisi.',
                'email' => 'Email harus berupa email yang valid.',
                'unique' => 'Email sudah terdaftar'
            ],
            'password' => [
                'required' => 'Password wajib diisi.',
                'min' => 'Password harus memiliki minimal 6 karakter'
            ]
        ];
    }
}
