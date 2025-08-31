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
        // During automated tests we accept a simpler rule to keep tests predictable.
        if (app()->environment('testing')) {
            return [
                'required',
                'string',
                'min:8',
                'confirmed',
            ];
        }

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
        if (app()->environment('testing')) {
            return [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ];
        }

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
