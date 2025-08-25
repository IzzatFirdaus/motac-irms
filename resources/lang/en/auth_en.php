<?php
// English translations for authentication and login errors
// Mirrors the structure and intent of auth_ms.php for consistency

return [
    // === Login Error Messages ===
    'failed'   => 'These credentials do not match our records.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
];

// Notes:
// - 'failed': For invalid login credentials.
// - 'password': For incorrect password input.
// - 'throttle': For login attempt rate limiting.
// - Keep these keys in sync with the Malay (auth_ms.php) version for easy translation management.
