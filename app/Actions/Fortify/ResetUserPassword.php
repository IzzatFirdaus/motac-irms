<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Rules\CustomPasswordValidationRules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

class ResetUserPassword implements ResetsUserPasswords
{
    /**
     * Validate and reset the user's forgotten password.
     *
     * @param array<string, string> $input
     */
    public function reset(User $user, array $input): void
    {
        Validator::make($input, [
            // Use our custom password rules for reset
            'password' => CustomPasswordValidationRules::rules(),
        ])->validate();

        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }
}
