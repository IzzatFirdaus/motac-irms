<?php

namespace App\Rules;

use Illuminate\Validation\Rules\Password as IlluminatePasswordRule;

/**
 * Provides standardized password validation rules for user creation and update.
 * Compatible with v4.0 (no references to email or legacy modules).
 */
class CustomPasswordValidationRules
{
    /**
     * Get the validation rules for new passwords (creation/mandatory change).
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|string>
     */
    public static function rules(): array
    {
        return [
            'required',
            'string',
            IlluminatePasswordRule::min(8) // Minimum 8 chars, mixed case, symbol, number, uncompromised
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised(),
            'confirmed', // Must match password_confirmation
        ];
    }

    /**
     * Get the validation rules for password updates (optional).
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|string>
     */
    public static function updateRules(): array
    {
        return [
            'nullable', // Optional for update
            'string',
            IlluminatePasswordRule::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised(),
            'confirmed',
        ];
    }
}
