<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
    public function rules():array
    {
        return [
            'image'           => ['required', 'image', 'mimes:jpeg,png', 'max:2048'],
            'categories'      => ['required', 'array'],
            'categories.*'    => ['integer', 'exists:categories,id'],
            'condition'       => ['required', 'string'],
            'name'            => ['required', 'string'],
            'brand'           => ['nullable', 'string'],
            'description'     => ['required', 'string', 'max:255'],
            'price'           => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'image.required'         => '商品画像をアップロードしてください。',
            'image.image'            => '画像ファイルを選択してください。',
            'image.mimes'            => '画像はJPEGまたはPNG形式でアップロードしてください。',
            'image.max'              => '画像サイズは2MB以内にしてください。',
            'categories.required'    => 'カテゴリーを選択してください。',
            'categories.array'       => 'カテゴリーの形式が不正です。',
            'categories.*.exists'    => '選択されたカテゴリーが無効です。',
            'condition.required'     => '商品の状態を入力してください。',
            'name.required'          => '商品名を入力してください。',
            'description.required'   => '商品の説明を入力してください。',
            'description.max'        => '商品の説明は255文字以内で入力してください。',
            'price.required'         => '価格を入力してください。',
            'price.integer'          => '価格は数値で入力してください。',
            'price.min'              => '価格は0円以上で入力してください。',
        ];
    }
}
