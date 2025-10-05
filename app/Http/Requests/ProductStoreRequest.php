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
    return [
      'category_id.required' => 'カテゴリは必須です',
      'name.required' => '商品名は必須です',
      'name.max' => '商品名は255文字以内で入力してください',
      'price.required' => '価格は必須です',
      'price.integer' => '価格は整数で入力してください',
      'price.min' => '価格は0円以上で入力してください',
      'stock.required' => '在庫数は必須です',
      'stock.integer' => '在庫数は整数で入力してください',
      'stock.min' => '在庫数は0以上で入力してください',
      'main_image.image' => 'メイン画像は画像ファイルを指定してください',
      'main_image.mimes' => 'メイン画像はjpeg、png、jpg、webp形式のみ対応しています',
      'main_image.max' => 'メイン画像は2MB以下のファイルを指定してください',
    ];
  }
}