<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'name' => ['required'],
            'price' => ['required',],
            'quantity' => ['required',],
            'categoryId' => ['sometimes', 'exists:categories,id'],
            'colorId' => ['sometimes', 'exists:colors,id'],
            'images' => ['array'],
            'images.*' => ['file', 'mimes:jpeg,png,jpg,gif,webp']
        ];
    }
}
