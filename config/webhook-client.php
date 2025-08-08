<?php

return [
    /*
     * Configuration for storing and processing webhook calls.
     * This config is designed to match the database structure defined in
     * 2024_05_27_100007_create_webhook_calls_table.php and can be adapted
     * for custom logic or used with a package such as Spatie Webhook Client.
     */
    'storage_table' => 'webhook_calls',

    /*
     * The amount of days after which records should be deleted (retention policy).
     * Set to null for no automatic deletion.
     */
    'delete_after_days' => 30,

    /*
     * If you want to validate incoming webhooks, specify a secret here.
     * Example: 'signing_secret' => env('WEBHOOK_SIGNING_SECRET')
     */
    'signing_secret' => env('WEBHOOK_SIGNING_SECRET'),

    /*
     * The name of the header containing the signature, if used.
     */
    'signature_header_name' => 'X-Hub-Signature',

    /*
     * Class responsible for validating the signature, if required.
     * This should implement a method: static function isValid($signature, $payload, $secret): bool
     */
    'signature_validator' => \App\Validator\CustomSignatureValidator::class,

    /*
     * Which headers to store. Use '*' to store all.
     */
    'store_headers' => '*',

    /*
     * The job or event to dispatch for further processing.
     * Set to null if not used.
     */
    'process_webhook_job' => null,

    /*
     * Add unique token to the route for additional security (optional).
     */
    'add_unique_token_to_route_name' => false,
];
