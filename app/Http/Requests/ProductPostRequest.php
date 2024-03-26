<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NoNegativeValue;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductPostRequest extends FormRequest
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
            'description' => 'required|max:500',
            'price' => ['required', 'numeric', new NoNegativeValue],
            'quantity' => ['required', 'integer', new NoNegativeValue],
            'category_id' => ['required', 'integer', new NoNegativeValue],
            'brand_id' => ['required', 'integer', new NoNegativeValue],
            'image' => 'image|mimes:png,jpg,jpeg|max:2048',
            // 'images' => 'max:5'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Product name must be filled',
            'description.required' => 'Description must be filled',
            'description.max' => 'Description must not exceed 500 characters',
            'price.required' => 'Price must not be empty',
            'price.numeric' => 'Price must be in numeric value',
            'quantity.required' => 'Quantity must not be empty',
            'quantity.integer' => 'Quantity must be in integer value',
            'category_id.required' => 'Category_id must not be empty',
            'category_id.integer' => 'Category_id must be in integer value',
            'brand_id.required' => 'Brand_id must not be empty',
            'brand_id.integer' => 'Brand_id must be in integer value',
            'image.max' => 'Image size should be less than 2mb',
            'image.mimes' => 'Only jpeg, png, jpg files are allowed',
            // 'images.max' => 'Only 5 images are allowed'
        ];
    }

    public function FailedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'data' => $validator->errors()
        ]));
    }
}
