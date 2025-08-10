<?php
// English translations for password reset and recovery messages
// Mirrors the structure of passwords_ms.php for maintainability

return [
    /*
    |--------------------------------------------------------------------------
    | Password Reset Language Lines
    |--------------------------------------------------------------------------
    |
    | The following lines are shown to users during various password reset flows.
    |
    */
    'reset'     => 'Your password has been reset.', // Shown after a successful password reset
    'sent'      => 'We have emailed your password reset link.', // Email sent notification
    'throttled' => 'Please wait before retrying.', // Too many attempts message
    'token'     => 'This password reset token is invalid.', // Invalid token error
    'user'      => "We can't find a user with that email address.", // User not found error
];

// Notes:
// - Update these keys if any changes are made in the Malay version (passwords_ms.php).
// - Maintain parity for easy management of both language packs.
