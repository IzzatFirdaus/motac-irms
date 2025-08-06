<?php

namespace App\Actions\Fortify;

/**
 * Legacy trait for password rules.
 * Kept for backward compatibility but no longer used by Fortify actions.
 */
trait PasswordValidationRules
{
    /**
     * @deprecated Use \App\Rules\CustomPasswordValidationRules::rules() instead.
     */
    protected function passwordRules(): array
    {
        return \App\Rules\CustomPasswordValidationRules::rules();
    }
}
