<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'postal'   => ['nullable', 'string', 'regex:/\A\d{3}-?\d{4}\z/'],
            'address'  => ['nullable', 'string', 'max:255'],
            'building' => ['nullable', 'string', 'max:255'],
            'avatar'   => ['nullable', 'image', 'mimes:jpeg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.image'   => '画像ファイルを選択してください。',
            'avatar.mimes'   => 'プロフィール画像はJPEGまたはPNG形式でアップロードしてください。',
            'avatar.max'     => 'プロフィール画像は2MB以内にしてください。',
            'name.required'  => 'お名前を入力してください。',
            'name.max'       => 'お名前は255文字以内で入力してください。',
            'postal.regex'   => '郵便番号は 123-4567（ハイフン任意）の形式で入力してください。',
            'address.max'    => '住所は255文字以内で入力してください。',
            'building.max'   => '建物名は255文字以内で入力してください。',
        ];
    }
}
