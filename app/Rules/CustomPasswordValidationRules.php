<?php

namespace App\Rules;

use Illuminate\Validation\Rules\Password as IlluminatePasswordRule;

class CustomPasswordValidationRules
{
  /**
   * Get the validation rules used to validate passwords for creation or mandatory changes.
   *
   * @return array<int, \Illuminate\Contracts\Validation\Rule|string>
   */
  public static function rules(): array
  {
    return [
      'required',
      'string',
      IlluminatePasswordRule::min(8) // Default minimum length from Laravel
        ->letters() // Require at least one letter
        ->mixedCase() // Require at least one uppercase and one lowercase letter
        ->numbers() // Require at least one number
        ->symbols() // Require at least one symbol
        ->uncompromised(), // Check against haveibeenpwned.com database
      'confirmed', // Requires a 'password_confirmation' field
    ];
  }

  /**
   * Get the validation rules used to validate passwords during an update
   * where changing the password might be optional.
   *
   * @return array<int, \Illuminate\Contracts\Validation\Rule|string>
   */
  public static function updateRules(): array
  {
    return [
      'nullable', // Allows password to be optional during updates
      'string',
      IlluminatePasswordRule::min(8)
        ->letters() // Require at least one letter
        ->mixedCase() // Require at least one uppercase and one lowercase letter
        ->numbers() // Require at least one number
        ->symbols() // Require at least one symbol
        ->uncompromised(),
      'confirmed',
    ];
  }
}
