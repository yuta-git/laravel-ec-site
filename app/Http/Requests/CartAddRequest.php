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

}