<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryProductRequest extends BaseFormRequest
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
            'name' => "required|unique:category_products,name,{$this->id}",
            'isActive' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'name' => [
                'required' => 'Nama wajib diisi.',
                'unique' => 'Nama kategori sudah terdaftar.'
            ],
            'isActive.boolean' => 'Status berupa boolean.'
        ];
    }
}
