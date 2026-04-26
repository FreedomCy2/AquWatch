<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'role' => ['nullable', Rule::in(['user', 'admin'])],
            'admin_code' => ['nullable', 'string'],
            'password' => $this->passwordRules(),
        ])->after(function ($validator) use ($input): void {
            $role = strtolower((string) ($input['role'] ?? 'user'));

            if ($role !== 'admin') {
                return;
            }

            $expectedCode = (string) config('auth.admin_registration_code', '');
            $providedCode = (string) ($input['admin_code'] ?? '');

            if ($expectedCode === '' || ! hash_equals($expectedCode, $providedCode)) {
                $validator->errors()->add('admin_code', 'The admin code is invalid.');
            }
        })->validate();

        $role = strtolower((string) ($input['role'] ?? 'user'));

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
            'role' => $role,
        ]);
    }
}
