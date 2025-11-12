<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartAddRequest extends FormRequest
{
    /**
     * リクエストが認証されているかを判定
     */
    public function authorize(): bool
    {
        // 未ログインでも購入可能なので true
        return true;
    }

    /**
     * バリデーションルール
     */
    public function rules(): array
    {
        return [
            'product_id' => [
                'required',
                'integer',
                'exists:products,id'
            ],
            'quantity' => [
                'required',
                'integer',
                'min:1',
                'max:99'
            ]
        ];
    }

    /**
     * カスタムエラーメッセージ
     */
    public function messages(): array
    {
        return [
            'product_id.required' => '商品IDは必須です',
            'product_id.integer' => '商品IDは整数である必要があります',
            'product_id.exists' => '指定された商品が存在しません',
            'quantity.required' => '数量は必須です',
            'quantity.integer' => '数量は整数で入力してください',
            'quantity.min' => '数量は1以上で入力してください',
            'quantity.max' => '数量は99以下で入力してください'
        ];
    }

    /**
     * バリデーション失敗時の属性名
     */
    public function attributes(): array
    {
        return [
            'product_id' => '商品ID',
            'quantity' => '数量'
        ];
    }
}