<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Events\Registered;


class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */

    public function create(array $input): User
    {
        Validator::make($input, (new RegisterRequest())->rules())->validate();

        $user = User::create([
            'name' => $input['name'] ?? '',
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        // 🔽 メール認証イベントを発火（これが超重要）
        event(new Registered($user));

        // 🔽 自動ログイン（問題ありません）
        Auth::login($user);

        // ❌ redirectは削除：FortifyがHOMEへリダイレクトしてくれる
        // redirect(URL::to('/mypage/profile'))->send();

        return $user;
    }

    protected function passwordRules()
    {
        return ['required', 'string', 'min:8', 'confirmed'];
    }
}
