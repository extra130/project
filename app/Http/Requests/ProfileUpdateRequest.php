<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '顯示名稱為必填欄位。',
            'name.string' => '顯示名稱必須是有效的字串。',
            'name.max' => '顯示名稱不能超過 255 個字元。',
            'email.required' => '電子郵件為必填欄位。',
            'email.email' => '電子郵件格式不正確。',
            'email.unique' => '這個電子郵件已經被註冊過了。',
            'email.max' => '電子郵件不能超過 255 個字元。',
        ];
    }
}
