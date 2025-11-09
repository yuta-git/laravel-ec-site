<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
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
      'name' => 'required|string|max:255',
      'description' => 'nullable|string|max:2000',
      'price' => 'required|integer|min:0|max:99999999',
      'stock' => 'required|integer|min:0|max:999999',
      'category_id' => 'required|exists:categories,id',
      'main_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
      'sub_image_1' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
      'sub_image_2' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
    ];
  }
  
  /**
   * カスタムエラーメッセージ
   */
  public function messages(): array
  {
    return [];
  }

  public function attributes(): array
  {
    return [
      'name' => '商品名',
    ];
  }
}