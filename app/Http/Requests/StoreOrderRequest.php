<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    // このリクエストを誰でも使えるようにする
    public function authorize(): bool
    {
        return true; // 認可を別でやる場合は true 固定でOK
    }

    // バリデーションルール
    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:100'],
            'phone_number'  => ['required', 'string', 'max:20'],
            'address'       => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [];
    }
}