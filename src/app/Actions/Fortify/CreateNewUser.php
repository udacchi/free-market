<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use App\Http\Requests\RegisterRequest;

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

        return User::create([
            'name' => $input['name'] ?? '',
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
    protected function passwordRules()
    {
        return ['required', 'string', 'min:8', 'confirmed'];
    }
}
