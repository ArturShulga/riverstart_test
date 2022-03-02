<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required',
            'vendor_code' => 'required',
            'price' => 'required|numeric',
            'categories' => 'required|array|min:2|max:10',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'validation_error',
            'data'      => $validator->errors()
        ]));
    }

    public function messages()
    {
        return [
            'title.required' => 'Название обязательно для заполнения',
            'vendor_code.required' => 'Артикул товара обязателен для заполнения',
            'price.required' => 'Цена обязательна для заполнения',
            'price.numeric' => 'Значения цены должно быть числовым',
            'categories.required' => 'Значение катогорий обязательно для заполнения',
            'categories.array' => 'Значение категорий должно передаваться массивом',
            'categories.min' => 'Минимальное количество категорий 2',
            'categories.max' => 'Максимальное количество категорий 10',
        ];
    }
}
