<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryStoreRequest extends FormRequest
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
      // 新規作成用
      'name' => 'required|string|max:255',
      'sort_order' => 'required|integer|min:0',

      // 既存カテゴリの更新用
      'categories.*.name' => 'required|string|max:255',
      'categories.*.sort_order' => 'required|integer|min:0',
    ];
  }

  /**
   * カスタムエラーメッセージ
   */
  public function messages(): array
  {
    return [];
  }

  /**
   * 属性名の日本語化
   */
  public function attributes(): array
  {
    return [
      'categories.*.name' => 'カテゴリ名',
      'categories.*.sort_order' => 'ソート順',
    ];
  }
}