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
            'postal'   => ['nullable', 'string', 'max:255'],
            'address'  => ['nullable', 'string', 'max:255'],
            'building' => ['nullable', 'string', 'max:255'],
            'avatar'   => ['nullable', 'image', 'mimes:jpeg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.image'   => '画像ファイルを選択してください。',
            'avatar.mimea'   => 'プロフィール画像はJPEGまたはPNG形式でアップロードしてください。',
            'avatar.max'     => 'プロフィール画像は2MB以内にしてください。',
        ];
    }
}
